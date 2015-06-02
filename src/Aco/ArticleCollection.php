<?php

namespace Aco;

use Rhumsaa\Uuid\Uuid;
use Aco\Exception\NoArticlesException;

class ArticleCollection
{
	private $uuid;
	private $date;
	private $title;
	private $description;
	/**
	 * @var Article[]
	 */
	private $articles;
        /**
         * @var string[]
         */
        private $tags;        
	
	public function __construct(Uuid $uuid, \Datetime $date, $title, $description, $articles, $tags = [])
	{
		if (empty($articles)) {
			throw new NoArticlesException();
		}
		
		$this->uuid = $uuid;
		$this->date = $date;
		$this->title = $title;
		$this->description = $description;
		$this->articles = $articles;
                $this->tags = $tags;
	}
	
	/**
	 * @return void
	 */
	public function addArticle(Article $article)
	{
		$this->articles[] = $article;
	}
	
	public function getUuid()
	{
		return $this->uuid;
	}
	
	public function getDate()
	{
		return $this->date;
	}
	
	public function getTitle()
	{
		return $this->title;
	}

	public function getDescription()
	{
		return $this->description;
	}
	
	public function getArticles()
	{
		return $this->articles;
	}
	
        public function getTags()
        {
            return $this->tags;
        }
        
	public function equals(ArticleCollection $x)
	{
		return $this->uuid === $x->getUuid();
	}
}