<?php

use Aco\CommandBus;
use Aco\Handler\AddArticleCollectionHandler;
use Aco\Command\AddArticleCollectionCommand;
use Aco\Command\Aco\Command;
use Aco\DateTimeGetter;
use Aco\ArticleCollection;
use Aco\ArticleCollectionRepository;

class FakeDateTimeGetter implements DateTimeGetter
{
	private $called = false;
	
	public function now()
	{
		$this->called = true;
		return null;
	}
	
	public function isCalled()
	{
		return $this->called;
	}
}

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
		$dtg = new FakeDateTimeGetter();
		$cb = new CommandBus();
		$cb->register('Aco\Command\AddArticleCollectionCommand', new AddArticleCollectionHandler($acr, $dtg));
		
		$c = new AddArticleCollectionCommand('title', 'description');
		$uuid = $cb->handle($c);
		
		$this->assertEquals(true, $dtg->isCalled());
		$this->assertEquals(36, strlen($uuid));
		
		$this->assertEquals(true, $acr->called);
		$this->assertEquals($uuid, $acr->articleCollection->getUuid());
	}
}
