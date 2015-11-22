<?php

namespace spec\Aco\App\Handler;

use PhpSpec\ObjectBehavior;
use Aco\Domain\Aco\Article;
use Aco\Domain\Aco\ArticleRepo;
use Aco\App\Command\RemoveArticleCommand;
use Rhumsaa\Uuid\Uuid;

class RemoveArticleHandlerSpec extends ObjectBehavior
{
    public function let(ArticleRepo $articleRepo)
    {
        $this->beConstructedWith($articleRepo);
    }

    public function it_removes_articles(ArticleRepo $articleRepo, RemoveArticleCommand $command, Article $article)
    {
        $command->uuid = Uuid::fromString('FE755198-9089-11E5-999C-0341358CC448');
        $articleRepo->find($command->uuid)->willReturn($article);
        $article->remove()->shouldBeCalled();
        $articleRepo->store($article)->shouldBeCalled();
        $this->handle($command);
    }
}
