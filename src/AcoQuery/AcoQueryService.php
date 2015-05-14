<?php

namespace AcoQuery;

interface AcoQueryService
{
	/**
	 * Returns all article collections, with general info,
	 * ordered by date (newer first). 
	 * 
	 * @return ListAco[]
	 */
	public function getArticleCollections();
}