<?php

namespace Aco\App\Handler;

use Aco\Domain\Aco\ArticleRepo;
use Aco\App\Command\RemoveArticleCommand;

class RemoveArticleHandler
{
    private $acoRepo;

    public function __construct(ArticleRepo $acoRepo)
    {
        $this->acoRepo = $acoRepo;
    }

    public function handle(RemoveArticleCommand $command)
    {
        $article = $this->acoRepo->find($command->uuid);
        $article->remove();
        $this->acoRepo->store($article);
    }
}
