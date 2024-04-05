<?php

namespace App\Controller;

use App\Entity\Contract;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contract/{id}', name: 'app_contract')]
class ContractController extends AbstractController
{
    public function __invoke(Contract $contract): Response
    {
        if (null === ($template = $contract->getFormation()?->getTemplate()->getTemplatePath())) {
            throw $this->createNotFoundException('Template not found');
        }

        return $this->render(
            view: $template,
            parameters : [
                'contract' => $contract,
            ]
        );
    }
}
