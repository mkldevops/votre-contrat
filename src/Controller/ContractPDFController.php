<?php

namespace App\Controller;

use App\Entity\Contract;
use Pontedilana\PhpWeasyPrint\Pdf;
use Pontedilana\WeasyprintBundle\WeasyPrint\Response\PdfResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

#[AsController]
#[Route('/contract-pdf/{id}', name: 'app_contract_pdf')]
final readonly class ContractPDFController
{
    public function __construct(
        private Environment $twig,
        private Pdf $weasyPrint,
        private SluggerInterface $slugger,
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
            throw new NotFoundHttpException('Template not found');
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
            fileName: sprintf('%s.pdf', $this->slugger->slug(strtolower($contract))),
            contentType: 'application/pdf',
            contentDisposition: ResponseHeaderBag::DISPOSITION_INLINE,
            // or download the file instead of displaying it in the browser with
            // contentDisposition: ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            status: 200,
            headers: []
        );
    }
}
