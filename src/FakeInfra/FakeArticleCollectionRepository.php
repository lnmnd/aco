<?php

namespace FakeInfra;

use Aco\ArticleCollectionRepository;
use Aco\ArticleCollection;

class FakeArticleCollectionRepository implements ArticleCollectionRepository
{
	public $called = false;
	public $articleCollection = null;

	public function add(ArticleCollection $articleCollection)
	{
		$this->called = true;
		$this->articleCollection = $articleCollection;
	}
}