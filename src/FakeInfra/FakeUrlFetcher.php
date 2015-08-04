<?php

namespace FakeInfra;

use Aco\UrlFetcher;
use Aco\Url;
use Aco\Exception\CannotFetchUrlException;

class FakeUrlFetcher implements UrlFetcher
{
    public $urls = [];
    public $callUrls = [];

    public function fetch(Url $url)
    {
        $this->callUrls[] = $url->getUrl();
        if (!array_key_exists($url->getUrl(), $this->urls)) {
            throw new CannotFetchUrlException();
        }

        return $this->urls[$url->getUrl()];
    }
}
