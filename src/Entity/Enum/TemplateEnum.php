<?php

namespace App\Entity\Enum;

enum TemplateEnum: string
{
    case basic = 'basic';

    public function getTemplatePath(): string
    {
        return sprintf('formations_templates/%s.html.twig', $this->value);
    }
}
