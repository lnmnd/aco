<?php

namespace Aco\Handler;

use Rhumsaa\Uuid\Uuid;
use Aco\Handler;
use Aco\Command\RemoveArticleCollectionCommand;
use Aco\ArticleCollectionRepository;

class RemoveArticleCollectionHandler implements Handler
{
	/**
	 * @var ArticleCollectionRepository
	 */
	private $articleCollectionRepository;
	
	public function __construct(ArticleCollectionRepository $articleCollectionRepository)
	{
		$this->articleCollectionRepository = $articleCollectionRepository;
	}
	
	/**
	 * @see \Aco\Handler::handle()
	 * @param RemoveArticleCollectionCommand $command
	 */
	public function handle($command)
	{
		$uuid = Uuid::fromString($command->uuid);
		$this->articleCollectionRepository->remove($uuid);
	}
}