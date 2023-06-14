<?php

namespace App\Controller\Admin;

use App\Entity\ContactMessage;
use App\Manager\MailerManager;
use App\Repository\ContactMessageRepository;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

final class ContactMessageAdminController extends CRUDController
{
    public function replyAction(int $id, ContactMessageRepository $contactMessageRepository, MailerManager $mailerManager): RedirectResponse
    {
        /** @var ContactMessage $contactMessage */
        $contactMessage = $this->admin->getSubject();
        if (!$contactMessage) {
            throw $this->createNotFoundException(sprintf('Unable to find the object with id: %s', $id));
        }
        $contactMessage
            ->setHasBeenReplied(true)
            ->setReplyDate(new \DateTimeImmutable())
        ;
        $contactMessageRepository->update(true);
        $result = true;
        try {
            // TODO
            $mailerManager->sendContactMessageReplyToPotentialCustomerNotification($contactMessage);
        } catch (TransportExceptionInterface) {
            $result = false;
        }
        if ($result) {
            $this->addFlash(
                'sonata_flash_success',
                $this->trans(
                    'Contact Message Reply Sent Success Flash Message',
                    [
                        '%num%' => $contactMessage->getId(),
                        '%email%' => $contactMessage->getEmail(),
                    ]
                )
            );
        } else {
            $this->addFlash(
                'sonata_flash_error',
                $this->trans(
                    'Contact Message Reply Sent Error Flash Message',
                    [
                        '%num%' => $contactMessage->getId(),
                        '%email%' => $contactMessage->getEmail(),
                    ]
                )
            );
        }

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}
