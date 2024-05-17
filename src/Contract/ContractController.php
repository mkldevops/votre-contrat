<?php

namespace App\Contract;

use App\Entity\Contract;
use App\Exception\AppException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/contract/{id}', name: 'app_contract')]
class ContractController extends AbstractController
{
    /**
     * @throws AppException
     */
    public function __invoke(Contract $contract, ContractPdfHandler $contractPdfHandler): Response
    {
        return new Response($contractPdfHandler->htmlContent($contract));
    }
}
