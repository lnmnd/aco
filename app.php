<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

$inj = new Auryn\Injector();
$inj->alias('Aco\ArticleCollectionRepository', 'Infra\SerializedArticleCollectionRepository');
$inj->alias('AcoQuery\QueryService', 'Infra\SerializedArticleCollectionRepository');
$inj->define('Infra\SerializedArticleCollectionRepository', [
		':file' => __DIR__.'/var/repository.php',
]);
$inj->alias('Aco\UrlFetcher', 'FakeInfra\FakeUrlFetcher');
$inj->define('Aco\CommandBus', [
		':handlers' => [['Aco\Command\AddArticleCollectionCommand', $inj->make('Aco\Handler\AddArticleCollectionHandler')]],
]);
$inj->prepare('App\AddCommand', function ($addCommand, $inj) {
	$addCommand->setCommandBus($inj->make('Aco\CommandBus'));
});
$inj->prepare('App\ListCommand', function ($listCommand, $inj) {
	$listCommand->setQueryService($inj->make('AcoQuery\QueryService'));
});

$application = new Application();
$application->add($inj->make('App\AddCommand'));
$application->add($inj->make('App\ListCommand'));
$application->run();

