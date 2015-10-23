<?php

namespace AcoQuery;

class FullArticle
{
    public $url;
    public $title;
    public $original_content;
    public $content;

    public function __construct($url, $title, $original_content, $content)
    {
        $this->url = $url;
        $this->title = $title;
        $this->original_content = $original_content;
        $this->content = $content;
    }

    public static function fromArray($x)
    {
        return new self($x['url'], $x['title'], $x['original_content'], $x['content']);
    }
}
