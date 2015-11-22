<?php

namespace Aco\Infra;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Aco\App\UrlFetcher;
use Aco\Domain\Aco\Url;
use Aco\App\Exception\CannotFetchUrlException;

class GuzzleUrlFetcher implements UrlFetcher
{
    public function fetch(Url $url)
    {
        try {
            $client = new Client();
            $response = $client->get($url->getUrl());
            $body = (string) $response->getBody();

            return $body;
        } catch (ConnectException $e) {
            throw new CannotFetchUrlException();
        }
    }
}
