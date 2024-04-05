<?php

namespace App\Event\DoctrineListener;

use App\Entity\AuthorEntityInterface;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsDoctrineListener(event: Events::prePersist)]
#[AsDoctrineListener(event: Events::preUpdate)]
final readonly class AuthorDoctrineListener
{
    public function __construct(
        private Security $security,
    ) {
    }

    public function __invoke(PrePersistEventArgs|PreUpdateEventArgs $eventArgs): void
    {
        $entity = $eventArgs->getObject();

        if (!$entity instanceof AuthorEntityInterface
            || $entity->getAuthor() instanceof User
            || !$this->security->getUser() instanceof User) {
            return;
        }

        $entity->setAuthor($this->security->getUser());
    }
}
