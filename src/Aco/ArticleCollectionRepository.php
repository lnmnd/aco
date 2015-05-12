<?php

namespace Aco;

interface ArticleCollectionRepository
{
	/**
	 * @param ArticleCollection $articleCollection
	 * @return void
	 */
	public function add (ArticleCollection $articleCollection);	
}