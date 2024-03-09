<?php

use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle;
use Symfony\Bundle\DebugBundle\DebugBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use Symfony\UX\StimulusBundle\StimulusBundle;
use Symfony\UX\Turbo\TurboBundle;
use Twig\Extra\TwigExtraBundle\TwigExtraBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Bundle\MakerBundle\MakerBundle;
use Pontedilana\WeasyprintBundle\WeasyprintBundle;
use EasyCorp\Bundle\EasyAdminBundle\EasyAdminBundle;
use Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle;
use DAMA\DoctrineTestBundle\DAMADoctrineTestBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Nelmio\Alice\Bridge\Symfony\NelmioAliceBundle;
use Fidry\AliceDataFixtures\Bridge\Symfony\FidryAliceDataFixturesBundle;
use Hautelook\AliceBundle\HautelookAliceBundle;
return [
    FrameworkBundle::class => ['all' => true],
    DoctrineBundle::class => ['all' => true],
    DoctrineMigrationsBundle::class => ['all' => true],
    DebugBundle::class => ['dev' => true],
    TwigBundle::class => ['all' => true],
    WebProfilerBundle::class => ['dev' => true, 'test' => true],
    StimulusBundle::class => ['all' => true],
    TurboBundle::class => ['all' => true],
    TwigExtraBundle::class => ['all' => true],
    SecurityBundle::class => ['all' => true],
    MonologBundle::class => ['all' => true],
    MakerBundle::class => ['dev' => true],
    WeasyprintBundle::class => ['all' => true],
    EasyAdminBundle::class => ['all' => true],
    StofDoctrineExtensionsBundle::class => ['all' => true],
    DAMADoctrineTestBundle::class => ['test' => true],
    DoctrineFixturesBundle::class => ['dev' => true, 'test' => true],
    NelmioAliceBundle::class => ['dev' => true, 'test' => true],
    FidryAliceDataFixturesBundle::class => ['dev' => true, 'test' => true],
    HautelookAliceBundle::class => ['dev' => true, 'test' => true],
];
