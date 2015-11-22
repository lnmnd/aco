<?php

namespace spec\Aco\Domain\Aco;

use PhpSpec\ObjectBehavior;
use Aco\Domain\Aco\Url;
use Aco\Domain\Aco\ArticleSource;

class ArticleSourceSpec extends ObjectBehavior
{
    public function let(Url $url)
    {
        $this->beConstructedWith($url, 'content');
    }

    public function it_returns_data(Url $url)
    {
        $this->getUrl()->shouldReturn($url);
        $this->getContent()->shouldReturn('content');
    }

    public function it_can_compare_itself_to_others(Url $url, Url $url2)
    {
        $this->beConstructedWith(new Url('http://localhost'), 'content');
        $this->shouldBeEqual(new ArticleSource(new Url('http://localhost'), 'content'));
        $this->shouldNotBeEqual(new ArticleSource(new Url('http://localhost'), 'different content'));
        $this->shouldNotBeEqual(new ArticleSource(new Url('http://localhost2'), 'content'));
    }
}
