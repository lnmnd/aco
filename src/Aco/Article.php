<?php

namespace Aco;

class Article
{
	private $url;
	private $originalContent;
	
	public function __construct(UrlFetcher $urlFetcher, Url $url)
	{
		$this->url = $url;
		$this->originalContent = $urlFetcher->fetch($url);
	}
	
	public function getUrl()
	{
		return $this->url;
	}
	
	public function getOriginalContent()
	{
		return $this->originalContent;
	}
}