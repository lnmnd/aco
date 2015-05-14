<?php

namespace Infra;

use Aco\ArticleCollectionRepository;
use Aco\ArticleCollection;

class SerializedArticleCollectionRepository implements ArticleCollectionRepository
{
	private $file;
	
	/**
	 * @param string $file
	 */
	public function __construct($file)
	{
		$this->file = $file;
	}
	
	public function add(ArticleCollection $articleCollection)
	{
		if ($this->fileInitialized()) {
			$contents = file_get_contents($this->file);
			$acos = unserialize($contents);
			$acos[] = $articleCollection;
		} else {
			$acos = [$articleCollection];
		}
		file_put_contents($this->file, serialize($acos));
	}
	
	/**
	 * @return boolean
	 */
	private function fileInitialized()
	{
		return file_exists($this->file);
	}
}