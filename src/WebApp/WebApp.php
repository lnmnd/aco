<?php

namespace WebApp;

use FastRoute\Dispatcher;
use AcoQuery\QueryService;
use AcoQuery\Exception\ArticleCollectionNotFoundException;
use Aco\CommandBus;
use Aco\Command\AddArticleCollectionCommand;
use Aco\Exception\BadUrl;

class WebApp
{
	private $commandBus;
	private $queryService;
	
	public function __construct(CommandBus $commandBus, QueryService $queryService)
	{
		$this->commandBus = $commandBus;
		$this->queryService = $queryService;
	}
	
	public function start()
	{
		$dispatcher = \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r) {
			$r->addRoute('POST', '/api/article-collections', [$this, 'postArticleCollection']);
			$r->addRoute('GET', '/api/article-collections', [$this, 'getArticleCollections']);
			$r->addRoute('GET', '/api/article-collections/{uuid}', [$this, 'getArticleCollection']);
		});
		
		$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		$httpMethod = $_SERVER['REQUEST_METHOD'];
			
		$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
		switch ($routeInfo[0]) {
			case Dispatcher::NOT_FOUND:
				header('HTTP/1.0 404 Not Found');
				echo '404: Not Found';
				break;
			case Dispatcher::METHOD_NOT_ALLOWED:
				$allowedMethods = $routeInfo[1];
				header('HTTP/1.0 405 Method not allowed');
				echo '405: Method not allowed';
				break;
			case Dispatcher::FOUND:
				$handler = $routeInfo[1];
				$vars = $routeInfo[2];
				header("Access-Control-Allow-Origin: *");
				header('Content-Type: application/json');
				echo json_encode(call_user_func_array($handler, $vars));
				break;
		}
	}
	
	public function postArticleCollection()
	{
		try {
			$contents = file_get_contents('php://input');
			$input = json_decode($contents);
			if ($this->badAcoInput($input)) {
				header('HTTP/1.0 400 Bad Request');
				return new \stdClass();
			}
		
			$res = new \stdClass();
			$res->uuid = $this->commandBus->handle(
					new AddArticleCollectionCommand($input->title, $input->description, $input->urls)
			);				
			return $res;
		} catch (BadUrl $e) {
			header('HTTP/1.0 400 Bad Request');
			return new \stdClass();
		}
	}
	
	public function getArticleCollections()
	{
		return $this->queryService->getArticleCollections();
	}
	
	public function getArticleCollection($uuid)
	{
		try {
			return $this->queryService->getArticleCollection($uuid);
		} catch (ArticleCollectionNotFoundException $e) {
			header('HTTP/1.0. 404 Not Found');
			return new \stdClass();
		}
	}
	
	private function badAcoInput($input)
	{
		return !isset($input->title)
			|| !isset($input->description)
			|| !isset($input->urls)
		;
	}
}