<?php

namespace App\Controller;

use App\Entity\Contract;
use Pontedilana\PhpWeasyPrint\Pdf;
use Pontedilana\WeasyprintBundle\WeasyPrint\Response\PdfResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[Route('/contract-pdf/{id}', name: 'app_contract_pdf')]
class ContractPDFController extends AbstractController
{
    public function __construct(
        private readonly Environment $twig,
        private readonly Pdf $weasyPrint,
    ) {
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function __invoke(Contract $contract): Response
    {
        if (null === ($template = $contract->getFormation()?->getTemplate()->getTemplatePath())) {
            throw $this->createNotFoundException('Template not found');
        }

        $html = $this->twig->render(
            name: $template,
            context : [
                'contract' => $contract,
            ]
        );

        $pdfContent = $this->weasyPrint->getOutputFromHtml($html);

        return new PdfResponse(
            content: $pdfContent,
            fileName: 'file.pdf',
            contentType: 'application/pdf',
            contentDisposition: ResponseHeaderBag::DISPOSITION_INLINE,
            // or download the file instead of displaying it in the browser with
            // contentDisposition: ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            status: 200,
            headers: []
        );
    }
}
