<?php

namespace AcoQuery;

class FullAco
{
	public $uuid;
	public $date;
	public $title;
	public $description;
	
	public function __construct($uuid, $date, $title, $description)
	{
		$this->uuid = $uuid;
		$this->date = $date;
		$this->title = $title;
		$this->description = $description;
	}
}