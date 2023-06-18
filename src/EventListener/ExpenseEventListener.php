<?php

namespace App\EventListener;

use App\Entity\Expense;
use App\Enum\EnvironmentEnum;
use App\Event\ExpenseCategoryTotalInvoicedUpdateEvent;
use App\Event\ProviderTotalInvoicedUpdateEvent;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ExpenseEventListener
{
    private EventDispatcherInterface $dispatcher;
    private ?ProviderTotalInvoicedUpdateEvent $providerTotalInvoicedUpdateEvent = null;
    private ?ProviderTotalInvoicedUpdateEvent $secondProviderTotalInvoicedUpdateEvent = null;
    private ?ExpenseCategoryTotalInvoicedUpdateEvent $expenseCategoryTotalInvoicedUpdateEvent = null;
    private ?ExpenseCategoryTotalInvoicedUpdateEvent $secondExpenseCategoryTotalInvoicedUpdateEvent = null;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function prePersist(Expense $expense): void
    {
        if (PHP_SAPI !== EnvironmentEnum::CLI->value && $expense->getProvider()) {
            $providerTotalInvoicedUpdateEvent = new ProviderTotalInvoicedUpdateEvent($expense->getProvider(), $expense->getTaxBaseAmount());
            $this->dispatcher->dispatch($providerTotalInvoicedUpdateEvent, ProviderTotalInvoicedUpdateEvent::UPDATE);
            if ($expense->getExpenseCategory()) {
                $expenseCategoryUpdateEvent = new ExpenseCategoryTotalInvoicedUpdateEvent($expense->getExpenseCategory(), $expense->getTaxBaseAmount());
                $this->dispatcher->dispatch($expenseCategoryUpdateEvent, ExpenseCategoryTotalInvoicedUpdateEvent::UPDATE);
            }
        }
    }

    public function preUpdate(Expense $expense, PreUpdateEventArgs $args): void
    {
        $changeSet = $args->getEntityChangeSet();
        if (PHP_SAPI !== EnvironmentEnum::CLI->value) {
            if (array_key_exists('taxBaseAmount', $changeSet) && array_key_exists('provider', $changeSet)) {
                // changes amount & provider
                if ($args->getOldValue('provider')) {
                    $this->providerTotalInvoicedUpdateEvent = new ProviderTotalInvoicedUpdateEvent($args->getOldValue('provider'), (int) -$args->getOldValue('taxBaseAmount'));
                }
                if ($args->getNewValue('provider')) {
                    $this->secondProviderTotalInvoicedUpdateEvent = new ProviderTotalInvoicedUpdateEvent($args->getNewValue('provider'), (int) $args->getNewValue('taxBaseAmount'));
                }
            } elseif (array_key_exists('taxBaseAmount', $changeSet) && !array_key_exists('provider', $changeSet) && $expense->getProvider()) {
                // only changes amount
                $delta = (int) $args->getNewValue('taxBaseAmount') - (int) $args->getOldValue('taxBaseAmount');
                $this->providerTotalInvoicedUpdateEvent = new ProviderTotalInvoicedUpdateEvent($expense->getProvider(), $delta);
            } elseif (!array_key_exists('taxBaseAmount', $changeSet) && array_key_exists('provider', $changeSet)) {
                // only changes provider
                $delta = $expense->getTaxBaseAmount();
                if ($args->getOldValue('provider')) {
                    $this->providerTotalInvoicedUpdateEvent = new ProviderTotalInvoicedUpdateEvent($args->getOldValue('provider'), -$delta);
                }
                if ($args->getNewValue('provider')) {
                    $this->secondProviderTotalInvoicedUpdateEvent = new ProviderTotalInvoicedUpdateEvent($args->getNewValue('provider'), $delta);
                }
            }
            if (array_key_exists('expenseCategory', $changeSet)) {
                if ($args->getOldValue('expenseCategory') && $args->getNewValue('expenseCategory')) {
                    // update both expenses amount
                    $this->expenseCategoryTotalInvoicedUpdateEvent = new ExpenseCategoryTotalInvoicedUpdateEvent($args->getOldValue('expenseCategory'), -$expense->getTaxBaseAmount());
                    $this->secondExpenseCategoryTotalInvoicedUpdateEvent = new ExpenseCategoryTotalInvoicedUpdateEvent($args->getNewValue('expenseCategory'), $expense->getTaxBaseAmount());
                } elseif (!$args->getOldValue('expenseCategory') && $args->getNewValue('expenseCategory')) {
                    // add new expense amount
                    $this->expenseCategoryTotalInvoicedUpdateEvent = new ExpenseCategoryTotalInvoicedUpdateEvent($args->getNewValue('expenseCategory'), $expense->getTaxBaseAmount());
                } elseif ($args->getOldValue('expenseCategory') && !$args->getNewValue('expenseCategory')) {
                    // remove old expense amount
                    $this->expenseCategoryTotalInvoicedUpdateEvent = new ExpenseCategoryTotalInvoicedUpdateEvent($args->getOldValue('expenseCategory'), -$expense->getTaxBaseAmount());
                }
            }
        }
    }

    public function postUpdate(): void
    {
        if (PHP_SAPI !== EnvironmentEnum::CLI->value) {
            if ($this->providerTotalInvoicedUpdateEvent) {
                $this->dispatcher->dispatch($this->providerTotalInvoicedUpdateEvent, ProviderTotalInvoicedUpdateEvent::UPDATE);
            }
            if ($this->secondProviderTotalInvoicedUpdateEvent) {
                $this->dispatcher->dispatch($this->secondProviderTotalInvoicedUpdateEvent, ProviderTotalInvoicedUpdateEvent::UPDATE);
            }
            if ($this->expenseCategoryTotalInvoicedUpdateEvent) {
                $this->dispatcher->dispatch($this->expenseCategoryTotalInvoicedUpdateEvent, ExpenseCategoryTotalInvoicedUpdateEvent::UPDATE);
            }
            if ($this->secondExpenseCategoryTotalInvoicedUpdateEvent) {
                $this->dispatcher->dispatch($this->secondExpenseCategoryTotalInvoicedUpdateEvent, ExpenseCategoryTotalInvoicedUpdateEvent::UPDATE);
            }
        }
    }

    public function postRemove(Expense $expense): void
    {
        if (PHP_SAPI !== EnvironmentEnum::CLI->value) {
            $providerTotalInvoicedUpdateEvent = new ProviderTotalInvoicedUpdateEvent($expense->getProvider(), -$expense->getTaxBaseAmount());
            $this->dispatcher->dispatch($providerTotalInvoicedUpdateEvent, ProviderTotalInvoicedUpdateEvent::UPDATE);
            if ($expense->getExpenseCategory()) {
                $expenseCategoryUpdateEvent = new ExpenseCategoryTotalInvoicedUpdateEvent($expense->getExpenseCategory(), -$expense->getTaxBaseAmount());
                $this->dispatcher->dispatch($expenseCategoryUpdateEvent, ExpenseCategoryTotalInvoicedUpdateEvent::UPDATE);
            }
        }
    }
}
