<?php

namespace Aco\Command;

class AddArticleCollectionCommand
{
    public $title;
    public $description;
    public $urls;
    public $tags;

    public function __construct($title, $description, $urls, $tags = [])
    {
        $this->title = $title;
        $this->description = $description;
        $this->urls = $urls;
        $this->tags = $tags;
    }
}
