<?php

namespace deploy;

use Castor\Attribute\AsTask;
use Exception;
use function Castor\io;
use function Castor\run;
use function Castor\capture;
use function Castor\variable;
use function Symfony\Component\String\u;

/**
 * @throws Exception
 */
#[AsTask(description: 'Deploy specif branch')]
function branch(string $branch = null, bool $clean = false): void
{
    [
        'appName' => $appName,
        'databaseName' => $databaseName,
        'branch' => $branch,
        'networkName' => $networkName
    ] = infos($branch);

    $clean && clean($branch, true, true);
    createVolume([$appName . '-var', $appName . '-uploads', $databaseName]);
    createNetwork($networkName);

    $databaseUrl = handleDatabase($branch);

    handleApp(databaseUrl: $databaseUrl, branch: $branch);

    install($branch);

    run('docker ps -a -f name=' . $networkName);
}

function createVolume(array $names): void
{
    foreach ($names as $name) {
        run('docker volume create ' . $name);
    }
}

/**
 * @throws Exception
 */
#[AsTask(description: 'Deploy specif branch')]
function clean(string $branch = null, bool $volumes = false, bool $networks = false): bool
{
    ['appName' => $appName, 'databaseName' => $databaseName,] = infos($branch);

    foreach ([$databaseName, $appName] as $name) {
        if (exists($name)) {
            run('docker stop ' . $name, allowFailure: true);
            run('docker rm -f ' . $name, allowFailure: true);
        }
    }

    if ($volumes) {
        run('docker volume prune -f');
    }

    if ($networks) {
        run('docker network prune -f');
    }

    return true;
}

/**
 * @return array{
 *     issue: int,
 *     appName: string,
 *     databaseName: string,
 *     branch: string,
 *     networkName: string,
 *     port: string
 * }
 * @throws Exception
 */
function infos(?string $branch): array
{
    $branch ??= capture('git branch --show-current');
    if ('' === $branch) {
        throw new Exception('No branch specified');
    }

    $issue = (int)u($branch)->before('-')->toString();
    if (0 === $issue) {
        dump($branch, $issue, u($branch)->before('-')->toString());
        io()->error('No issue number found in branch name');
    }

    $networkName = sprintf("%s-%d", variable(key: 'PROJECT_NAME'), $issue);

    return [
        'issue' => $issue,
        'appName' => $networkName . '-app',
        'databaseName' => $networkName . '-database',
        'branch' => $branch,
        'networkName' => $networkName,
        'port' =>  '6' . str_pad((string) $issue, 4, '0', STR_PAD_LEFT),
    ];
}

function exists(string $containerName): bool
{
    return '' !== capture('docker ps -a -q -f name=' . $containerName);
}

function handleDatabase(string $branch = null): string
{
    ['databaseName' => $databaseName, 'networkName' => $networkName] = infos($branch);

    $password = variable('POSTGRES_PASSWORD') ?? u(hash('sha256', $databaseName))->slice(0, 16)->toString();

    if (!exists($databaseName)) {
        run(strtr(
            'docker run -d --name :databaseName ' .
            '-e POSTGRES_DB=app ' .
            '-e POSTGRES_USER=app ' .
            '-e POSTGRES_PASSWORD=:password ' .
            '--network :networkName ' .
            '-v :databaseName:/var/lib/postgresql/data ' .
            '-p 5432 ' .
            'postgres:16',
            [
                ':databaseName' => $databaseName,
                ':networkName' => $networkName,
                ':password' => $password
            ]
        ));
    }

    $databaseUrl = sprintf('postgresql://app:%s@%s:5432/app', $password, $databaseName);
    io()->info('Database URL: ' . $databaseUrl);
    return $databaseUrl;
}

/**
 * @throws Exception
 */
function handleApp(string $databaseUrl, ?string $branch = null)
{
    ['issue' => $issue, 'appName' => $appName, 'networkName' => $networkName, 'port' => $port] = infos($branch);

    run('docker pull ' . variable('DOCKER_IMAGE_NAME') . ':' . $branch);

    if (exists($appName)) {
        io()->warning('Container already exists');
        return;
    }

    run(command: strtr(
        'docker run ' .
        '--name :appName ' .
        '-p :port:80 ' .
        '-v :appName-var:/srv/app/var ' .
        '-v :appName-uploads:/var/www/html/uploads ' .
        '-e APP_ENV=dev ' .
        '-e DATABASE_URL=:databaseUrl-:appName ' .
        '-e APP_SECRET=:appSecret ' .
        '--network :networkName ' .
        '-i --rm -d ' .
        ':dockerImageName::branch',
        [
            ':appName' => $appName,
            ':issue' => $issue,
            ':databaseUrl' => $databaseUrl,
            ':appSecret' => variable('APP_SECRET'),
            ':dockerImageName' => variable('DOCKER_IMAGE_NAME'),
            ':branch' => $branch,
            ':networkName' => $networkName,
            ':port' => $port
        ]
    ));
}

function install(string $branch = null) : void
{
    $dockerExec = static fn(string $command) => run(
        sprintf("docker exec -it %s %s", infos($branch)['appName'], $command)
    );

    $dockerExec('symfony composer install');
    $dockerExec('chmod -R 777 var/ public/uploads/');

    $dockerExec('symfony console doctrine:database:create --if-not-exists');
    $dockerExec('symfony console doctrine:migrations:migrate --no-interaction');

    if (isFeature($branch)) {
        $dockerExec('symfony console hautelook:fixtures:load --no-interaction');
    }
}

function isFeature(string $branch) : bool
{
    return !preg_match('/^(\d+\.\d+\.\d+|main)$/', $branch);
}

#[AsTask(description: 'Add reverse proxy')]
function addReverseProxy(?string $branch = null) : void
{
    ['networkName' => $networkName, 'port' => $port ] = infos($branch);

    $host = sprintf("%s.%s", $networkName, variable('DOMAIN_NAME'));

    $data = [ 'apps' => [ 'http' => [ 'servers' => [
        'srv0' => [
            'listen' => [':80'],
            'routes' => [
                [
                    'match' => [['host' => [$host]]],
                    'handle' => [
                        [
                            'handler' => 'reverse_proxy',
                            'upstreams' => [
                                ['dial' => 'localhost:'.$port]
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]]]];

    run(sprintf(
        'curl -X POST "http://localhost:2019/load" -H "Content-Type: application/json" -d \'%s\'',
        json_encode($data)
    ));
}

function createNetwork(string $networkName): string
{
    if (capture('docker network ls --format "{{.Name}}" | grep ' . $networkName, allowFailure: true)) {
        io()->info('Network already exists');
        return $networkName;
    }

    io()->info('Creating network ' . $networkName);
    run('docker network create ' . $networkName);

    return $networkName;
}
