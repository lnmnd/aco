<?php

namespace AcoQuery;

class FullArticle
{
    public $uuid;
    public $url;
    public $title;
    public $created_at;
    public $original_content;
    public $content;

    public static function build($uuid, $url, $title, $created_at, $original_content, $content)
    {
        $obj = new self();
        $obj->uuid = $uuid;
        $obj->url = $url;
        $obj->title = $title;
        $obj->created_at = $created_at;
        $obj->original_content = $original_content;
        $obj->content = $content;

        return $obj;
    }
}
