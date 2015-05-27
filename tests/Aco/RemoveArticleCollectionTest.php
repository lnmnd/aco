<?php

use Aco\CommandBus;
use Aco\Handler\RemoveArticleCollectionHandler;
use Aco\Command\RemoveArticleCollectionCommand;
use Aco\Command\Aco\Command;
use Aco\ArticleFactory;
use Aco\ArticleCollection;
use Aco\ArticleCollectionRepository;
use Aco\UrlFetcher;
use Aco\Url;
use FakeInfra\FakeArticleCollectionRepository;
use FakeInfra\FakeUrlFetcher;
use Aco\Aco;

class RemoveArticleCollectionTest extends \PHPUnit_Framework_TestCase {
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
		$this->cb->register('Aco\Command\RemoveArticleCollectionCommand', new RemoveArticleCollectionHandler($this->acr));
	}

	/**
	 * @test
	 */
	public function remove()
	{
		$furls = ['http://url1' => 'content'];
		$this->fuf->urls = $furls;
		$articleCollection = new ArticleCollection('tit', 'des', [$this->af->make(new Url('http://url1'))]);
		$uuid = $articleCollection->getUuid();
		$this->acr->articleCollections[] = $articleCollection;
		
		$c = new RemoveArticleCollectionCommand($uuid);
		$this->cb->handle($c);
		
		$this->assertEmpty($this->acr->articleCollections);
	}
	
	/**
	 * @test
	  @expectedException Aco\Exception\DoesNotExistException
	 */
	public function does_not_exist()
	{
		$furls = ['http://url1' => 'content'];
		$this->fuf->urls = $furls;
		$articleCollection = new ArticleCollection('tit', 'des', [$this->af->make(new Url('http://url1'))]);
		$uuid = $articleCollection->getUuid();
		
		$c = new RemoveArticleCollectionCommand($uuid);
		$this->cb->handle($c);
	}
}