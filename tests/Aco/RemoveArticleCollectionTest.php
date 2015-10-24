<?php

namespace Aco;

use Rhumsaa\Uuid\Uuid;
use Aco\Handler\RemoveArticleCollectionHandler;
use Aco\Command\RemoveArticleCollectionCommand;
use FakeInfra\FakeArticleCollectionRepository;
use FakeInfra\FakeUrlFetcher;

class RemoveArticleCollectionTest extends \PHPUnit_Framework_TestCase
{
    private $acr;
    private $fuf;
    private $cb;

    public function setUp()
    {
        $this->acr = new FakeArticleCollectionRepository();
        $this->fuf = new FakeUrlFetcher();
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
        $articles = [new Article(
            new Url('http://url1'),
            'title',
            'content',
            'content'
        )];
        $articleCollection = new ArticleCollection(
            $uuid = Uuid::uuid4(),
            new \DateTime(),
            'tit',
            'des',
            $articles);
        $this->acr->articleCollections[] = $articleCollection;
        $uuid = $articleCollection->getUuid();

        $c = new RemoveArticleCollectionCommand($uuid);
        $this->cb->handle($c);

        $this->assertEmpty($this->acr->articleCollections);
    }

    /**
     * @test
     * @expectedException Aco\Exception\DoesNotExistException
     */
    public function does_not_exist()
    {
        $furls = ['http://url1' => 'content'];
        $this->fuf->urls = $furls;
        $articles = [new Article(
            new Url('http://url1'),
            'title',
            'content',
            'content')];
        $articleCollection = new ArticleCollection(
            Uuid::uuid4(),
            new \DateTime(),
            'tit',
            'des',
            $articles);
        $uuid = $articleCollection->getUuid();

        $c = new RemoveArticleCollectionCommand($uuid);
        $this->cb->handle($c);
    }
}
