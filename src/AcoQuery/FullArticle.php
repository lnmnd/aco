<?php

namespace AcoQuery;

class FullArticle
{
	public $url;
	public $title;	
	public $originalContent;
	public $content;
	
	public function __construct($url, $title, $originalContent,  $content)
	{
		$this->url = $url;
		$this->title = $title;
		$this->originalContent = $originalContent;
		$this->content = $content;
	}
}