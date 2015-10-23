<?php

namespace AcoQuery;

class FullAco
{
    public $uuid;
    public $date;
    public $title;
    public $description;
    public $tags;
    public $articles;

    public function __construct($uuid, $date, $title, $description, $tags, $articles)
    {
        $this->uuid = $uuid;
        $this->date = $date;
        $this->title = $title;
        $this->description = $description;
        $this->tags = $tags;
        $this->articles = $articles;
    }

    public static function fromArray($x)
    {
        return new self($x['uuid'], $x['date'], $x['title'], $x['description'], $x['tags'], $x['articles']);
    }
}
