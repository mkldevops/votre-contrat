<?php

namespace App\Tests\Trait;

use App\Entity\Package;
use App\Repository\PackageRepository;

trait GetPackageTrait
{
    public static function getPackage(string $slug): Package
    {
        $package = static::getContainer()
            ->get(PackageRepository::class)
            ->findOneBy(['slug' => $slug]);

        static::assertInstanceOf(Package::class, $package);

        return $package;
    }
}
