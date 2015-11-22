<?php

namespace Aco\App;

use Aco\Domain\Aco\Url;

interface UrlFetcher
{
    /**
     * @param Url $url
     *
     * @return string
     *
     * @throws CannotFetchUrlException
     */
    public function fetch(Url $url);
}
