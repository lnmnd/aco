<?php

namespace spec\Aco\App\Handler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Aco\Domain\Aco\ArticleRepo;
use Aco\Domain\Aco\Article;
use Aco\App\Command\DeleteArticleCommand;
use Rhumsaa\Uuid\Uuid;

class DeleteArticleHandlerSpec extends ObjectBehavior
{
    public function let(ArticleRepo $articleRepo)
    {
        $this->beConstructedWith($articleRepo);
    }

    public function it_removes_articles(ArticleRepo $articleRepo, DeleteArticleCommand $command, Article $article)
    {
        $command->uuid = 'FE755198-9089-11E5-999C-0341358CC448';
        $articleRepo->find(Argument::type('Rhumsaa\Uuid\Uuid'))->willReturn($article);
        $articleRepo->remove($article)->shouldBeCalled();
        $this->handle($command);
    }
}
