<?php

use Aco\Url;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    public function testUrl()
    {
        $url = new Url('http://localhost');
        $this->assertEquals('http://localhost', $url->getUrl());
    }

    public function testEquals()
    {
        $url1 = new Url('http://localhost');
        $url2 = new Url('http://localhost');
        $url3 = new Url('http://localhost2');

        $this->assertTrue($url1->equals($url2));
        $this->assertFalse($url1->equals($url3));
    }

    /**
     * @expectedException Aco\Exception\BadUrlException
     */
    public function testWrong()
    {
        new Url('wrong');
    }
}
