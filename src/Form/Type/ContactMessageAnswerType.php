<?php

namespace App\Form\Type;

use App\Entity\ContactMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactMessageAnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'replyMessage',
                TextareaType::class,
                [
                    'label' => 'Reply Message',
                    'required' => true,
                    'attr' => [
                        'rows' => 10,
                        'style' => 'margin-bottom: 10px',
                    ],
                ]
            )
            ->add(
                'send',
                SubmitType::class,
                [
                    'attr' => [
                        'style' => 'margin-bottom: 0',
                        'class' => 'btn btn-primary',
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => ContactMessage::class,
            ]
        );
    }
}
