<?php

namespace Aco\App\Handler;

use Aco\Domain\Aco\ArticleRepo;
use Aco\App\UrlFetcher;
use Aco\App\Command\AddArticleCommand;
use Aco\Domain\Aco\Url;
use Aco\Domain\Aco\Article;
use Aco\Domain\Aco\ArticleSource;
use Aco\Domain\Aco\ContentExtractor;
use Rhumsaa\Uuid\Uuid;

class AddArticleHandler
{
    private $articleRepo;
    private $urlFetcher;

    public function __construct(ArticleRepo $articleRepo, UrlFetcher $urlFetcher)
    {
        $this->articleRepo = $articleRepo;
        $this->urlFetcher = $urlFetcher;
    }

    public function __invoke(AddArticleCommand $command)
    {
        $title = $command->title;
        $url = new Url($command->url);
        $uuid = Uuid::uuid4();
        $date = new \DateTime();
        $originalContent = $this->urlFetcher->fetch($url);

        $articleSource = new ArticleSource($url, $originalContent);
        $content = ContentExtractor::extractContent($originalContent);
        $article = new Article($uuid, $title, $date, $articleSource, $content);
        $this->articleRepo->store($article);

        return $uuid->toString();
    }
}
