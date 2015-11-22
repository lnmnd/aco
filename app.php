<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

if (file_exists(__DIR__.'/.env')) {
    \Dotenv::load(__DIR__);
}
\Dotenv::required('REPOSITORY_PATH');

$dburl = parse_url(getenv('DATABASE_URL'));

$inj = new Auryn\Injector();

$inj->define('Aco\Infra\FilesystemArticleRepo', [
        ':file' => getenv('REPOSITORY_PATH'),
]);
$inj->alias('Aco\App\UrlFetcher', 'Aco\Infra\GuzzleUrlFetcher');
$inj->alias('Aco\Domain\Aco\ArticleRepo', 'Aco\Infra\FilesystemArticleRepo');
$inj->define('Aco\App\CommandBus', [
        ':handlers' => [
                ['Aco\App\Command\AddArticleCommand',
                $inj->make('Aco\App\Handler\AddArticleHandler'), ],
                ['Aco\App\Command\DeleteArticleCommand',
                $inj->make('Aco\App\Handler\DeleteArticleHandler'), ],
                ['Aco\App\Command\RemoveArticleCommand',
                $inj->make('Aco\App\Handler\RemoveArticleHandler'), ],
        ],
]);
$inj->prepare('Aco\CliApp\AddArticleCliCommand', function ($command, $inj) {
    $command->setCommandBus($inj->make('Aco\App\CommandBus'));
});
$inj->prepare('Aco\CliApp\DeleteArticleCliCommand', function ($command, $inj) {
    $command->setCommandBus($inj->make('Aco\App\CommandBus'));
});
$inj->prepare('Aco\CliApp\RemoveArticleCliCommand', function ($command, $inj) {
    $command->setCommandBus($inj->make('Aco\App\CommandBus'));
});
 $inj->prepare('Aco\CliApp\ListArticlesCliCommand', function ($command, $inj) {
    $command->setRepo($inj->make('Aco\Domain\Aco\ArticleRepo'));
});

$application = new Application();
$application->add($inj->make('Aco\CliApp\AddArticleCliCommand'));
$application->add($inj->make('Aco\CliApp\DeleteArticleCliCommand'));
$application->add($inj->make('Aco\CliApp\RemoveArticleCliCommand'));
$application->add($inj->make('Aco\CliApp\ListArticlesCliCommand'));
$application->run();
