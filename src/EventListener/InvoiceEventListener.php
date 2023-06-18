<?php

namespace App\EventListener;

use App\Entity\Invoice;
use App\Enum\EnvironmentEnum;
use App\Event\CustomerTotalInvoicedUpdateEvent;
use App\Manager\InvoiceManager;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class InvoiceEventListener
{
    private InvoiceManager $invoiceManager;
    private EventDispatcherInterface $dispatcher;
    private ?CustomerTotalInvoicedUpdateEvent $customerTotalInvoicedUpdateEvent = null;
    private ?CustomerTotalInvoicedUpdateEvent $secondCustomerTotalInvoicedUpdateEvent = null;

    public function __construct(InvoiceManager $invoiceManager, EventDispatcherInterface $dispatcher)
    {
        $this->invoiceManager = $invoiceManager;
        $this->dispatcher = $dispatcher;
    }

    public function prePersist(Invoice $invoice): void
    {
        if (PHP_SAPI !== EnvironmentEnum::CLI->value) {
            $series = $this->invoiceManager->getCurrentSeries();
            $number = $this->invoiceManager->getCurrentNumberBySeries($series);
            $date = $this->invoiceManager->getCurrentDateBySeries($series);
            $invoice
                ->setSeries($series)
                ->setNumber($number)
            ;
            if ($invoice->getDate() < $date) {
                $invoice->setDate($date);
            }
            $customerTotalInvoicedUpdateEvent = new CustomerTotalInvoicedUpdateEvent($invoice->getCustomer(), $invoice->getTaxBaseAmount());
            $this->dispatcher->dispatch($customerTotalInvoicedUpdateEvent, CustomerTotalInvoicedUpdateEvent::UPDATE);
        }
    }

    public function preUpdate(Invoice $invoice, PreUpdateEventArgs $args): void
    {
        $changeSet = $args->getEntityChangeSet();
        if (PHP_SAPI !== EnvironmentEnum::CLI->value) {
            if (array_key_exists('taxBaseAmount', $changeSet) && array_key_exists('customer', $changeSet)) {
                // changes amount & customer
                $this->customerTotalInvoicedUpdateEvent = new CustomerTotalInvoicedUpdateEvent($args->getOldValue('customer'), (int) -$args->getOldValue('taxBaseAmount'));
                $this->secondCustomerTotalInvoicedUpdateEvent = new CustomerTotalInvoicedUpdateEvent($args->getNewValue('customer'), (int) $args->getNewValue('taxBaseAmount'));
            } elseif (array_key_exists('taxBaseAmount', $changeSet) && !array_key_exists('customer', $changeSet)) {
                // only changes amount
                $delta = (int) $args->getNewValue('taxBaseAmount') - (int) $args->getOldValue('taxBaseAmount');
                $this->customerTotalInvoicedUpdateEvent = new CustomerTotalInvoicedUpdateEvent($invoice->getCustomer(), $delta);
            } elseif (!array_key_exists('taxBaseAmount', $changeSet) && array_key_exists('customer', $changeSet)) {
                // only changes customer
                $delta = $invoice->getTaxBaseAmount();
                $this->customerTotalInvoicedUpdateEvent = new CustomerTotalInvoicedUpdateEvent($args->getOldValue('customer'), -$delta);
                $this->secondCustomerTotalInvoicedUpdateEvent = new CustomerTotalInvoicedUpdateEvent($args->getNewValue('customer'), $delta);
            }
        }
    }

    public function postUpdate(): void
    {
        if (PHP_SAPI !== EnvironmentEnum::CLI->value) {
            if ($this->customerTotalInvoicedUpdateEvent) {
                $this->dispatcher->dispatch($this->customerTotalInvoicedUpdateEvent, CustomerTotalInvoicedUpdateEvent::UPDATE);
            }
            if ($this->secondCustomerTotalInvoicedUpdateEvent) {
                $this->dispatcher->dispatch($this->secondCustomerTotalInvoicedUpdateEvent, CustomerTotalInvoicedUpdateEvent::UPDATE);
            }
        }
    }

    public function postRemove(Invoice $invoice): void
    {
        if (PHP_SAPI !== EnvironmentEnum::CLI->value) {
            $customerTotalInvoicedUpdateEvent = new CustomerTotalInvoicedUpdateEvent($invoice->getCustomer(), -$invoice->getTaxBaseAmount());
            $this->dispatcher->dispatch($customerTotalInvoicedUpdateEvent, CustomerTotalInvoicedUpdateEvent::UPDATE);
        }
    }
}
