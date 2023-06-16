<?php

namespace App\Controller\Admin;

use App\Entity\ContactMessage;
use App\Manager\MailerManager;
use App\Repository\ContactMessageRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

final class ContactMessageAdminController extends CRUDController
{
    private ManagerRegistry $mr;

    public function __construct(ManagerRegistry $mr)
    {
        $this->mr = $mr;
    }

    public function showAction(Request $request): Response
    {
        /** @var ContactMessage $object */
        $object = $this->assertObjectExists($request, true);
        \assert(null !== $object);
        $this->checkParentChildAssociation($request, $object);
        $this->admin->checkAccess('show', $object);
        $object->setHasBeenRead(true);

        $this->mr->getManager()->persist($object);
        $this->mr->getManager()->flush();

        return parent::showAction($request);
    }

    public function replyAction(int $id, ContactMessageRepository $cmr, MailerManager $mm): RedirectResponse
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
        $cmr->update(true);
        $result = true;
        try {
            $mm->sendContactMessageReplyToPotentialCustomerNotification($contactMessage);
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
