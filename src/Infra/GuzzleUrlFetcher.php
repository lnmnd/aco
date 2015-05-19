<?php

namespace Infra;

use GuzzleHttp\Client;
use Aco\UrlFetcher;
use Aco\Url;

class GuzzleUrlFetcher implements UrlFetcher
{
	public function fetch(Url $url)
	{
		$client = new Client();
		$response = $client->get($url->getUrl());
		$body = (string)$response->getBody();
		return $body;
	}
} 