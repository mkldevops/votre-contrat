<?php

use Castor\Attribute\AsContext;
use Castor\Attribute\AsTask;
use Castor\Context;

use function Castor\io;
use function Castor\load_dot_env;
use function Castor\log;
use function Castor\run;

enum Environment: string
{
    case DEV = 'dev';
    case PROD = 'prod';
}

#[AsContext()]
function my_context(): Context
{
    log('Loading context');

    return new Context(load_dot_env());
}

#[AsTask(description: 'Build frankenphp image')]
function build(): void
{
    $result = run('/usr/local/bin/docker build -t $DOCKER_IMAGE_NAME -f $PWD/frankenphp.Dockerfile .');

    if ($result->isSuccessful()) {
        io()->success('Build executed successfully');
    } else {
        io()->error('Command failed '.$result->getOutput());
    }
}

#[AsTask(description: 'deploy project')]
function deploy(string $env = Environment::DEV->value): void
{
    io()->info('Deploying project to '.$env);
    run(
        command: 'docker compose -f compose.prod.yaml up -d --pull always --remove-orphans --wait',
        environment: ['APP_ENV' => $env]
    );

    io()->info('Running migrations');
    run('docker compose -f compose.prod.yaml exec app symfony console doctrine:migrations:migrate --no-interaction');

    io()->info('Clearing cache');
    run('docker compose -f compose.prod.yaml exec app symfony console cache:clear');
}

#[AsTask(description: 'Push frankenphp image')]
function push(): void
{
    $login = run('/usr/local/bin/docker login');
    if (!$login->isSuccessful()) {
        io()->error('Login failed');
    }

    $result = run('/usr/local/bin/docker push mkldevops/moncontrat');

    if ($result->isSuccessful()) {
        io()->success('Push executed successfully');
    } else {
        io()->error('Command failed '.$result->getOutput());
    }
}
