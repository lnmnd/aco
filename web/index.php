<?php

require __DIR__.'/../vendor/autoload.php';

$inj = new Auryn\Injector();
$inj->alias('AcoQuery\QueryService', 'Infra\SerializedArticleCollectionRepository');
$inj->define('Infra\SerializedArticleCollectionRepository', [
		':file' => __DIR__.'/../var/repository.php',
]);

$app = $inj->make('WebApp\WebApp');
$app->start();
