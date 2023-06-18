<?php

namespace App\Manager;

use App\Entity\Invoice;
use App\Entity\InvoiceLine;
use App\Repository\InvoiceLineRepository;
use App\Repository\InvoiceRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class InvoiceManager
{
    private InvoiceRepository $invoiceRepository;
    private InvoiceLineRepository $invoiceLineRepository;
    private MailerManager $mailerManager;

    public function __construct(InvoiceRepository $invoiceRepository, InvoiceLineRepository $invoiceLineRepository, MailerManager $mailerManager)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->invoiceLineRepository = $invoiceLineRepository;
        $this->mailerManager = $mailerManager;
    }

    public function getCurrentSeries(): string
    {
        return (new \DateTimeImmutable())->format('Y');
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getCurrentNumberBySeries(string $series): int
    {
        $result = 0;
        $lastInvoice = $this->invoiceRepository->getLastInvoiceBySeries($series);
        if ($lastInvoice) {
            $result = $lastInvoice->getNumber();
        }

        return $result + 1;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getCurrentDateBySeries(string $series): \DateTimeInterface
    {
        $result = new \DateTimeImmutable();
        $lastInvoice = $this->invoiceRepository->getLastInvoiceBySeries($series);
        if ($lastInvoice && $lastInvoice->getDate() > $result) {
            $result = $lastInvoice->getDate();
        }

        return $result;
    }

    public function duplicateInvoiceAndPersist(Invoice $invoiceToBeDuplicated): Invoice
    {
        $newInvoice = new Invoice();
        $newInvoice
            ->setCustomer($invoiceToBeDuplicated->getCustomer())
            ->setDate($invoiceToBeDuplicated->getDate())
            ->setPaymentMethod($invoiceToBeDuplicated->getPaymentMethod())
            ->setPaymentComments($invoiceToBeDuplicated->getPaymentComments())
            ->setIsIntraCommunityInvoice($invoiceToBeDuplicated->isIntraCommunityInvoice())
            ->setTaxPercentage($invoiceToBeDuplicated->getTaxPercentage())
            ->setDiscountPercentage($invoiceToBeDuplicated->getDiscountPercentage())
            ->setTaxBase($invoiceToBeDuplicated->getTaxBase())
            ->setTotal($invoiceToBeDuplicated->getTotal())
        ;
        $this->invoiceRepository->add($newInvoice, true);
        /** @var InvoiceLine $line */
        foreach ($invoiceToBeDuplicated->getInvoiceLines() as $line) {
            $newInvoiceLine = new InvoiceLine();
            $newInvoiceLine
                ->setInvoice($newInvoice)
                ->setUnits($line->getUnits())
                ->setUnitPrice($line->getUnitPrice())
                ->setDescription($line->getDescription())
                ->setDiscount($line->getDiscount())
                ->setTotal($line->getTotal())
            ;
            $this->invoiceLineRepository->add($newInvoiceLine, true);
        }

        return $newInvoice;
    }

    public function sendNewInvoiceToCustomerNotificationAndUpdate(Invoice $invoiceToBeSend): bool
    {
        $invoiceToBeSend
            ->setHasBeenSent(true)
            ->setSendDate(new \DateTimeImmutable())
        ;
        $this->invoiceRepository->update(true);
        $result = true;
        try {
            $this->mailerManager->sendNewInvoiceNotificationToCustomer($invoiceToBeSend);
        } catch (TransportExceptionInterface|\ReflectionException) {
            $result = false;
        }

        return $result;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getPreviousInvoice(Invoice $invoice): ?Invoice
    {
        return $this->invoiceRepository->getPreviousInvoice($invoice);
    }

    /**
     * @throws NonUniqueResultException
     */
    public function getNextInvoice(Invoice $invoice): ?Invoice
    {
        return $this->invoiceRepository->getNextInvoice($invoice);
    }
}
