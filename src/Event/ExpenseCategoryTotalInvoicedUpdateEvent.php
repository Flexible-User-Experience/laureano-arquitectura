<?php

namespace App\Event;

use App\Entity\ExpenseCategory;
use Symfony\Contracts\EventDispatcher\Event;

class ExpenseCategoryTotalInvoicedUpdateEvent extends Event
{
    public const UPDATE = 'expense_category.total_invoiced_update';

    private ExpenseCategory $expenseCategory;
    private int $totalInvoicedDelta;

    public function __construct(ExpenseCategory $expenseCategory, int $totalInvoicedDelta)
    {
        $this->expenseCategory = $expenseCategory;
        $this->totalInvoicedDelta = $totalInvoicedDelta;
    }

    public function getExpenseCategory(): ExpenseCategory
    {
        return $this->expenseCategory;
    }

    public function getTotalInvoicedDelta(): int
    {
        return $this->totalInvoicedDelta;
    }
}
