<?php

namespace spec\Aco\App\Handler;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Aco\Domain\Aco\ArticleRepo;
use Aco\App\UrlFetcher;
use Aco\App\Command\AddArticleCommand;

class AddArticleHandlerSpec extends ObjectBehavior
{
    public function let(ArticleRepo $articleRepo, UrlFetcher $urlFetcher)
    {
        $this->beConstructedWith($articleRepo, $urlFetcher);
    }

    public function it_adds_articles(ArticleRepo $articleRepo, UrlFetcher $urlFetcher, AddArticleCommand $command)
    {
        $command->title = 'title';
        $command->url = 'http://url';
        $urlFetcher->fetch(Argument::type('Aco\Domain\Aco\Url'))->willReturn('content');
        $articleRepo->store(Argument::type('Aco\Domain\Aco\Article'))->shouldBeCalled();
        $this->handle($command)->shouldBeUuidString();
    }

    public function getMatchers()
    {
        return [
                'beUuidString' => function ($re) {
                    return strlen($re) === 36;
                },
                    ];
    }
}
