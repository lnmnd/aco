<?php

namespace Aco;

use Rhumsaa\Uuid\Uuid;

class ArticleCollection
{
	private $uuid;
	
	public function __construct()
	{
		$this->uuid = Uuid::uuid4();
	}
	
	public function getUuid()
	{
		return $this->uuid;
	}
	
	public function equals(ArticleCollection $x)
	{
		return $this->uuid === $x->getUuid();
	}
}