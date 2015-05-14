<?php

namespace Infra;

use Aco\ArticleCollectionRepository;
use Aco\ArticleCollection;
use AcoQuery\QueryService;
use AcoQuery\ListAco;
use AcoQuery\Exception\ArticleCollectionNotFoundException;
use AcoQuery\FullAco;
use AcoQuery\AcoQuery;

class SerializedArticleCollectionRepository implements ArticleCollectionRepository, QueryService
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
		$acos = $this->loadAcos();
		$acos[] = $articleCollection;
		$this->saveAcos($acos);
	}

	public function getArticleCollections()
	{
		$acos = $this->loadAcos();
		$lacos = [];
		/**
		 * @var $aco ArticleCollection
		 */
		foreach ($acos as $aco) {
			$lacos[] = new ListAco(
					$aco->getUuid()->toString(),
					$aco->getDate(),
					$aco->getTitle(),
					$aco->getDescription());
		}		
		usort($lacos, function (ListAco $a, ListAco $b) {
			return $a->date < $b->date;
		});
		return $lacos;
	}
	
	public function getArticleCollection($uuid)
	{
		$acos = $this->loadAcos();
		/**
		 * @var $foundAco ArticleCollection 
		 */
		$foundAco = array_reduce($acos, function ($foundAco, ArticleCollection $aco) use ($uuid) {
			if ($aco->getUuid()->toString() === $uuid) {
				return $aco;
			} else {
				return $foundAco;
			}
		}, false);
		if ($foundAco) {
			return new FullAco(
				$foundAco->getUuid()->toString(),
				$foundAco->getDate(),
				$foundAco->getTitle()
			);
		} else {
			throw new ArticleCollectionNotFoundException();
		}
	}
	
	private function loadAcos()
	{
		if ($this->fileInitialized()) {
			$contents = file_get_contents($this->file);
			$acos = unserialize($contents);
		} else {
			$acos = [];
		}
		return $acos;
	}
	
	private function saveAcos($acos)
	{
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