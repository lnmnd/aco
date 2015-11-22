<?php

namespace Aco\App\Handler;

use Aco\Domain\Aco\ArticleRepo;
use Aco\App\Command\DeleteArticleCommand;

class DeleteArticleHandler
{
    private $acoRepo;

    public function __construct(ArticleRepo $acoRepo)
    {
        $this->acoRepo = $acoRepo;
    }

    public function handle(DeleteArticleCommand $command)
    {
        $article = $this->acoRepo->find($command->uuid);
        $this->acoRepo->remove($article);
    }
}
