<?php

namespace Aco\Domain\Aco;

use Rhumsaa\Uuid\Uuid;

class Article
{
    private $uuid;
    private $title;
    private $createdAt;
    private $articleSource;
    private $content;
    private $removed;

    public function __construct(Uuid $uuid, $title, \DateTime $createdAt, ArticleSource $articleSource, $content)
    {
        $this->uuid = $uuid;
        $this->title = $title;
        $this->createdAt = $createdAt;
        $this->articleSource = $articleSource;
        $this->content = $content;
        $this->removed = false;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getArticleSource()
    {
        return $this->articleSource;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function isEqual(Article $article)
    {
        return $this->uuid->equals($article->getUuid());
    }

    public function isRemoved()
    {
        return $this->removed;
    }

    public function remove()
    {
        $this->removed = true;
    }
}
