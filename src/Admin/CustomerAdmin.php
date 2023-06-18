<?php

namespace App\Admin;

use App\Enum\LocaleEnum;
use App\Enum\SortOrderEnum;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

class CustomerAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'Customer';
    protected $baseRoutePattern = 'enterprise/customer';

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        parent::configureDefaultSortValues($sortValues);
        $sortValues[DatagridInterface::SORT_ORDER] = SortOrderEnum::ASCENDING->value;
        $sortValues[DatagridInterface::SORT_BY] = 'fiscalName';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection->remove('show');
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('fiscalIdentificationCode')
            ->add('fiscalName')
            ->add('commercialName')
            ->add('email')
            ->add('phoneNumber')
            ->add('mobileNumber')
            ->add('website')
            ->add('address1')
            ->add('address2')
            ->add('postalCode')
            ->add('city')
            ->add('state')
            ->add(
                'country',
                null,
                [
                    'field_type' => CountryType::class,
                    'field_options' => [
                        'preferred_choices' => [strtoupper(LocaleEnum::ES->value)],
                        'required' => true,
                        'multiple' => false,
                        'expanded' => false,
                    ],
                ]
            )
            ->add(
                'locale',
                null,
                [
                    'field_type' => ChoiceType::class,
                    'field_options' => [
                        'choices' => LocaleEnum::getChoices(),
                        'expanded' => false,
                        'multiple' => false,
                    ],
                ]
            )
            ->add('isCompany')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add(
                'fiscalIdentificationCode',
                FieldDescriptionInterface::TYPE_STRING,
                [
                    'editable' => true,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                ]
            )
            ->add(
                'fiscalName',
                FieldDescriptionInterface::TYPE_STRING,
                [
                    'editable' => true,
                ]
            )
            ->add(
                'email',
                FieldDescriptionInterface::TYPE_STRING,
                [
                    'editable' => true,
                ]
            )
            ->add(
                'mobileNumber',
                FieldDescriptionInterface::TYPE_STRING,
                [
                    'editable' => true,
                ]
            )
            ->add(
                'city',
                FieldDescriptionInterface::TYPE_STRING,
                [
                    'editable' => true,
                ]
            )
            ->add(
                'totalInvoicedAmount',
                null,
                [
                    'editable' => false,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                    'template' => '@App/admin/cells/list__cell_total_invoiced_money_field.html.twig',
                ]
            )
            ->add(
                'isCompany',
                FieldDescriptionInterface::TYPE_BOOLEAN,
                [
                    'editable' => true,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                ]
            )
            ->add(
                ListMapper::NAME_ACTIONS,
                ListMapper::TYPE_ACTIONS,
                [
                    'actions' => [
                        'edit' => [],
                    ],
                    'header_style' => 'width:63px',
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                ]
            )
        ;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with(
                'General',
                [
                    'class' => 'col-md-4',
                    'box_class' => 'box box-success',
                ]
            )
            ->add('fiscalIdentificationCode')
            ->add('fiscalName')
            ->add('commercialName')
            ->end()
            ->with(
                'Contact',
                [
                    'class' => 'col-md-4',
                    'box_class' => 'box box-success',
                ]
            )
            ->add('email')
            ->add('phoneNumber')
            ->add('mobileNumber')
            ->add(
                'website',
                UrlType::class,
                [
                    'default_protocol' => 'https',
                    'required' => false,
                ]
            )
            ->end()
            ->with(
                'Address',
                [
                    'class' => 'col-md-4',
                    'box_class' => 'box box-success',
                ]
            )
            ->add('address1')
            ->add('address2')
            ->add('postalCode')
            ->add('city')
            ->add('state')
            ->add(
                'country',
                CountryType::class,
                [
                    'preferred_choices' => [strtoupper(LocaleEnum::ES->value)],
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                ]
            )
            ->end()
            ->with(
                'Controls',
                [
                    'class' => 'col-md-4',
                    'box_class' => 'box box-success',
                ]
            )
            ->add(
                'locale',
                ChoiceType::class,
                [
                    'preferred_choices' => [
                        LocaleEnum::CA->value
                    ],
                    'choices' => LocaleEnum::getChoices(),
                    'required' => true,
                    'multiple' => false,
                    'expanded' => false,
                ]
            )
            ->add(
                'isCompany',
                null,
                [
                    'required' => false,
                ]
            )
            ->end()
        ;
    }
}
