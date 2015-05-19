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
		return $response->getBody();
	}
} 