<?php

namespace Aco\Handler;

use Rhumsaa\Uuid\Uuid;
use Aco\Handler;
use Aco\Command\AddArticleCollectionCommand;
use Aco\ArticleCollectionRepository;
use Aco\Article;
use Aco\ArticleCollection;
use Aco\Url;
use Aco\UrlFetcher;

class AddArticleCollectionHandler implements Handler
{
    /**
     * @var ArticleCollectionRepository
     */
    private $articleCollectionRepository;

    public function __construct(ArticleCollectionRepository $articleCollectionRepository,
                                UrlFetcher $urlFetcher)
    {
        $this->articleCollectionRepository = $articleCollectionRepository;
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
        $articleCollection = new ArticleCollection(
            Uuid::uuid4(),
            new \DateTime(),
            $command->title,
            $command->description,
            $articles,
            $command->tags
        );
        $this->articleCollectionRepository->add($articleCollection);

        return $articleCollection->getUuid()->toString();
    }
}
