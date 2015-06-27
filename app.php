<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

if (file_exists(__DIR__.'/.env')) {
    Dotenv::load(__DIR__);
}
//Dotenv::required('REPOSITORY_PATH');
Dotenv::required('DATABASE_URL');

$dburl = parse_url(getenv('DATABASE_URL'));

$inj = new Auryn\Injector();
$inj->share('PDO');
$inj->define('PDO', [
    'pgsql:dbname='.ltrim($dburl["path"], '/').';host='.$dburl["host"],
    $dburl["user"],
    $dburl["pass"],
]);
//$inj->define('Infra\SerializedArticleCollectionRepository', [
//        ':file' => getenv('REPOSITORY_PATH'),
//]);
$inj->alias('Aco\ArticleCollectionRepository', 'Infra\PgsqlArticleCollectionRepository');
$inj->alias('AcoQuery\QueryService', 'Infra\PgsqlArticleCollectionRepository');
$inj->alias('Aco\UrlFetcher', 'Infra\GuzzleUrlFetcher');
$inj->define('Aco\CommandBus', [
        ':handlers' => [['Aco\Command\AddArticleCollectionCommand',
                         $inj->make('Aco\Handler\AddArticleCollectionHandler')],
                        ['Aco\Command\RemoveArticleCollectionCommand',
                         $inj->make('Aco\Handler\RemoveArticleCollectionHandler')]],
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
