<?php

namespace App\Form\Type;

use App\Entity\ContactMessage;
use EWZ\Bundle\RecaptchaBundle\Form\Type\EWZRecaptchaV3Type;
use EWZ\Bundle\RecaptchaBundle\Validator\Constraints\IsTrueV3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactMessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'mobileNumber',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'message',
                TextareaType::class,
                [
                    'required' => true,
                    'attr' => [
                        'rows' => 5,
                    ],
                ]
            )
            ->add(
                'recaptcha',
                EWZRecaptchaV3Type::class,
                [
                    'action_name' => 'contact',
                    'mapped' => false,
                    'constraints' => [
                        new IsTrueV3(),
                    ],
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ContactMessage::class,
            'labs' => ContactMessage::class,
        ]);
    }
}
