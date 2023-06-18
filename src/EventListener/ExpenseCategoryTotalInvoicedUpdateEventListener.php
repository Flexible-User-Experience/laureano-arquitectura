<?php

namespace App\EventListener;

use App\Enum\EnvironmentEnum;
use App\Event\ExpenseCategoryTotalInvoicedUpdateEvent;
use App\Repository\ExpenseCategoryRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ExpenseCategoryTotalInvoicedUpdateEventListener implements EventSubscriberInterface
{
    private ExpenseCategoryRepository $expenseCategoryRepository;

    public function __construct(ExpenseCategoryRepository $expenseCategoryRepository)
    {
        $this->expenseCategoryRepository = $expenseCategoryRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ExpenseCategoryTotalInvoicedUpdateEvent::UPDATE => 'update',
        ];
    }

    public function update(ExpenseCategoryTotalInvoicedUpdateEvent $event): void
    {
        if (PHP_SAPI !== EnvironmentEnum::CLI->value) {
            $expenseCategory = $event->getExpenseCategory();
            $expenseCategory->setTotalInvoicedAmount($expenseCategory->getTotalInvoicedAmount() + $event->getTotalInvoicedDelta());
            $this->expenseCategoryRepository->update(true);
        }
    }
}
