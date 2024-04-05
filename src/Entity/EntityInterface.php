<?php

namespace App\Entity;

use Stringable;
use Symfony\Component\Uid\Uuid;

interface EntityInterface extends Stringable
{
    public function getId(): ?Uuid;
}
