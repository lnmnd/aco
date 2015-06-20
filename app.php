<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

if (file_exists(__DIR__.'/.env')) {
    Dotenv::load(__DIR__);
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
        ':handlers' => [['Aco\Command\AddArticleCollectionCommand', $inj->make('Aco\Handler\AddArticleCollectionHandler')],
                        ['Aco\Command\RemoveArticleCollectionCommand', $inj->make('Aco\Handler\RemoveArticleCollectionHandler')]],
]);
$inj->prepare('App\AddCommand', function ($addCommand, $inj) {
    $addCommand->setCommandBus($inj->make('Aco\CommandBus'));
});
$inj->prepare('App\RemoveCommand', function ($removeCommand, $inj) {
    $removeCommand->setCommandBus($inj->make('Aco\CommandBus'));
});
$inj->prepare('App\ListCommand', function ($listCommand, $inj) {
    $listCommand->setQueryService($inj->make('AcoQuery\QueryService'));
});

$application = new Application();
$application->add($inj->make('App\AddCommand'));
$application->add($inj->make('App\RemoveCommand'));
$application->add($inj->make('App\ListCommand'));
$application->run();
