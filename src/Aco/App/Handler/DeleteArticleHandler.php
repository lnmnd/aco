<?php

namespace Aco\App\Handler;

use Aco\Domain\Aco\ArticleRepo;
use Aco\App\Command\DeleteArticleCommand;
use Rhumsaa\Uuid\Uuid;

class DeleteArticleHandler
{
    private $acoRepo;

    public function __construct(ArticleRepo $acoRepo)
    {
        $this->acoRepo = $acoRepo;
    }

    public function __invoke(DeleteArticleCommand $command)
    {
        $uuid = Uuid::fromString($command->uuid);
        $article = $this->acoRepo->find($uuid);
        $this->acoRepo->remove($article);
    }
}
