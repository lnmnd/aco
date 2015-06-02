<?php

namespace WebApp;

use FastRoute\Dispatcher;

class WebApp
{
	private $apiController;
	private $htmlController;
	
	public function __construct(ApiController $apiController, HtmlController $htmlController)
	{
		$this->apiController = $apiController;
		$this->htmlController = $htmlController;
	}
	
	public function start()
	{
		$dispatcher = \FastRoute\simpleDispatcher(function(\FastRoute\RouteCollector $r) {
			$r->addRoute('OPTIONS', '/api/article-collections', [$this->apiController, 'optionArticleCollection']);
			$r->addRoute('POST', '/api/article-collections', [$this->apiController, 'postArticleCollection']);
			$r->addRoute('GET', '/api/article-collections', [$this->apiController, 'getArticleCollections']);
			$r->addRoute('GET', '/api/article-collections/{uuid}', [$this->apiController, 'getArticleCollection']);
                        $r->addRoute('GET', '/api/tags', [$this->apiController, 'getTags']);
                        $r->addRoute('GET', '/api/tags/{tag}', [$this->apiController, 'getTagsArticleCollections']);
			
			$r->addRoute('GET', '/', [$this->htmlController, 'getArticleCollections']);
			$r->addRoute('GET', '/article-collections/{uuid}', [$this->htmlController, 'getArticleCollection']);
			$r->addRoute('GET', '/add', [$this->htmlController, 'addArticleCollection']);
			$r->addRoute('POST', '/add', [$this->htmlController, 'addArticleCollection']);	
                        $r->addRoute('GET', '/tags', [$this->htmlController, 'getTags']);
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
				header('HTTP/1.0 405 Method not allowed');
				echo '405: Method not allowed';
				break;
			case Dispatcher::FOUND:
				$handler = $routeInfo[1];
				$vars = $routeInfo[2];
				call_user_func_array($handler, $vars);
				break;
		}
	}
}