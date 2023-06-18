<?php

namespace App\Controller\Admin;

use App\Entity\Invoice;
use App\Manager\AssetsManager;
use App\Manager\InvoiceManager;
use App\Pdf\Builder\InvoicePdfBuilder;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

final class InvoiceAdminController extends CRUDController
{
    public function duplicateAction(int $id, InvoiceManager $invoiceManager): RedirectResponse
    {
        /** @var Invoice $invoice */
        $invoice = $this->admin->getSubject();
        if (!$invoice) {
            throw $this->createNotFoundException(sprintf('Unable to find the object with id: %s', $id));
        }
        $invoiceManager->duplicateInvoiceAndPersist($invoice);
        $this->addFlash(
            'sonata_flash_success',
            $this->trans(
                'Duplicated Invoice Success Flash Message',
                [
                    '%num%' => $invoice->getSluggedSerialNumber(),
                ]
            )
        );

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * @throws \ReflectionException
     */
    public function pdfAction(int $id, InvoicePdfBuilder $invoicePdfBuilder, TranslatorInterface $ts, ParameterBagInterface $pb): Response
    {
        /** @var Invoice $invoice */
        $invoice = $this->admin->getSubject();
        if (!$invoice) {
            throw $this->createNotFoundException(sprintf('Unable to find the object with id: %s', $id));
        }
        $pdf = $invoicePdfBuilder->build($invoice);

        return new Response(
            $pdf->Output(
                $pb->get('project_web_title').'_'.strtolower($ts->trans('Invoice')).'_'.$invoice->getSluggedSerialNumber().'.pdf'
            ),
            200,
            [
                'Content-type' => AssetsManager::MIME_APPLICATION_PDF_TYPE,
            ]
        );
    }

    public function emailAction(int $id, InvoiceManager $invoiceManager): RedirectResponse
    {
        /** @var Invoice $invoice */
        $invoice = $this->admin->getSubject();
        if (!$invoice) {
            throw $this->createNotFoundException(sprintf('Unable to find the object with id: %s', $id));
        }
        $result = $invoiceManager->sendNewInvoiceToCustomerNotificationAndUpdate($invoice);
        if ($result) {
            $this->addFlash(
                'sonata_flash_success',
                $this->trans(
                    'Invoice Sent Success Flash Message',
                    [
                        '%num%' => $invoice->getSluggedSerialNumber(),
                        '%email%' => $invoice->getCustomer()->getEmail(),
                    ]
                )
            );
        } else {
            $this->addFlash(
                'sonata_flash_error',
                $this->trans(
                    'Invoice Sent Error Flash Message',
                    [
                        '%num%' => $invoice->getSluggedSerialNumber(),
                        '%email%' => $invoice->getCustomer()->getEmail(),
                    ]
                )
            );
        }

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}
