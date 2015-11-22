<?php

namespace WebApp;

use FastRoute\Dispatcher;

class WebApp
{
    private $htmlController;
    private $feedController;

    public function __construct(HtmlController $htmlController, FeedController $feedController)
    {
        $this->htmlController = $htmlController;
        $this->feedController = $feedController;
    }

    public function start()
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $r) {
            $r->addRoute('GET', '/', [$this->htmlController, 'getArticles']);
            $r->addRoute('GET', '/article-collections/{uuid}', [$this->htmlController, 'getArticle']);

            $r->addRoute('GET', '/feed', [$this->feedController, 'getArticleCollections']);
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
