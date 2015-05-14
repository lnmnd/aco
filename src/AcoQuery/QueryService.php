<?php

namespace AcoQuery;

interface QueryService
{
	/**
	 * Returns all article collections, with general info,
	 * ordered by date (newer first). 
	 * 
	 * @return ListAco[]
	 */
	public function getArticleCollections();
}