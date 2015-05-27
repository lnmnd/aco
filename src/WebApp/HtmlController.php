<?php

namespace WebApp;

use AcoQuery\QueryService;
use Aco\CommandBus;

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

	public function getArticleCollections()
	{
		$this->render('index.html',
				['acos' => $this->queryService->getArticleCollections()]);
	}
	
	private function render($template, $data)
	{
		echo $this->mustache->render($template, $data);		
	}
}