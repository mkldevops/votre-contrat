<?php

namespace App\Contract;

use App\Entity\Contract;
use App\Exception\AppException;
use Pontedilana\WeasyprintBundle\WeasyPrint\Response\PdfResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsController]
#[Route('/contract-pdf/{id}', name: 'app_contract_pdf')]
final readonly class ContractPDFController
{
    public function __construct(
        private SluggerInterface $slugger,
        private ContractPdfHandler $contractPdfHandler,
    ) {
    }

    /**
     * @throws AppException
     */
    public function __invoke(Contract $contract): Response
    {
        return new PdfResponse(
            content: $this->contractPdfHandler->pdfContent($contract),
            fileName: sprintf('%s.pdf', $this->slugger->slug(strtolower($contract))),
            contentDisposition: ResponseHeaderBag::DISPOSITION_INLINE,
        );
    }
}
