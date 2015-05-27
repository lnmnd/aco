<?php

namespace Aco;

use Rhumsaa\Uuid\Uuid;
use Aco\Exception\DoesNotExistException;

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
	 * @throws DoesNotExistException
	 */
	public function get(Uuid $uuid);
	
	/**
	 * @param Uuid $uuid
	 * @return void
	 */
	public function remove(Uuid $uuid);
}