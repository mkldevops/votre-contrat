<?php

namespace docker;

use Castor\Attribute\AsTask;
use Psr\Cache\CacheItemInterface;
use Symfony\Component\Process\Process;

use function Castor\cache;
use function Castor\capture;
use function Castor\io;
use function Castor\log;
use function Castor\run;
use function Castor\variable;

function compose(string $command, array $environment = [], bool $silent = false): Process
{
    log(message: 'Executing docker command : docker compose '.$command);

    return run(
        command: sprintf('docker compose %s', $command),
        environment: $environment,
        quiet: $silent
    );
}

#[AsTask(description: 'Install project')]
function sh(): void
{
    if (!isContainerRunning()) {
        io()->error('Container is not running');

        $restart = io()->ask('Do you want to start the container?', 'yes');
        if ('yes' !== $restart) {
            return;
        }

        up();
    }

    exec('zsh');
}

function isContainerRunning(): bool
{
    return cache('docker-is-running', static function (CacheItemInterface $item) : bool {
        $item->expiresAfter(20);
        return (bool) capture('docker compose ps | grep php && echo 1 || echo 0');
    });
}

#[AsTask(description: 'Execute docker exec command')]
function exec(string $command, string $service = 'php', bool $silent = false): Process
{
    if (!isContainerRunning()) {
        return run($command);
    }

    return compose(
        command : sprintf(
            'exec -e APP_ENV=%s %s %s',
            variable('APP_ENV'),
            $service,
            $command
        ),
        silent: $silent
    );
}

#[AsTask(description: 'Install project')]
function up(bool $restart = false, bool $build = false, bool $removeVolumes = false): void
{
    if ($restart) {
        io()->title('Restarting project');
        compose('down --remove-orphans '.($removeVolumes ? '--volumes' : ''));
    }

    io()->title('Starting project');

    $up = compose(
        command: sprintf(
            'up -d --wait %s',
            $build ? '--build' : ''
        )
    );

    if (!$up->isSuccessful()) {
        compose('logs -f');
    }
}

#[AsTask(description: 'Execute docker push command')]
function push(string $target, ?string $tag = null): Process
{
    $login = run('docker login --username $DOCKER_USERNAME --password $DOCKERHUB_TOKEN');
    if (!$login->isSuccessful()) {
        io()->error('Login failed');

        return $login;
    }

    // docker build with target
    $build = run('docker build --target '.$target.' -t $DOCKER_IMAGE_NAME:'.$tag.' .');

    if (!$build->isSuccessful()) {
        io()->error('Build failed');

        return $build;
    }

    $result = run('docker push $DOCKER_IMAGE_NAME:'.$tag);

    if ($result->isSuccessful()) {
        io()->success('Push executed successfully');
    }

    return $result;
}


#[AsTask(description: 'Execute docker exec command')]
function logs(string $command, ?string $service = null, bool $follow = false): Process
{
    return compose(
        command: sprintf('logs %s %s %s', $service, $command, $follow ? '-f' : '')
    );
}

#[AsTask(description: 'Execute docker ps')]
function ps(bool $all = false): Process
{
    return compose(
        command: sprintf('ps %s', $all ? '-a' : '')
    );
}

#[AsTask(description: 'Build image')]
function build(): void
{
    $result = run('/usr/local/bin/docker build -t $DOCKER_IMAGE_NAME .');

    if ($result->isSuccessful()) {
        io()->success('Build executed successfully');
    } else {
        io()->error('Command failed '.$result->getOutput());
    }
}
