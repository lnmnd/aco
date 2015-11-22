<?php

namespace Aco\Domain\Aco;

class ArticleSource
{
    private $url;
    private $content;

    public function __construct(Url $url, $content)
    {
        $this->url = $url;
        $this->content = $content;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function isEqual(ArticleSource $articleSource)
    {
        return $this->content === $articleSource->getContent() &&
        $this->url->isEqual($articleSource->getUrl());
    }
}
