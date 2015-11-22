<?php

require __DIR__.'/../vendor/autoload.php';

if (file_exists(__DIR__.'/../.env')) {
    \Dotenv::load(__DIR__.'/..');
}
\Dotenv::required('REPOSITORY_PATH');
//\Dotenv::required('DATABASE_URL');
\Dotenv::required('AUTH_USE');

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

$inj = new Auryn\Injector();
$inj->define('Aco\Infra\FilesystemArticleRepo', [
		':file' => getenv('REPOSITORY_PATH'),
]);
$inj->alias('AcoQuery\QueryService', 'Aco\Infra\FilesystemArticleRepo');

$inj->delegate('Mustache_Engine', function () {
    return new Mustache_Engine(array(
        'loader' => new Mustache_Loader_FilesystemLoader(__DIR__.'/../templates'),
    ));
});

$app = $inj->make('WebApp\WebApp');
$app->start();
