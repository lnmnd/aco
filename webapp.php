<?php

if (file_exists(__DIR__.'/.env')) {
    \Dotenv::load(__DIR__);
}
\Dotenv::required('DATABASE_URL');
\Dotenv::required('AUTH_USE');
\Dotenv::required('DISPLAY_ERROR_DETAILS');

if (getenv('AUTH_USE') === 'true') {
    \Dotenv::required('AUTH_USER');
    \Dotenv::required('AUTH_PASS');

    if (!isset($_SERVER['PHP_AUTH_USER'])) {
        header('WWW-Authenticate: Basic realm="My Realm"');
        header('HTTP/1.0 401 Unauthorized');
        exit;
    } else {
        if (($_SERVER['PHP_AUTH_USER'] !== getenv('AUTH_USER')) ||
            ($_SERVER['PHP_AUTH_PW'] !== getenv('AUTH_PASS'))) {
            echo 'Bad auth';
            exit;
        }
    }
}

$container = new \Slim\Container();
if (getenv('DISPLAY_ERROR_DETAILS') === 'true') {
    $container['settings']['displayErrorDetails'] = true;
}
$container['queryService'] = function ($c) {
    return new \Aco\Infra\DbalArticleRepo(getenv('DATABASE_URL'));
};
$container['view'] = function ($c) {
    $view = new \Slim\Views\Twig(__DIR__.'/templates');
    $view->addExtension(new \Slim\Views\TwigExtension(
            $c['router'],
            $c['request']->getUri()
            ));

    return $view;
};

$app = new \Slim\App($container);

$app->get('/', function ($req, $res, $args) {
    $ctx = [
        'articles' => $this->queryService->findArticles(),
            ];

    return $this->view->render($res, 'index.html.twig', $ctx);
})->setName('index');

$app->get('/article/{uuid}/', function ($req, $res, $args) {
    $uuid = $args['uuid'];
    try {
        $article = $this->queryService->findArticle($uuid);
        $article->created_at = $article->created_at->format('Y-m-d');
        $ctx = [
                'article' => $article,
        ];

        return $this->view->render($res, 'article.html.twig', $ctx);
    } catch (\Aco\Domain\Aco\Exception\ArticleDoesNotExistException $e) {
        return $res->withStatus(404);
    }
})->setName('article');

$app->get('/feed/', function ($req, $res, $args) {
    $xml = new \SimpleXMLElement('<xml/>');
    $feed = $xml->addChild('feed');
    $feed->addAttribute('xmlns', 'http://www.w3.org/2005/Atom');
    $feed->addChild('title', 'Article Collections');

    $articles = $this->queryService->findArticles();
    foreach ($articles as $a) {
        $entry = $feed->addChild('entry');
        $entry->addChild('title', $a->title);
        $link = $entry->addChild('link');
        $link->addAttribute('href', $a->url);
        $link->addAttribute('rel', 'alternate');
        $link->addAttribute('type', 'text/html');
        $content = $entry->addChild('content', $a->content);
        $content->addAttribute('type', 'html');
    }

    $body = $res->getBody();
    $body->write($xml->asXML());

    return $res->withHeader('Content-Type', 'application/atom+xml');
})->setName('feed');

$app->run();
