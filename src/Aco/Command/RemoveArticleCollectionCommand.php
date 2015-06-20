<?php

namespace Aco\Command;

class RemoveArticleCollectionCommand
{
    public $uuid;

    public function __construct($uuid)
    {
        $this->uuid = $uuid;
    }
}
