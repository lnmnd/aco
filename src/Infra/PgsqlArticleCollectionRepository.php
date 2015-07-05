<?php

namespace Infra;

use \Aco\ArticleCollectionRepository;
use \Aco\ArticleCollection;
use \Aco\Article;
use \Aco\Url;
use \Aco\Exception\DoesNotExistException;
use \AcoQuery\QueryService;
use \AcoQuery\ListAco;
use \AcoQuery\FullAco;
use \AcoQuery\FullArticle;
use \Rhumsaa\Uuid\Uuid;

class PgsqlArticleCollectionRepository implements ArticleCollectionRepository, QueryService
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // ArticleCollectionRepository
    
    public function add(ArticleCollection $articleCollection)
    {
        $uuid = $articleCollection->getUuid();

        $st = $this->pdo->prepare('insert into article_collection values (:uuid, :date, :title, :description)');
        $st->bindValue('uuid', $uuid);
        $st->bindValue('date', $articleCollection->getDate()->format(ArticleCollection::DATE_FORMAT));
        $st->bindValue('title', $articleCollection->getTitle());
        $st->bindValue('description', $articleCollection->getDescription());
        $st->execute();
        
        $st = $this->pdo->prepare('insert into article (aco_uuid, url, title, content, original_content) values (:aco_uuid, :url, :title, :content, :original_content)');

        foreach ($articleCollection->getArticles() as $art) {
            $st->bindValue('aco_uuid', $uuid);
            $st->bindValue('url', $art->getUrl()->getUrl());
            $st->bindValue('title', $art->getTitle());
            $st->bindValue('content', $art->getContent());
            $st->bindValue('original_content', $art->getOriginalContent());
            $st->execute();
        }

        $stSel = $this->pdo->prepare('select id from tag where tag=:tag');
        $stIns = $this->pdo->prepare('insert into tag (tag) values (:tag) returning id');
        $stLink = $this->pdo->prepare('insert into aco_tag (aco_uuid, tag_id) values (:aco_uuid, :tag_id)');
        foreach ($articleCollection->getTags() as $tag) {
            $stSel->bindValue('tag', $tag);
            $stSel->execute();
            $tagRes = $stSel->fetch();

            if (!$tagRes) {
                $stIns->bindValue('tag', $tag);
                $stIns->execute();
                $tagRes = $stIns->fetch();
            }

            $tagId = $tagRes['id'];
            $stLink->bindValue('aco_uuid', $uuid);
            $stLink->bindValue('tag_id', $tagId);
            $stLink->execute();
        }
    }

    public function get(Uuid $uuid)
    {
        $uuidStr = $uuid->toString();

        $acoSt = $this->pdo->prepare('select date, title, description from article_collection where uuid=:uuid');
        $acoSt->bindValue('uuid', $uuidStr);
        $acoSt->execute();
        $acoArr = $acoSt->fetch();
        $acoArr['date'] = \DateTime::createFromFormat(ArticleCollection::DATE_FORMAT, $acoArr['date']);
        if (!$acoArr) {
            throw new DoesNotExistException();
        }

        $articles = [];
        $artSt = $this->pdo->prepare('select url, title, content, original_content from article where aco_uuid=:uuid');
        $artSt->bindValue('uuid', $uuidStr);
        $artSt->execute();
        while ($artArr = $artSt->fetch()) {
            $articles[] = new Article(new Url($artArr['url']), $artArr['original_content']);
        }
        $articles[] = new Article(new Url('http://localhost'), 'content');//DB

        return new ArticleCollection(
            $uuid,
            $acoArr['date'],
            $acoArr['title'],
            $acoArr['description'],
            $articles
        );
    }

    public function remove(ArticleCollection $articleCollection)
    {
        $uuid = $articleCollection->getUuid();

        $st = $this->pdo->prepare('delete from article_collection where uuid=:uuid');
        $st->bindValue('uuid', $uuid);
        $st->execute();
    }

    // QueryService
    
    public function getArticleCollections($offset = 0, $limit = 0)
    {
        $sql = 'select uuid, date, title, description from article_collection order by date desc offset :offset';
        if ($limit === 0) {
            $st = $this->pdo->prepare($sql);
        } else {
            $st = $this->pdo->prepare($sql.' limit :limit');
            $st->bindValue('limit', $limit);
        }
        $st->bindValue('offset', $offset);
        $st->execute();

        $acos = [];
        while ($x = $st->fetch()) {
            $x['date'] = \DateTime::createFromFormat(ArticleCollection::DATE_FORMAT, $x['date']);
            $acos[] = ListAco::fromArray($x);
        }
        return $acos;
    }

    public function getArticleCollection($uuid)
    {
        $st = $this->pdo->prepare(
            'select uuid, date, title, description from article_collection where uuid=:uuid'
        );
        $st->bindValue('uuid', $uuid);
        $st->execute();
        $acoArr = $st->fetch();
        $acoArr['date'] = \DateTime::createFromFormat(ArticleCollection::DATE_FORMAT, $acoArr['date']);

        // articles
        $st = $this->pdo->prepare(
            'select url, title, content, original_content from article where aco_uuid=:uuid'
        );
        $st->bindValue('uuid', $uuid);
        $st->execute();

        $acoArr['articles'] = [];
        while ($x = $st->fetch()) {
            $acoArr['articles'][] =  FullArticle::fromArray($x);
        }

        // tags
        $st = $this->pdo->prepare(
            'select tag from tag inner join aco_tag on tag.id=aco_tag.tag_id where aco_uuid=:uuid'
        );
        $st->bindValue('uuid', $uuid);
        $st->execute();

        $acoArr['tags'] = [];
        while ($x = $st->fetch()) {
            $acoArr['tags'][] = $x['tag'];
        }

        return FullAco::fromArray($acoArr);
    }

    public function getTags($offset = 0, $limit = 0)
    {
        $sql = 'select tag from tag order by tag offset :offset';
        if ($limit === 0) {
            $st = $this->pdo->prepare($sql);
        } else {
            $st = $this->pdo->prepare($sql.' limit :limit');
            $st->bindValue('limit', $limit);
        }
        $st->bindValue('offset', $offset);
        $st->execute();
        return array_map(function ($x) {
            return $x['tag'];
        }, $st->fetchAll());
    }

    public function getTagsArticleCollections($tag, $offset = 0, $limit = 0)
    {
        $sql = 'select id from tag where tag=:tag offset :offset';
        if ($limit === 0) {
            $st = $this->pdo->prepare($sql);
        } else {
            $st = $this->pdo->prepare($sql.' limit :limit');
            $st->bindValue('limit', $limit);
        }
        $st->bindValue('offset', $offset);
        $st->bindValue('tag', $tag);
        $st->execute();
        $row = $st->fetch();
        $tagId = $row['id'];
        
        $st = $this->pdo->prepare(
            'select uuid, date, title, description from article_collection inner join aco_tag on uuid=aco_uuid where tag_id=:tag_id'
        );
        $st->bindValue('tag_id', $tagId);
        $st->execute();

        $acos = [];
        while ($x = $st->fetch()) {
            $x['date'] = \DateTime::createFromFormat(ArticleCollection::DATE_FORMAT, $x['date']);
            $acos[] = ListAco::fromArray($x);
        }
        return $acos;
    }

    public function getArticles($offset = 0, $limit = 0)
    {
        $sql = 'select url, title, content, original_content from article offset :offset';
        if ($limit === 0) {
            $st = $this->pdo->prepare($sql);
        } else {
            $st = $this->pdo->prepare($sql.' limit :limit');
            $st->bindValue('limit', $limit);
        }
        $st->bindValue('offset', $offset);
        $st->execute();

        $arts = [];
        while ($x = $st->fetch()) {
            $arts[] = FullArticle::fromArray($x);
        }
        return $arts;
    }
}
