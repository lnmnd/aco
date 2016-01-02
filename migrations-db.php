<?php

require __DIR__.'/vendor/autoload.php';

if (file_exists(__DIR__.'/.env')) {
    \Dotenv::load(__DIR__);
}
\Dotenv::required('DATABASE_URL');
$dbopts = parse_url(getenv('DATABASE_URL'));

return [
    'driver' => 'pdo_pgsql',
    'host' => $dbopts['host'],
    'user' => $dbopts['user'],
    'password' => $dbopts['pass'],
    'dbname' => ltrim($dbopts['path'], '/'),
];
