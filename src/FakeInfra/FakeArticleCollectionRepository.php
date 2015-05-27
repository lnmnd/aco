<?php

namespace FakeInfra;

use Aco\ArticleCollectionRepository;
use Aco\ArticleCollection;
use Rhumsaa\Uuid\Uuid;

class FakeArticleCollectionRepository implements ArticleCollectionRepository
{
	public $called = false;
	public $articleCollections = [];

	public function add(ArticleCollection $articleCollection)
	{
		$this->called = true;
		$this->articleCollections[] = $articleCollection;
	}
	
	public function get(Uuid $uuid)
	{
		foreach ($this->articleCollections as $aco) {
			if ($aco->getUuid()->equals($uuid)) {
				return $aco;
			}
		}
	}
	
	public function remove(Uuid $uuid)
	{
		$i = 0;
		foreach ($this->articleCollections as $aco) {
			if ($aco->getUuid()->equals($uuid)) {
				unset($this->articleCollections[$i]);
				$this->articleCollections = array_values($this->articleCollections);
			}
			$i++;
		}
	}
}