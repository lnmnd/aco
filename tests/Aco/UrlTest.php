<?php

use Aco\Url;

class UrlTest extends \PHPUnit_Framework_TestCase {
    public function testUrl() {
    	$url = new Url('http://localhost');
		$this->assertEquals('http://localhost', $url->getUrl());
    }
    
    /**
     * @expectedException Aco\BadUrlException
     */
    public function testWrong()
    {
    	new Url('wrong');
    }
}
