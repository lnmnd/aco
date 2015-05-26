<?php

namespace AcoQuery;

class FullArticle
{
	public $url;
	public $originalContent;
	public $content;
	
	public function __construct($url, $originalContent, $content)
	{
		$this->url = $url;
		$this->originalContent = $originalContent;
		$this->content = $content;
	}
}