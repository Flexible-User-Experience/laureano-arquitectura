<?php

namespace App\Controller\Admin;

use App\Entity\Expense;
use App\Repository\ExpenseRepository;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

final class ExpenseAdminController extends CRUDController
{
    public function duplicateAction(int $id, ExpenseRepository $expenseRepository): RedirectResponse
    {
        /** @var Expense $expense */
        $expense = $this->admin->getSubject();
        if (!$expense) {
            throw $this->createNotFoundException(sprintf('Unable to find the object with id: %s', $id));
        }
        $newExpense = new Expense();
        $newExpense
            ->setDate(new \DateTimeImmutable())
            ->setProvider($expense->getProvider())
            ->setCategory($expense->getCategory())
            ->setExpenseCategory($expense->getExpenseCategory())
            ->setDescription($expense->getDescription())
            ->setTaxPercentage($expense->getTaxPercentage())
            ->setTaxBaseAmount($expense->getTaxBaseAmount())
            ->setTaxBaseCurrency($expense->getTaxBaseCurrency())
            ->setTotalAmount($expense->getTotalAmount())
            ->setTotalCurrency($expense->getTotalCurrency())
            ->setHasBeenPaid(false)
        ;
        $expenseRepository->add($newExpense, true);
        $this->addFlash(
            'sonata_flash_success',
            $this->trans('Duplicated Expense Success Flash Message')
        );

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    public function pdfAction(): Response
    {
        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}
