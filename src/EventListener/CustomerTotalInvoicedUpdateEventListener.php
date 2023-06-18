<?php

namespace App\EventListener;

use App\Enum\EnvironmentEnum;
use App\Event\CustomerTotalInvoicedUpdateEvent;
use App\Repository\CustomerRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class CustomerTotalInvoicedUpdateEventListener implements EventSubscriberInterface
{
    private CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            CustomerTotalInvoicedUpdateEvent::UPDATE => 'update',
        ];
    }

    public function update(CustomerTotalInvoicedUpdateEvent $event): void
    {
        if (PHP_SAPI !== EnvironmentEnum::CLI->value) {
            $customer = $event->getCustomer();
            $customer->setTotalInvoicedAmount($customer->getTotalInvoicedAmount() + $event->getTotalInvoicedDelta());
            $this->customerRepository->update(true);
        }
    }
}
