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
     * @return FullArticle[]
     */
    public function getArticles($offset = 0, $limit = 0);
}
