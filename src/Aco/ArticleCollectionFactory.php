<?php

namespace Aco;

use Rhumsaa\Uuid\Uuid;

class ArticleCollectionFactory
{
	/**
	 * @return ArticleCollection
	 */
	public function make($title, $description, $articles)
	{
		$uuid = Uuid::uuid4();
		$date = new \DateTime();
		return new ArticleCollection($uuid, $date, $title, $description, $articles);
	}
}