<?php

namespace Aco;

interface UrlFetcher
{
    /**
     * @param  Url    $url
     * @return string
     */
    public function fetch(Url $url);
}
