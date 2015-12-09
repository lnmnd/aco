<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

if (file_exists(__DIR__.'/.env')) {
    \Dotenv::load(__DIR__);
}
\Dotenv::required('REPOSITORY_PATH');

$dburl = parse_url(getenv('DATABASE_URL'));

$container = new \Pimple\Container();
$container['article_repo'] = function ($c) {
    return new \Aco\Infra\FilesystemArticleRepo(getenv('REPOSITORY_PATH'));
};
$container['url_fetcher'] = function ($c) {
    return new \Aco\Infra\GuzzleUrlFetcher();
};
$container['command_bus'] = function ($c) {
    return new \Aco\App\CommandBus([
        ['Aco\App\Command\AddArticleCommand',
         new \Aco\App\Handler\AddArticleHandler($c['article_repo'], $c['url_fetcher']), ],
        ['Aco\App\Command\DeleteArticleCommand',
         new \Aco\App\Handler\DeleteArticleHandler($c['article_repo']), ],
        ['Aco\App\Command\RemoveArticleCommand',
         new \Aco\App\Handler\RemoveArticleHandler($c['article_repo']), ],
    ]);
};

$container['command.add_article'] = function ($c) {
    $cmd = new \Aco\CliApp\AddArticleCliCommand();
    $cmd->setCommandBus($c['command_bus']);

    return $cmd;
};

$container['command.delete_article'] = function ($c) {
    $cmd = new \Aco\CliApp\DeleteArticleCliCommand();
    $cmd->setCommandBus($c['command_bus']);

    return $cmd;
};

$container['command.remove_article'] = function ($c) {
    $cmd = new \Aco\CliApp\RemoveArticleCliCommand();
    $cmd->setCommandBus($c['command_bus']);

    return $cmd;
};

$container['command.list_articles'] = function ($c) {
        $cmd = new \Aco\CliApp\ListArticlesCliCommand();
        $cmd->setRepo($c['article_repo']);

        return $cmd;
};

$application = new Application();
$application->add($container['command.add_article']);
$application->add($container['command.delete_article']);
$application->add($container['command.remove_article']);
$application->add($container['command.list_articles']);
$application->run();
