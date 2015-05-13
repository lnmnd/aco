<?php

namespace FakeInfra;

use Aco\UrlFetcher;
use Aco\Url;

class FakeUrlFetcher implements UrlFetcher
{
	public $urls = [];

	public function fetch(Url $url)
	{
		$this->urls[] = $url;
		return 'fake content';
	}
}