<?php

namespace AcoQuery;

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
}
