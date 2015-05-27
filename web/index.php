<?php

require __DIR__.'/../vendor/autoload.php';

if (file_exists(__DIR__.'/../.env')) {
	Dotenv::load(__DIR__.'/..');
}
Dotenv::required('REPOSITORY_PATH');

$inj = new Auryn\Injector();
$inj->alias('Aco\ArticleCollectionRepository', 'Infra\SerializedArticleCollectionRepository');
$inj->alias('AcoQuery\QueryService', 'Infra\SerializedArticleCollectionRepository');
$inj->define('Infra\SerializedArticleCollectionRepository', [
		':file' => getenv('REPOSITORY_PATH'),
]);
$inj->alias('Aco\UrlFetcher', 'Infra\GuzzleUrlFetcher');
$inj->define('Aco\CommandBus', [
		':handlers' => [['Aco\Command\AddArticleCollectionCommand', $inj->make('Aco\Handler\AddArticleCollectionHandler')]],
]);

$inj->delegate('Mustache_Engine', function () {
	return new Mustache_Engine(array(
			'loader' => new Mustache_Loader_FilesystemLoader(__DIR__.'/../templates'),
	));
});

$app = $inj->make('WebApp\WebApp');
$app->start();
