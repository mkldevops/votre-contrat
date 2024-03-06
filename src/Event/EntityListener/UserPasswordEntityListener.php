<?php

namespace App\Event\EntityListener;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsEntityListener(event: Events::prePersist, entity: User::class)]
final readonly class UserPasswordEntityListener
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher,
    ) {
    }

    public function __invoke(User $user): void
    {
        if ('' === $user->getPassword()) {
            return;
        }

        if ($this->passwordHasher->needsRehash($user)) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $user->getPassword()));
        }
    }
}
