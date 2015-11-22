<?php

namespace spec\Aco\Domain\Aco;

use PhpSpec\ObjectBehavior;

class ContentExtractorSpec extends ObjectBehavior
{
    public function it_can_extract_content()
    {
        $sourceContent = '<div><p>no</p></div><div><p>content here</p><p>yes</p></div><div><p>no!</p></div>';
        $content = '<p>content here</p><p>yes</p>';
        $this::extractContent($sourceContent)->shouldReturn($content);
    }

    public function it_can_remove_styles()
    {
        $sourceContent = '<div><p class="foo" style="border: 1px solid red;">content</p></div>';
        $content = '<p>content</p>';
        $this::extractContent($sourceContent)->shouldReturn($content);
    }

    public function it_can_return_empty_content()
    {
        $sourceContent = '';
        $content = '';
        $this::extractContent($sourceContent)->shouldReturn($content);
    }
}
