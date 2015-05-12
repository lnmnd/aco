<?php

namespace Aco;

class ArticleFactory
{
	/**
	 * @var UrlFetcher
	 */
	private $urlFetcher;
	
	public function __construct(UrlFetcher $urlFetcher)
	{
		$this->urlFetcher = $urlFetcher;
	}
	
	/**
	 * @return Article
	 */
	public function make(Url $url)
	{
		return new Article($this->urlFetcher, $url);
	}
}