<?php

namespace App\Tests\Trait;

use App\Entity\User;
use App\Repository\UserRepository;

trait GetUserTrait
{
    public static function getUser(string $email): User
    {
        $user = static::getContainer()
            ->get(UserRepository::class)
            ->findOneBy(['email' => $email]);

        static::assertInstanceOf(User::class, $user);

        return $user;
    }
}
