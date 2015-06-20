<?php

namespace WebApp;

use AcoQuery\QueryService;

class FeedController
{
    private $queryService;

    public function __construct(QueryService $queryService)
    {
        $this->queryService = $queryService;
    }

    public function getArticleCollections()
    {
        header('Content-Type: application/atom+xml');
        $xml = new \SimpleXMLElement('<xml/>');
        $feed = $xml->addChild('feed');
        $feed->addAttribute('xmlns', 'http://www.w3.org/2005/Atom');
        $feed->addChild('title', 'Article Collections');
            
        $articles = $this->queryService->getArticles();
        foreach ($articles as $a) {
            $entry = $feed->addChild('entry');
            $entry->addChild('title', $a->title);
            $link = $entry->addChild('link');
            $link->addAttribute('href', $a->url);
            $link->addAttribute('rel', 'alternate');
            $link->addAttribute('type', 'text/html');
            $content = $entry->addChild('content', htmlspecialchars($a->content));
            $content->addAttribute('type', 'html');
        }

        echo $xml->asXML();
    }
}
