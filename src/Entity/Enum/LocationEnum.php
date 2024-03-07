<?php

namespace App\Entity\Enum;

enum LocationEnum: string
{
    case OnSite = 'On site';
    case Remote = 'Remote';
    case Hybrid = 'Hybrid';
}
