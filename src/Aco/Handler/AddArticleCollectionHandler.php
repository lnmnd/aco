<?php

namespace Aco\Handler;

use Aco\Handler;
use Aco\Command\AddArticleCollectionCommand;
use Aco\DateTimeGetter;
use Aco\ArticleCollectionRepository;
use Aco\ArticleCollection;

class AddArticleCollectionHandler implements Handler
{
	/**
	 * @var ArticleCollectionRepository
	 */
	private $articleCollectionRepository;
	/**
	 * @var DateTimeGetter
	 */
	private $dateTimeGetter;
	
	public function __construct(ArticleCollectionRepository $articleCollectionRepository, DateTimeGetter $dateTimeGetter)
	{
		$this->articleCollectionRepository = $articleCollectionRepository;
		$this->dateTimeGetter = $dateTimeGetter;
	}
	
	/**
	 * @see \Aco\Handler::handle()
	 * @param AddArticleCollectionCommand $command
	 */
	public function handle($command)
	{
		echo "handle ".$command->title;
		$articleCollection = new ArticleCollection();
		// just call
		$this->dateTimeGetter->now();
		$this->articleCollectionRepository->add($articleCollection);
		
		return $articleCollection->getUuid();
	}
}