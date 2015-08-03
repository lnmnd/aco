<?php

namespace Aco;

use Rhumsaa\Uuid\Uuid;

class ArticleCollectionTest extends \PHPUnit_Framework_TestCase
{
    public function testConstruct()
    {
        $uuid = Uuid::uuid4();
        $now = new \DateTime();
        $art = new Article(new Url('http://localhost'), 'content');
        $aco = new ArticleCollection(
            $uuid,
            $now,
            'title',
            'description',
            [$art],
            ['tag1', 'tag2']
        );

        $this->assertEquals($uuid, $aco->getUuid());
        $this->assertEquals($now, $aco->getDate());
        $this->assertEquals('title', $aco->getTitle());
        $this->assertEquals('description', $aco->getDescription());
        $this->assertEquals([$art], $aco->getArticles());
        $this->assertEquals(['tag1', 'tag2'], $aco->getTags());
        $this->assertTrue($aco->equals($aco));
    }

    public function testAddArticle()
    {
        $art1 = new Article(new Url('http://localhost'), 'content1');
        $art2 = new Article(new Url('http://localhost'), 'content2');
        $aco = new ArticleCollection(
            Uuid::uuid4(),
            new \DateTime(),
            'title',
            'description',
            [$art1],
            []
        );

        $aco->addArticle($art2);

        $this->assertEquals([$art1, $art2], $aco->getArticles());
    }
}
