<?php

use Aco\CommandBus;
use Aco\Handler\AddArticleCollectionHandler;
use Aco\Command\AddArticleCollectionCommand;
use Aco\Command\Aco\Command;
use Aco\ArticleFactory;
use Aco\ArticleCollection;
use Aco\ArticleCollectionRepository;
use Aco\UrlFetcher;
use Aco\Url;

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

class FakeUrlFetcher implements UrlFetcher
{
	public $urls = [];
	
	public function fetch(Url $url)
	{
		$this->urls[] = $url;
		return 'fake content';
	}
}

class AddArticleCollectionTest extends \PHPUnit_Framework_TestCase {
	public function testAdd()
	{
		$acr = new FakeArticleCollectionRepository();
		$fuf = new FakeUrlFetcher();
		$af = new ArticleFactory($fuf);
		$cb = new CommandBus();
		$cb->register('Aco\Command\AddArticleCollectionCommand', new AddArticleCollectionHandler($acr, $af));
		$urls = ['http://localhost/a1', 'http://localhost/a2'];
		$c = new AddArticleCollectionCommand('title', 'description', $urls);
		$uuid = $cb->handle($c);
		
		$this->assertEquals(36, strlen($uuid));
		$this->assertEquals(true, $acr->called);
		$this->assertEquals($uuid, $acr->articleCollection->getUuid());
		$this->assertEquals(count($urls), count($fuf->urls));
	}
	
	/** 
	 * @test
	 * @expectedException Aco\Exception\BadUrl
	 */
	public function wrong_url()
	{
		$acr = new FakeArticleCollectionRepository();
		$fuf = new FakeUrlFetcher();
		$af = new ArticleFactory($fuf);
		$cb = new CommandBus();
		$cb->register('Aco\Command\AddArticleCollectionCommand', new AddArticleCollectionHandler($acr, $af));
		$urls = ['wrongurl'];
		$c = new AddArticleCollectionCommand('title', 'description', $urls);
		$uuid = $cb->handle($c);
	} 
}
