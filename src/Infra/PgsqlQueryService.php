<?php

namespace Infra;

use \AcoQuery\QueryService;
use \AcoQuery\ListAco;
use \AcoQuery\FullAco;
use \AcoQuery\FullArticle;
use \Aco\ArticleCollection;
use \AcoQuery\Exception\ArticleCollectionNotFoundException;

class PgsqlQueryService implements QueryService
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

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

        if (!$acoArr) {
            throw new ArticleCollectionNotFoundException();
        }

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
