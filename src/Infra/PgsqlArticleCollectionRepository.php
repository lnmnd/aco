<?php

namespace Infra;

use \Aco\ArticleCollectionRepository;
use \Aco\ArticleCollection;
use \Aco\Article;
use \Aco\Url;
use \Aco\Exception\DoesNotExistException;
use \Rhumsaa\Uuid\Uuid;

class PgsqlArticleCollectionRepository implements ArticleCollectionRepository
{
    private $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

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

        if (!$acoArr) {
            throw new DoesNotExistException();
        }

        $acoArr['date'] = \DateTime::createFromFormat(ArticleCollection::DATE_FORMAT, $acoArr['date']);

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
}
