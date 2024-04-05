<?php

use Castor\Attribute\AsContext;
use Castor\Attribute\AsTask;
use Castor\Context;
use Symfony\Component\Process\Process;

use function Castor\io;
use function Castor\load_dot_env;
use function Castor\log;
use function Castor\run;

enum castor: string
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
function deploy(string $env = 'prod'): void
{
    io()->info('Deploying project to '.$env);
    run(
        command: 'docker compose -f compose.prod.yaml up -d --pull always --remove-orphans --wait',
        environment: ['APP_ENV' => $env]
    );

    io()->info('Running migrations');
    dockerExec('symfony composer install', $env);

    io()->info('Running migrations');
    dockerExec('symfony console doctrine:migrations:migrate --no-interaction', $env);

    io()->info('Clearing cache');
    dockerExec('symfony console cache:clear', $env);
}

#[AsTask(description: 'Execute docker exec command')]
function dockerExec(string $command, string $env = 'dev'): Process
{
    io()->info('Executing command '.$command.' in '.$env.' environment');

    return run(
        command: sprintf('docker compose -f compose.prod.yaml exec app %s', $command)
    );
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
