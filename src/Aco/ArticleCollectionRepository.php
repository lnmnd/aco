<?php

namespace Aco;

use Rhumsaa\Uuid\Uuid;
interface ArticleCollectionRepository
{
	/**
	 * @param ArticleCollection $articleCollection
	 * @return void
	 */
	public function add (ArticleCollection $articleCollection);	
	
	/**
	 * @param Uuid $uuid
	 * @return ArticleCollection
	 */
	public function get(Uuid $uuid);
}