<?php

namespace App\Entity;

interface AuthorEntityInterface extends EntityInterface
{
    public function getAuthor(): ?User;

    public function setAuthor(User $user): static;
}
