<?php

namespace spec\Aco\Domain\Aco;

use PhpSpec\ObjectBehavior;
use Rhumsaa\Uuid\Uuid;
use Aco\Domain\Aco\ArticleSource;

class ArticleSpec extends ObjectBehavior
{
    public function let(\DateTime $date, ArticleSource $articleSource)
    {
        $uuid = Uuid::fromString('FE755198-9089-11E5-999C-0341358CC448');
        $this->beConstructedWith($uuid, 'title', $date, $articleSource, 'content');
    }

    public function it_returns_data(\DateTime $date, ArticleSource $articleSource)
    {
        $uuid = Uuid::fromString('FE755198-9089-11E5-999C-0341358CC448');
        $this->getUuid()->shouldEqualUuid($uuid);
        $this->getTitle()->shouldReturn('title');
        $this->getDate()->shouldReturn($date);
        $this->getArticleSource()->shouldReturn($articleSource);
        $this->getContent()->shouldReturn('content');
        $this->shouldNotBeRemoved();
    }

    public function it_can_be_removed()
    {
        $this->shouldNotBeRemoved();
        $this->remove();
        $this->shouldBeRemoved();
    }

    public function getMatchers()
    {
        return [
                'equalUuid' => function (Uuid $uuida, Uuid $uuidb) {
                    return $uuida->equals($uuidb);
                },
        ];
    }
}
