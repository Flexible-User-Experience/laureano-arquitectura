<?php

namespace App\Manager;

use App\Entity\ContactMessage;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class MailerManager
{
    private TranslatorInterface $translator;
    private MailerInterface $mailer;
    private ParameterBagInterface $parameterBag;
    private RouterInterface $router;
    private Environment $twig;

    public function __construct(TranslatorInterface $translator, MailerInterface $mailer, ParameterBagInterface $parameterBag, RouterInterface $router, Environment $twig)
    {
        $this->translator = $translator;
        $this->mailer = $mailer;
        $this->parameterBag = $parameterBag;
        $this->router = $router;
        $this->twig = $twig;
    }

    /**
     * @throws SyntaxError
     * @throws TransportExceptionInterface
     * @throws RuntimeError
     * @throws LoaderError
     */
    public function sendNewContactMessageFromNotificationToManager(ContactMessage $contactMessage): void
    {
        $email = (new NotificationEmail())
            ->from(new Address($this->parameterBag->get('mailer_destination'), $this->parameterBag->get('project_web_title')))
            ->to($this->parameterBag->get('mailer_destination'))
            ->importance(NotificationEmail::IMPORTANCE_HIGH)
            ->subject($this->translator->trans('Message contact web form'))
            ->action(
                $this->translator->trans('Reply'),
                $this->router->generate(
                    'admin_app_contactmessage_reply',
                    [
                        'id' => $contactMessage->getId(),
                    ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                )
            )
            ->markdown(
                $this->twig->render(
                    '@App/mail/new_contact_message_form_notification_to_manager.md.twig',
                    [
                        'contact' => $contactMessage,
                    ]
                )
            )
        ;
        $this->mailer->send($email);
    }

    /**
     * @throws TransportExceptionInterface
     */
//    public function sendContactMessageReplyToPotentialCustomerNotification(ContactMessage $contactMessage): void
//    {
//        $email = (new TemplatedEmail())
//            ->from(new Address($this->parameterBag->get('mailer_destination'), $this->parameterBag->get('project_web_title')))
//            ->to(new Address($contactMessage->getEmail(), $contactMessage->getName()))
//            ->subject($this->translator->trans('Contact message answer').' '.$this->parameterBag->get('project_web_title'))
//            ->htmlTemplate('@App/Mail/contact_message_reply_notification_to_potential_customer.html.twig')
//            ->context([
//                'contact' => $contactMessage,
//            ])
//        ;
//        $this->mailer->send($email);
//    }
}
