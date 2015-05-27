<?php

namespace WebApp;

use AcoQuery\QueryService;
use Aco\CommandBus;
use Aco\Command\AddArticleCollectionCommand;

class HtmlController
{
	private $mustache;
	private $commandBus;
	private $queryService;

	public function __construct(\Mustache_Engine $mustache, CommandBus $commandBus, QueryService $queryService)
	{
		$this->mustache = $mustache;
		$this->commandBus = $commandBus;
		$this->queryService = $queryService;
	}

	public function addArticleCollection()
	{
		if (isset($_POST['title'])) {
			$title = $_POST['title'];
			$description = $_POST['description'];
			$urls = explode(',', $_POST['urls']);
			
			$uuid = $this->commandBus->handle(
					new AddArticleCollectionCommand($title, $description, $urls)
			);
			header('Location: /article-collections/' . $uuid);
			return;
		}
		$this->render('add.html', []);
	}
	
	public function getArticleCollections()
	{
		$this->render('index.html',
				['acos' => $this->queryService->getArticleCollections()]);
	}
	
	public function getArticleCollection($uuid)
	{
		try {
			$this->render('aco.html', ['aco' => $this->queryService->getArticleCollection($uuid)]);
		} catch (ArticleCollectionNotFoundException $e) {
			header('HTTP/1.0. 404 Not Found');
			return $this->respond(new \stdClass());
		}
	}
	
	private function render($template, $data)
	{
		echo $this->mustache->render($template, $data);		
	}
}