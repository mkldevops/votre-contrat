<?php

namespace App\Contract;

use App\Entity\Contract;
use App\Exception\AppException;
use Pontedilana\PhpWeasyPrint\Pdf;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

readonly class ContractPdfHandler
{
    public function __construct(
        private Environment $twig,
        private Pdf $weasyPrint,
        #[Autowire('%contract_dir%')]
        private string $contractDir,
    ) {
    }

    /**
     * @throws AppException
     */
    public function pdfContent(Contract $contract): string
    {
        if (file_exists($this->getPdfPath($contract))) {
            return (new File($this->getPdfPath($contract)))->getContent();
        }

        return $this->weasyPrint->getOutputFromHtml($this->htmlContent($contract));
    }

    /**
     * @throws AppException
     */
    public function htmlContent(Contract $contract): string
    {
        if (null === ($template = $contract->getFormation()?->getTemplate()->getTemplatePath())) {
            throw new NotFoundHttpException('Template not found');
        }

        try {
            return $this->twig->render(
                name: $template,
                context : [
                    'contract' => $contract,
                ]
            );
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            throw new AppException('Error rendering the template', previous: $e);
        }
    }

    /**
     * @throws AppException
     */
    public function savePdf(Contract $contract, bool $overwrite = false): File
    {
        $pdfPath = $this->getPdfPath($contract);

        if (file_exists($pdfPath) && !$overwrite) {
            return new File($pdfPath);
        }

        file_put_contents($pdfPath, $this->pdfContent($contract));

        return new File($pdfPath);
    }

    public function getPdfPath(Contract $contract): string
    {
        if (null !== $contract->getFileContract()) {
            return $contract->getFileContract();
        }

        return sprintf('%s/%s.pdf', $this->contractDir, $contract->getId()?->__toString());
    }
}
