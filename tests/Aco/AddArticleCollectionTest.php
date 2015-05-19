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
use FakeInfra\FakeArticleCollectionRepository;
use FakeInfra\FakeUrlFetcher;

class AddArticleCollectionTest extends \PHPUnit_Framework_TestCase {
	private $acr;
	private $fuf;
	private $af;
	private $cb;
	
	public function setUp()
	{
		$this->acr = new FakeArticleCollectionRepository();
		$this->fuf = new FakeUrlFetcher();
		$this->af = new ArticleFactory($this->fuf);
		$this->cb = new CommandBus();
		$this->cb->register('Aco\Command\AddArticleCollectionCommand', new AddArticleCollectionHandler($this->acr, $this->af));
	}
	
	public function testAdd()
	{
		$urls = ['http://localhost/a1', 'http://localhost/a2'];
		$c = new AddArticleCollectionCommand('title', 'description', $urls);
		$uuid = $this->cb->handle($c);
		
		$this->assertEquals(36, strlen($uuid));
		$this->assertEquals(true, $this->acr->called);
		$this->assertEquals($uuid, $this->acr->articleCollection->getUuid());
		$this->assertEquals(count($urls), count($this->fuf->urls));
	}
	
	/** 
	 * @test
	 * @expectedException Aco\Exception\BadUrlException
	 */
	public function wrong_url()
	{
		$urls = ['wrongurl'];
		$c = new AddArticleCollectionCommand('title', 'description', $urls);
		$uuid = $this->cb->handle($c);
	} 
	
	/**
	 * @test
	 * @expectedException Aco\Exception\NoArticlesException
	 */
	public function no_articles()
	{
		$urls = [];
		$c = new AddArticleCollectionCommand('title', 'description', $urls);
		$uuid = $this->cb->handle($c);
	}
}
