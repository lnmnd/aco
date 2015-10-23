<?php

namespace Aco;

class Article
{
    private $url;
    private $originalContent;
    private $title;
    private $content;

    public function __construct(Url $url, $title, $originalContent)
    {
        $this->url = $url;
        $this->originalContent = $originalContent;
        $this->title = $title;
        $this->content = extractContent($this->originalContent);
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getOriginalContent()
    {
        return $this->originalContent;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getContent()
    {
        return $this->content;
    }
}
