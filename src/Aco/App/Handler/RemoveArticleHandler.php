<?php

namespace Aco\App\Handler;

use Aco\Domain\Aco\ArticleRepo;
use Aco\App\Command\RemoveArticleCommand;
use Rhumsaa\Uuid\Uuid;

class RemoveArticleHandler
{
    private $acoRepo;

    public function __construct(ArticleRepo $acoRepo)
    {
        $this->acoRepo = $acoRepo;
    }

    public function __invoke(RemoveArticleCommand $command)
    {
        $uuid = Uuid::fromString($command->uuid);
        $article = $this->acoRepo->find($uuid);
        $article->remove();
        $this->acoRepo->store($article);
    }
}
