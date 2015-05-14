<?php

namespace AcoQuery;

class FullAco
{
	public $uuid;
	public $date;
	public $title;
	
	public function __construct($uuid, $date, $title)
	{
		$this->uuid = $uuid;
		$this->date = $date;
		$this->title = $title;
	}
}