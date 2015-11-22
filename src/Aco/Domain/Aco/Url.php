<?php

namespace Aco\Domain\Aco;

class Url
{
    private $url;

    public function __construct($url)
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            throw new \InvalidArgumentException();
        }

        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function isEqual(Url $url)
    {
        return $this->getUrl() === $url->getUrl();
    }
}
