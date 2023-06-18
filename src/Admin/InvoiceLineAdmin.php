<?php

namespace App\Admin;

use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Tbbc\MoneyBundle\Form\Type\SimpleMoneyType;

final class InvoiceLineAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'InvoiceLine';
    protected $baseRoutePattern = 'enterprise/invoice-line';

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('invoice')
            ->add('units')
            ->add('unitPriceAmount')
            ->add('description')
            ->add('discount')
            ->add('totalAmount')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('invoice')
            ->add('units')
            ->add('unitPriceAmount')
            ->add('description')
            ->add('discount')
            ->add('totalAmount')
            ->add(
                ListMapper::NAME_ACTIONS,
                ListMapper::TYPE_ACTIONS,
                [
                    'actions' => [
                        'show' => [],
                        'edit' => [],
                    ],
                    'header_style' => 'width:86px',
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                ]
            )
        ;
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add(
                'invoice',
                null,
                [
                    'attr' => [
                        'hidden' => true,
                    ],
                    'required' => true,
                ]
            )
            ->add(
                'units',
                NumberType::class,
                [
                    'required' => true,
                    'scale' => 2,
                ]
            )
            ->add(
                'description',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'unitPrice',
                SimpleMoneyType::class,
                [
                    'required' => true,
                    'disabled' => false,
                    'amount_options' => [
                        'label' => false,
                    ],
                ]
            )
            ->add(
                'total',
                SimpleMoneyType::class,
                [
                    'required' => true,
                    'disabled' => true,
                    'amount_options' => [
                        'label' => false,
                    ],
                ]
            )
        ;
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('units')
            ->add('unitPriceAmount')
            ->add('unitPriceCurrency')
            ->add('description')
            ->add('discount')
            ->add('totalAmount')
            ->add('totalCurrency')
        ;
    }
}
