<?php

namespace Infra;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ConnectException;
use Aco\UrlFetcher;
use Aco\Url;
use Aco\Exception\CannotFetchUrlException;
use Aco\Exception\Aco\Exception;

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
