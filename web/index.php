<?php

require __DIR__.'/../vendor/autoload.php';

$inj = new Auryn\Injector();
$inj->alias('Aco\ArticleCollectionRepository', 'Infra\SerializedArticleCollectionRepository');
$inj->alias('AcoQuery\QueryService', 'Infra\SerializedArticleCollectionRepository');
$inj->define('Infra\SerializedArticleCollectionRepository', [
		':file' => __DIR__.'/../var/repository.php',
]);
$inj->alias('Aco\UrlFetcher', 'FakeInfra\FakeUrlFetcher');
$inj->define('Aco\CommandBus', [
		':handlers' => [['Aco\Command\AddArticleCollectionCommand', $inj->make('Aco\Handler\AddArticleCollectionHandler')]],
]);

$app = $inj->make('WebApp\WebApp');
$app->start();
