<?php

namespace AcoQuery;

use Aco\Domain\Aco\Exception\ArticleDoesNotExistException;

interface QueryService
{
    /**
     * Returns all the articles.
     *
     * @param int $offset
     * @param int $limit
     *
     * @return ListArticle[]
     */
    public function findArticles($offset = 0, $limit = 0);

    /**
     * @param string $uuid
     *
     * @return FullArticle
     *
     * @throws ArticleDoesNotExistException
     */
    public function findArticle($uuid);
}
