<?php

namespace Aco\Command;

class AddArticleCollectionCommand
{
	public $title;
	public $description;
	public $urls;
	
	public function __construct($title, $description, $urls)
	{
		$this->title = $title;
		$this->description = $description;
		$this->urls = $urls;
	}
}