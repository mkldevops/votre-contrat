<?php

namespace test;

use Castor\Attribute\AsTask;
use function Castor\io;
use function Castor\variable;
use function docker\exec as dockerExec;
use function symfony\console;

#[AsTask(description: 'Execute tests')]
function phpunit(): void
{
    if('test' !== variable('APP_ENV')) {
        dump(variable('APP_ENV'));
        io()->error('This task can only be executed in test environment');
        return;
    }

    dockerExec('php bin/phpunit');
}

#[AsTask(description: 'Execute tests', enabled: "var('APP_ENV') == 'test'")]
function all(): void
{
    if('test' !== variable('APP_ENV')) {
        io()->error('This task can only be executed in test environment');
        return;
    }

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
    console('cache:clear', silent: true);
    console('doctrine:schema:drop --force --full-database', silent: true);
    console('doctrine:schema:update --force', silent: true);
    console('doctrine:fixtures:load --no-interaction', silent: true);
    phpunit();
}
