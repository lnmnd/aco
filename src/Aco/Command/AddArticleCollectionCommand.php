<?php

namespace Aco\Command;

class AddArticleCollectionCommand
{
	public $title;
	public $description;
	
	public function __construct($title, $description)
	{
		$this->title = $title;
		$this->description = $description;
	}
}