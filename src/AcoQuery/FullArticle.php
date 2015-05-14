<?php

namespace AcoQuery;

class FullArticle
{
	public $url;
	public $originalContent;
	
	public function __construct($url, $originalContent)
	{
		$this->url = $url;
		$this->originalContent = $originalContent;
	}
}