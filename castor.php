<?php

use Castor\Attribute\AsContext;
use Castor\Attribute\AsTask;
use Castor\Context;
use Symfony\Component\Process\Process;

use function Castor\io;
use function Castor\load_dot_env;
use function Castor\run;

#[AsContext()]
function my_context(): Context
{
    io()->note('Loading context');

    return new Context(load_dot_env());
}

#[AsTask(description: 'Build frankenphp image')]
function build(): void
{
    $result = run('/usr/local/bin/docker build -t $DOCKER_IMAGE_NAME .');

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
    docker(command: 'up -d --pull always --remove-orphans --wait');

    io()->info('Running migrations');
    dockerExec('symfony composer install', env: $env);

    io()->info('Running migrations');
    dockerExec('symfony console doctrine:migrations:migrate --no-interaction', env: $env);

    io()->info('Clearing cache');
    dockerExec('symfony console cache:clear', env: $env);

    io()->info('chmod cache & logs');
    dockerExec('chmod -R 777 var public/uploads', env: $env);
}

#[AsTask(description: 'Execute docker exec command')]
function dockerExec(string $command, string $service = 'app', string $env = 'dev'): Process
{
    return docker(sprintf(
        'exec %s %s %s',
        $env ? '-e APP_ENV='.$env : '',
        $service,
        $command
    ));
}

#[AsTask(description: 'Execute docker exec command')]
function dockerLogs(string $command, ?string $service = null, bool $follow = false): Process
{
    return docker(
        command: sprintf('logs %s %s %s', $service, $command, $follow ? '-f' : '')
    );
}

#[AsTask(description: 'Execute docker ps')]
function dockerPs(bool $all = false): Process
{
    return docker(
        command: sprintf('ps %s', $all ? '-a' : '')
    );
}

#[AsTask(description: 'Execute docker exec command')]
function docker(string $command): Process
{
    io()->info('Executing docker command : '.$command);

    return run(
        command: sprintf('docker compose -f compose.prod.yaml %s', $command)
    );
}

#[AsTask(description: 'Push image')]
function push(): void
{
    $login = run('/usr/local/bin/docker login');
    if (!$login->isSuccessful()) {
        io()->error('Login failed');
    }

    $result = run('/usr/local/bin/docker push $DOCKER_IMAGE_NAME');

    if ($result->isSuccessful()) {
        io()->success('Push executed successfully');
    }
}
