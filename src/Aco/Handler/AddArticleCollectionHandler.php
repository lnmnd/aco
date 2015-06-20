<?php

namespace Aco\Handler;

use Aco\Handler;
use Aco\Command\AddArticleCollectionCommand;
use Aco\ArticleCollectionRepository;
use Aco\ArticleCollectionFactory;
use Aco\ArticleCollection;
use Aco\ArticleFactory;
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

        private $articleCollectionFactory;

    public function __construct(ArticleCollectionRepository $articleCollectionRepository, ArticleFactory $articleFactory,
                ArticleCollectionFactory $articleCollectionFactory)
    {
        $this->articleCollectionRepository = $articleCollectionRepository;
        $this->articleFactory = $articleFactory;
                $this->articleCollectionFactory = $articleCollectionFactory;
    }

    /**
     * @see \Aco\Handler::handle()
     * @param AddArticleCollectionCommand $command
     */
    public function handle($command)
    {
        $articles = [];
        foreach ($command->urls as $url) {
            $articles[] = $this->articleFactory->make(new Url($url));
        }
        $articleCollection = $this->articleCollectionFactory->make(
                        $command->title, $command->description, $articles, $command->tags);
        $this->articleCollectionRepository->add($articleCollection);

        return $articleCollection->getUuid()->toString();
    }
}
