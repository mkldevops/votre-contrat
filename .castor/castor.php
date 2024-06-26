<?php

use Castor\Attribute\AsContext;
use Castor\Attribute\AsTask;
use Castor\Context;

use function Castor\import;
use function Castor\io;
use function Castor\load_dot_env;
use function Castor\log;
use function Castor\variable;
use function git\commit;
use function git\push as gitPush;
use function quality\analyze;
use function symfony\console;
use function docker\exec as dockerExec;
use function docker\up as dockerUp;
use function docker\compose;
use function test\all as testAll;


import(__DIR__);

#[AsContext(name: 'local', default: true)]
function contextLocal(): Context
{
    log('Loading local context');

    return new Context(
        data: [...load_dot_env(), ... [
            'APP_ENV' => 'dev',
            'SERVER_NAME' => 'app.localhost'
        ]]
    );
}

#[AsContext(name: 'test')]
function contextTest(): Context
{
    log('Loading test context');

    return new Context(
        data: load_dot_env(),
        environment: ['APP_ENV' => 'test'],
        quiet: true,
    );
}

#[AsContext(name: 'prod')]
function contextProd(): Context
{
    log('Loading prod context');

    return new Context(
        data: [...load_dot_env(), ...[
            'compose_file' => 'compose.prod.yml',
        ]],
        environment: ['APP_ENV' => 'prod']
    );
}

#[AsTask(description: 'env')]
function info(): void
{
    io()->info('Environment: '. variable('APP_ENV'));
    io()->info('Server name: '. variable('SERVER_NAME'));
}


#[AsTask(description: 'Install project')]
function install(): void
{
    io()->title('Installing project');

    sync(dropDatabase: false, noFixtures: false);
    dockerUp(build: true);

    io()->success('Project installed');
}

#[AsTask(description: 'Install project')]
function sync(bool $dropDatabase = true, bool $noFixtures = true): void
{
    dockerUp();

    dockerExec('symfony composer install');

    if(file_exists('package.json')) {
        dockerExec('npm install');
        dockerExec('npm run dev');
    }

    if ($dropDatabase) {
        console('doctrine:database:drop --force --if-exists');
    }

    console('doctrine:database:create --if-not-exists');
    console('doctrine:migrations:migrate --no-interaction');

    if ($noFixtures === false) {
        console('hautelook:fixtures:load --no-interaction');
    }
}

#[AsTask(description: 'deploy project')]
function deploy(): void
{
    io()->info('Deploying project to '. variable('APP_ENV'));
    compose(command: 'up -d --pull always --remove-orphans --wait');

    io()->info('Running migrations');
    dockerExec('symfony composer install');

    io()->info('Running migrations');
    dockerExec('symfony console doctrine:migrations:migrate --no-interaction');

    io()->info('Clearing cache');
    dockerExec('symfony console cache:clear');

    io()->info('chmod cache & logs');
    dockerExec('chmod -R 777 var public/uploads');
}

#[AsTask(description: 'Git commit and push')]
function push(?string $message = null, bool $noRebase = false): void
{
    analyze();
    contextTest();
    \Castor\run('echo $APP_ENV');
    testAll();

    commit($message, $noRebase);
    gitPush();
}
