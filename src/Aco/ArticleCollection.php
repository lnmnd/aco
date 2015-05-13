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
	
	public function __construct($title, $description, $articles)
	{
		if (empty($articles)) {
			throw new NoArticlesException();
		}
		
		$this->uuid = Uuid::uuid4();
		$this->date = new \DateTime();
		$this->title = $title;
		$this->description = $description;
		$this->articles = $articles;
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
	
	public function equals(ArticleCollection $x)
	{
		return $this->uuid === $x->getUuid();
	}
}