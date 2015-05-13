<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;

$inj = new Auryn\Injector();

$application = new Application();
$application->add($inj->make('App\AddCommand'));
$application->run();

