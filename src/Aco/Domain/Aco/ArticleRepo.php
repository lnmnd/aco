<?php

namespace Aco\Domain\Aco;

use Rhumsaa\Uuid\Uuid;

interface ArticleRepo
{
    public function remove(Article $article);

    public function find(Uuid $uuid);

    public function store(Article $article);
}
