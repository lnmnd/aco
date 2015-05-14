<?php

namespace AcoQuery;

class FullAco
{
	public $uuid;
	public $date;
	public $title;
	public $description;
	public $articles;
	
	public function __construct($uuid, $date, $title, $description, $articles)
	{
		$this->uuid = $uuid;
		$this->date = $date;
		$this->title = $title;
		$this->description = $description;
		$this->articles = $articles;
	}
}