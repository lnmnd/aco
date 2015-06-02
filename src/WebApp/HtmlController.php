<?php

namespace WebApp;

use AcoQuery\QueryService;
use Aco\CommandBus;
use Aco\Command\AddArticleCollectionCommand;
use AcoQuery\Exception\ArticleCollectionNotFoundException;

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
		$error = false;
		if (isset($_POST['title'])) {
			$title = $_POST['title'];
			$description = $_POST['description'];
			$urls = explode(',', $_POST['urls']);
			$tags = explode(',', $_POST['tags']);
                        // empty
                        if ((count($tags) === 1) && $tags[0] === '') {
                            $tags = [];
                        }
                
			try {
				$uuid = $this->commandBus->handle(
						new AddArticleCollectionCommand($title, $description, $urls, $tags)
				);
				header('Location: /article-collections/' . $uuid);
				return;
			} catch (\Exception $e) {
				$error = true;
			}
		}
		$this->render('add.html', ['error' => $error]);
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
			return $this->render('404.html', new \stdClass());
		}
	}
	
	private function render($template, $data)
	{
		echo $this->mustache->render($template, $data);		
	}
}