<?php

namespace spec\Aco\Domain\Aco;

use PhpSpec\ObjectBehavior;
use Aco\Domain\Aco\Url;

class UrlSpec extends ObjectBehavior
{
    public function it_can_be_constructed()
    {
        $this->beConstructedWith('http://localhost');
        $this->getUrl()->shouldReturn('http://localhost');
    }

    public function it_throws_exception_on_invalid_constructor()
    {
        $this->shouldThrow('\InvalidArgumentException')
                ->during('__construct', ['wrong']);
    }

    public function it_can_compare_itself_to_others()
    {
        $this->beConstructedWith('http://localhost');
        $url2 = new Url('http://localhost');
        $url3 = new Url('http://localhost2');
        $this->shouldBeEqual($url2);
        $this->shouldNotBeEqual($url3);
    }
}
