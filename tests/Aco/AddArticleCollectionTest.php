<?php

use Aco\CommandBus;
use Aco\Handler\AddArticleCollectionHandler;
use Aco\Command\AddArticleCollectionCommand;
use Aco\Command\Aco\Command;
use Aco\ArticleCollection;
use Aco\ArticleCollectionRepository;

class FakeArticleCollectionRepository implements ArticleCollectionRepository
{
	public $called = false;
	public $articleCollection = null;
	
	public function add(ArticleCollection $articleCollection)
	{
		$this->called = true;
		$this->articleCollection = $articleCollection;	
	}
}

class AddArticleCollectionTest extends \PHPUnit_Framework_TestCase {
	public function testAdd()
	{
		$acr = new FakeArticleCollectionRepository();
		$cb = new CommandBus();
		$cb->register('Aco\Command\AddArticleCollectionCommand', new AddArticleCollectionHandler($acr));
		$urls = ['http://localhost/a1', 'http://localhost/a2'];
		$c = new AddArticleCollectionCommand('title', 'description', $urls);
		$uuid = $cb->handle($c);
		
		$this->assertEquals(36, strlen($uuid));
		$this->assertEquals(true, $acr->called);
		$this->assertEquals($uuid, $acr->articleCollection->getUuid());
	}
}
