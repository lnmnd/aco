<?php

namespace Aco\Handler;

use Aco\Handler;
use Aco\Command\AddArticleCollectionCommand;
use Aco\ArticleCollectionRepository;
use Aco\ArticleCollection;
use Aco\ArticleFactory;
use Aco\Article;
use Aco\Url;

class AddArticleCollectionHandler implements Handler
{
	/**
	 * @var ArticleCollectionRepository
	 */
	private $articleCollectionRepository;
	/**
	 * @var ArticleFactory
	 */
	private $articleFactory;
	
	public function __construct(ArticleCollectionRepository $articleCollectionRepository, ArticleFactory $articleFactory)
	{
		$this->articleCollectionRepository = $articleCollectionRepository;
		$this->articleFactory = $articleFactory;
	}
	
	/**
	 * @see \Aco\Handler::handle()
	 * @param AddArticleCollectionCommand $command
	 */
	public function handle($command)
	{
		$articleCollection = new ArticleCollection($command->title, $command->description);
		foreach ($command->urls as $url) {
			$article = $this->articleFactory->make(new Url($url));
			$articleCollection->addArticle($article);
		}
		$this->articleCollectionRepository->add($articleCollection);
		
		return $articleCollection->getUuid();
	}
}