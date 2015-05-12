<?php

namespace Aco\Handler;

use Aco\Handler;
use Aco\Command\AddArticleCollectionCommand;
use Aco\ArticleCollectionRepository;
use Aco\ArticleCollection;
use Aco\Article;
use Aco\Url;

class AddArticleCollectionHandler implements Handler
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
	 * @param AddArticleCollectionCommand $command
	 */
	public function handle($command)
	{
		$articleCollection = new ArticleCollection($command->title, $command->description);
		foreach ($command->urls as $url) {
			$article = new Article(new Url($url));
			$articleCollection->addArticle($article);
		}
		$this->articleCollectionRepository->add($articleCollection);
		
		return $articleCollection->getUuid();
	}
}