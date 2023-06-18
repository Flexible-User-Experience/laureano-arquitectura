<?php

namespace App\EventListener;

use App\Enum\EnvironmentEnum;
use App\Event\ProviderTotalInvoicedUpdateEvent;
use App\Repository\ProviderRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProviderTotalInvoicedUpdateEventListener implements EventSubscriberInterface
{
    private ProviderRepository $providerRepository;

    public function __construct(ProviderRepository $providerRepository)
    {
        $this->providerRepository = $providerRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ProviderTotalInvoicedUpdateEvent::UPDATE => 'update',
        ];
    }

    public function update(ProviderTotalInvoicedUpdateEvent $event): void
    {
        if (PHP_SAPI !== EnvironmentEnum::CLI->value) {
            $provider = $event->getProvider();
            $provider->setTotalInvoicedAmount($provider->getTotalInvoicedAmount() + $event->getTotalInvoicedDelta());
            $this->providerRepository->update(true);
        }
    }
}
