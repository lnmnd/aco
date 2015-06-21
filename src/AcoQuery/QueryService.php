<?php

namespace AcoQuery;

interface QueryService
{
    /**
     * Returns all article collections, with general info,
     * ordered by date (newer first).
     *
     * @return ListAco[]
     */
    public function getArticleCollections();

    /**
     * Returns an article collection
     *
     * @param  string $uuid
     * @return FullAco
     * @throws AcoQuery\Exception\ArticleCollectionNotFoundException
     */
    public function getArticleCollection($uuid);

    /**
     * return string[]
     */
    public function getTags();

    /**
     * @param string $tag
     * @return ListAco[]
     */
    public function getTagsArticleCollections($tag);
}
