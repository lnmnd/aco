<?php

namespace FakeInfra;

use Aco\UrlFetcher;
use Aco\Url;
use Aco\Exception\CannotFetchUrlException;
use Aco\Exception\Aco\Exception;

class FakeUrlFetcher implements UrlFetcher
{
	public $urls = [];
	public $callurls = [];
	
	public function fetch(Url $url)
	{
		$this->callUrls[] = $url->getUrl();
		if (array_key_exists($url->getUrl(), $this->urls)) {
			return $this->urls[$url->getUrl()];
		} else {
			throw new CannotFetchUrlException();
		}
	}
}