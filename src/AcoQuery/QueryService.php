<?php

namespace AcoQuery;

interface QueryService
{
    /**
     * Returns all article collections, with general info,
     * ordered by date (newer first).
     *
     * @param int $offset
     * @param int $limit
     * @return ListAco[]
     */
    public function getArticleCollections($offset = 0, $limit = 0);

    /**
     * Returns an article collection
     *
     * @param  string $uuid
     * @return FullAco
     * @throws AcoQuery\Exception\ArticleCollectionNotFoundException
     */
    public function getArticleCollection($uuid);

    /**
     * @param int $offset
     * @param int $limit
     * @return string[]
     */
    public function getTags($offset = 0, $limit = 0);

    /**
     * @param string $tag
     * @param int $offset
     * @param int $limit
     * @return ListAco[]
     */
    public function getTagsArticleCollections($tag, $offset = 0, $limit = 0);

    /**
     * Returns all the articles
     *
     * @param int $offset
     * @param int $limit
     * @return FullArticle[]
     */
    public function getArticles($offset = 0, $limit = 0);
}
