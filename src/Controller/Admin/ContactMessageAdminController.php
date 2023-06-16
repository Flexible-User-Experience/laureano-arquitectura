<?php

namespace App\Controller\Admin;

use App\Entity\ContactMessage;
use App\Form\Type\ContactMessageAnswerType;
use App\Manager\MailerManager;
use App\Repository\ContactMessageRepository;
use Doctrine\Persistence\ManagerRegistry;
use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

    public function replyAction(Request $request, ContactMessageRepository $cmr, MailerManager $mm): Response
    {
        /** @var ContactMessage $object */
        $object = $this->assertObjectExists($request, true);
        \assert(null !== $object);
        $this->checkParentChildAssociation($request, $object);
        $this->admin->checkAccess('show', $object);
        $form = $this->createForm(ContactMessageAnswerType::class, $object);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // persist new contact message form record
            $object
                ->setHasBeenRead(true)
                ->setHasBeenReplied(true)
                ->setReplyDate(new \DateTimeImmutable())
            ;
            $cmr->update(true);
            // send notifications
            $mm->sendContactMessageReplyToPotentialCustomerNotification($object);
            // build flash message
            $this->addFlash(
                'sonata_flash_success',
                $this->trans(
                    'Contact Message Reply Sent Success Flash Message',
                    [
                        '%email%' => $object->getEmail(),
                    ]
                )
            );

            return new RedirectResponse($this->admin->generateUrl('list'));
        }

        return $this->renderWithExtraParams(
            'admin/contact_message/reply_answer.html.twig',
            [
                'action' => 'reply',
                'object' => $object,
                'form' => $form->createView(),
                'elements' => $this->admin->getShow(),
            ]
        );
    }
}
