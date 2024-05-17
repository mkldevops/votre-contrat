<?php

namespace symfony;

use Symfony\Component\Process\Process;
use function docker\exec;
use function Castor\run;
use Castor\Attribute\AsTask;
use function Castor\capture;
use function Castor\io;

#[AsTask(description: 'Execute symfony command')]
function console(string $cmd, bool $silent = false): Process
{
    return exec('php bin/console '.$cmd, silent: $silent);
}

#[AsTask(description: 'Execute symfony command')]
function doctrine(string $cmd, string $env = 'dev'): Process
{
    return console('doctrine:'.$cmd);
}

#[AsTask(description: 'Execute symfony command')]
function cc(string $env = 'dev'): Process
{
    return console('cache:clear');
}

#[AsTask(description: 'Execute symfony command')]
function migration(bool $amend = false): void
{
    if ($amend) {
        $last = capture('docker compose exec php php bin/console doctrine:migrations:current');
        io()->info('Last migration : '.$last);

        doctrine('migration:execute -n --down '.addslashes($last));
        run('rm -f src/Migrations/*'.substr($last, 0, 14).'.php');
    }

    console('make:migration');
    doctrine('migration:migrate --no-interaction');
}
