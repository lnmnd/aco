<?php

namespace FakeInfra;

use Aco\ArticleCollectionRepository;
use Aco\ArticleCollection;

class FakeArticleCollectionRepository implements ArticleCollectionRepository
{
	public $called = false;
	public $articleCollections = [];

	public function add(ArticleCollection $articleCollection)
	{
		$this->called = true;
		$this->articleCollections[] = $articleCollection;
	}
}