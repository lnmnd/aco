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
		$furls = ['http://localhost/a1' => 'a1', 'http://localhost/a2' => 'a2'];
		$this->fuf->urls = $furls;
		$c = new AddArticleCollectionCommand('title', 'description', $urls);
		$uuid = $this->cb->handle($c);
		
		$this->assertEquals(36, strlen($uuid));
		$this->assertEquals(count($urls), count($this->fuf->callUrls));
		$this->assertEquals(true, $this->acr->called);
		$this->assertEquals($uuid, $this->acr->articleCollection->getUuid());
		$articles = $this->acr->articleCollection->getArticles();
		$this->assertEquals(2, count($articles));
		$this->assertEquals('a1', $articles[0]->getOriginalContent());
	}
	
	/**
	 * @test
	 */
	public function extract_content()
	{
		$urls = ['http://localhost/a1'];
		$furls = ['http://localhost/a1' => '<div><p>no</p></div><div><p>content here</p><p>yes</p></div><div><p>no!</p></div>'];
		$this->fuf->urls = $furls;
		$c = new AddArticleCollectionCommand('title', 'description', $urls);
		$uuid = $this->cb->handle($c);

		$articles = $this->acr->articleCollection->getArticles();
		$this->assertEquals('<p>content here</p><p>yes</p>', $articles[0]->getContent());
	} 
	
	/**
	 * @test
	 * @expectedException Aco\Exception\CannotExtractContentException
	 */
	public function no_content_to_extract()
	{
		$urls = ['http://localhost/a1'];
		$furls = ['http://localhost/a1' => ''];
		$this->fuf->urls = $furls;
		
		$c = new AddArticleCollectionCommand('title', 'description', $urls);
		$uuid = $this->cb->handle($c);
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
	
	/**
	 * @test
	 * @expectedException Aco\Exception\CannotFetchUrlException
	 */
	public function cannot_fetch_url()
	{
		$urls = ['http://doesnexist.foo'];
		$c = new AddArticleCollectionCommand('title', 'description', $urls);
		$uuid = $this->cb->handle($c);
	}	
}
