<?php

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Console\Application;
use App\AddCommand;

$application = new Application();
$application->add(new AddCommand());
$application->run();