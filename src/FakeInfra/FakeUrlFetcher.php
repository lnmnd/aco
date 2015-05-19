<?php

namespace FakeInfra;

use Aco\UrlFetcher;
use Aco\Url;

class FakeUrlFetcher implements UrlFetcher
{
	public $urls = [];
	public $callurls = [];
	
	public function fetch(Url $url)
	{
		$this->callUrls[] = $url->getUrl();
		return $this->urls[$url->getUrl()];
	}
}