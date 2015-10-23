<?php

namespace Aco\Handler;

use Aco\Handler;
use Aco\Command\AddArticleCollectionCommand;
use Aco\ArticleCollectionRepository;
use Aco\ArticleCollectionFactory;
use Aco\Article;
use Aco\Url;
use Aco\UrlFetcher;

class AddArticleCollectionHandler implements Handler
{
    /**
     * @var ArticleCollectionRepository
     */
    private $articleCollectionRepository;
    private $articleCollectionFactory;

    public function __construct(ArticleCollectionRepository $articleCollectionRepository,
                                ArticleCollectionFactory $articleCollectionFactory,
                                UrlFetcher $urlFetcher)
    {
        $this->articleCollectionRepository = $articleCollectionRepository;
        $this->articleCollectionFactory = $articleCollectionFactory;
        $this->urlFetcher = $urlFetcher;
    }

    /**
     * @see \Aco\Handler::handle()
     *
     * @param AddArticleCollectionCommand $command
     */
    public function handle($command)
    {
        $articles = [];
        foreach ($command->urls as $url) {
            $url = new Url($url);
            $originalContent = $this->urlFetcher->fetch($url);
            $title = \Aco\extractTitle($originalContent);
            $content = \Aco\extractContent($originalContent);
            $articles[] = new Article(
                $url,
                $title,
                $content,
                $originalContent
            );
        }
        $articleCollection = $this->articleCollectionFactory->make(
            $command->title,
            $command->description,
            $articles,
            $command->tags
        );
        $this->articleCollectionRepository->add($articleCollection);

        return $articleCollection->getUuid()->toString();
    }
}
