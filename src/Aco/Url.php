<?php

namespace Aco;

use Aco\Exception\BadUrl;


class Url
{
	private $url;
	
	public function __construct($url)
	{
		if (filter_var($url, FILTER_VALIDATE_URL)) {
			$this->url = $url;
		} else {
			throw new BadUrl();
		}
	}
	
	public function getUrl()
	{
		return $this->url;	
	}
}