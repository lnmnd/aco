<?php

namespace Aco;

use Aco\Exception\BadUrlException;

class Url
{
    private $url;

    public function __construct($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new BadUrlException();
        }

        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function equals(Url $url)
    {
        return $this->getUrl() === $url->getUrl();
    }
}
