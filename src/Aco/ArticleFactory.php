<?php

namespace Aco;

class ArticleFactory
{
    /**
     * @var UrlFetcher
     */
    private $urlFetcher;

    public function __construct(UrlFetcher $urlFetcher)
    {
        $this->urlFetcher = $urlFetcher;
    }

    /**
     * @return Article
     */
    public function make(Url $url)
    {
        $originalContent = $this->urlFetcher->fetch($url);
        $title = extractTitle($originalContent);

        return new Article($url, $title, $originalContent);
    }
}
