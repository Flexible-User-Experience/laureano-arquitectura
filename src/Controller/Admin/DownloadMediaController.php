<?php

namespace App\Controller\Admin;

use App\Entity\Expense;
use App\Repository\ExpenseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Vich\UploaderBundle\Handler\DownloadHandler;

#[Route('/admin/download-media')]
final class DownloadMediaController extends AbstractController
{
    #[Route('/expense/media-inline/{id}', name: 'download_media_inline_expense_document')]
    public function mediaInlineSpendingAction(int $id, DownloadHandler $downloadHandler, ExpenseRepository $expenseRepository): Response
    {
        return $downloadHandler->downloadObject($expenseRepository->find($id), 'document', Expense::class, true, false);
    }
}
