<?php

namespace test;

use Castor\Attribute\AsTask;
use function Castor\io;
use function Castor\run;
use function Castor\variable;
use function docker\exec as dockerExec;
use function symfony\console;

#[AsTask(description: 'Execute tests')]
function phpunit(): void
{
    run('php bin/phpunit', environment: ['APP_ENV' => 'test']);
}

#[AsTask(description: 'Execute tests')]
function all(): void
{
    $environment = ['APP_ENV' => 'test'];

    // check if vendor is installed
    if (!file_exists('vendor/autoload.php')) {
        io()->section('Installing composer dependencies');
        dockerExec('composer install', silent: true);
    }

    // check node_modules
    if (file_exists('package.json') && !file_exists('node_modules')) {
        io()->section('Installing npm dependencies');
        dockerExec('npm install', silent: true);
        dockerExec('npm run build', silent: true);
    }

    io()->section('Running tests');
    run('symfony console cache:clear', $environment);
    run('symfony console doctrine:schema:drop --force --full-database', $environment);
    run('symfony console doctrine:schema:update --force', $environment);
    run('symfony console hautelook:fixtures:load --no-interaction', $environment);
    phpunit();
}
