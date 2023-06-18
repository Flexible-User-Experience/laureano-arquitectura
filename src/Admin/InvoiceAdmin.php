<?php

namespace App\Admin;

use App\Entity\AbstractBase;
use App\Entity\Customer;
use App\Entity\Invoice;
use App\Enum\SortOrderEnum;
use App\Manager\InvoiceManager;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\Form\Type\CollectionType;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Tbbc\MoneyBundle\Form\Type\SimpleMoneyType;

final class InvoiceAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'Invoice';
    protected $baseRoutePattern = 'enterprise/invoice';
    private InvoiceManager $invoiceManager;

    public function getInvoiceManager(): InvoiceManager
    {
        return $this->invoiceManager;
    }

    public function setInvoiceManager(InvoiceManager $invoiceManager): void
    {
        $this->invoiceManager = $invoiceManager;
    }

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        parent::configureDefaultSortValues($sortValues);
        $sortValues[DatagridInterface::SORT_ORDER] = SortOrderEnum::DESCENDING->value;
        $sortValues[DatagridInterface::SORT_BY] = 'date';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        parent::configureRoutes($collection);
        $collection
            ->add('duplicate', $this->getRouterIdParameter().'/duplicate')
            ->add('pdf', $this->getRouterIdParameter().'/pdf')
            ->add('email', $this->getRouterIdParameter().'/email')
            ->remove('show')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('series')
            ->add('number')
            ->add(
                'date',
                DateFilter::class,
                [
                    'field_type' => DatePickerType::class,
                    'field_options' => [
                        'widget' => 'single_text',
                        'format' => AbstractBase::DATE_PICKER_TYPE_FORMAT,
                    ],
                ]
            )
            ->add(
                'customer',
                ModelFilter::class,
                [
                    'field_options' => [
                        'class' => Customer::class,
                        'choice_label' => 'fiscalAndCommercialName',
                        'query_builder' => $this->getEntityManager()->getRepository(Customer::class)->getAllSortedByNameQB(),
                    ],
                ]
            )
            ->add('taxBaseAmount')
            ->add('totalAmount')
            ->add('discountPercentage')
            ->add('taxPercentage')
            ->add('hasBeenSent')
            ->add(
                'sendDate',
                DateFilter::class,
                [
                    'field_type' => DatePickerType::class,
                    'field_options' => [
                        'widget' => 'single_text',
                        'format' => AbstractBase::DATE_PICKER_TYPE_FORMAT,
                    ],
                ]
            )
            ->add('hasBeenPaid')
            ->add(
                'paymentDate',
                DateFilter::class,
                [
                    'field_type' => DatePickerType::class,
                    'field_options' => [
                        'widget' => 'single_text',
                        'format' => AbstractBase::DATE_PICKER_TYPE_FORMAT,
                    ],
                ]
            )
            ->add('isIntraCommunityInvoice')
            ->add('customerReference')
            ->add('paymentComments')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add(
                'series',
                FieldDescriptionInterface::TYPE_STRING,
                [
                    'editable' => false,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                ]
            )
            ->add(
                'number',
                FieldDescriptionInterface::TYPE_INTEGER,
                [
                    'editable' => false,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                ]
            )
            ->add(
                'date',
                FieldDescriptionInterface::TYPE_DATE,
                [
                    'format' => AbstractBase::DATE_STRING_FORMAT,
                    'editable' => false,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                ]
            )
            ->add(
                'customer',
                FieldDescriptionInterface::TYPE_MANY_TO_ONE,
                [
                    'editable' => false,
                    'sortable' => true,
                    'associated_property' => 'getCommercialOrFiscalName',
                    'route' => [
                        'name' => 'edit',
                    ],
                    'sort_field_mapping' => [
                        'fieldName' => 'fiscalName',
                    ],
                    'sort_parent_association_mappings' => [
                        [
                            'fieldName' => 'customer',
                        ],
                    ],
                ]
            )
            ->add(
                'taxBaseAmount',
                null,
                [
                    'editable' => false,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                    'template' => '@App/Admin/Cells/list__cell_tax_base_money_field.html.twig',
                ]
            )
            ->add(
                'totalAmount',
                null,
                [
                    'editable' => false,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                    'template' => '@App/Admin/Cells/list__cell_total_money_field.html.twig',
                ]
            )
            ->add(
                'hasBeenSent',
                FieldDescriptionInterface::TYPE_BOOLEAN,
                [
                    'editable' => true,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                ]
            )
            ->add(
                'hasBeenPaid',
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
                        'pdf' => [
                            'template' => '@App/Admin/Cells/list__action_invoice_pdf_button.html.twig',
                        ],
                        'email' => [
                            'template' => '@App/Admin/Cells/list__action_invoice_email_button.html.twig',
                        ],
                        'duplicate' => [
                            'template' => '@App/Admin/Cells/list__action_invoice_duplicate_button.html.twig',
                        ],
                    ],
                    'header_style' => 'width:146px',
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
        ;
        if ($this->isCurrentRoute('edit')) {
            $form
                ->add(
                    'series',
                    null,
                    [
                        'required' => true,
                    ]
                )
                ->add(
                    'number',
                    NumberType::class,
                    [
                        'required' => true,
                    ]
                )
            ;
        }
        $form
            ->add(
                'date',
                DatePickerType::class,
                [
                    'format' => AbstractBase::DATE_FORM_TYPE_FORMAT,
                    'required' => true,
                ]
            )
            ->add(
                'customer',
                EntityType::class,
                [
                    'required' => true,
                    'class' => Customer::class,
                    'choice_label' => 'fiscalAndCommercialName',
                    'query_builder' => $this->getEntityManager()->getRepository(Customer::class)->getAllSortedByNameQB(),
                ]
            )
            ->end()
            ->with(
                'Amounts',
                [
                    'class' => 'col-md-4',
                    'box_class' => 'box box-success',
                ]
            )
            ->add(
                'taxBase',
                SimpleMoneyType::class,
                [
                    'required' => false,
                    'disabled' => true,
                    'amount_options' => [
                        'label' => false,
                    ],
                ]
            )
            ->add(
                'total',
                SimpleMoneyType::class,
                [
                    'required' => false,
                    'disabled' => true,
                    'amount_options' => [
                        'label' => false,
                    ],
                ]
            )
            ->add(
                'discountPercentage',
                IntegerType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'taxPercentage',
                IntegerType::class,
                [
                    'required' => true,
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
        ;
        if ($this->isCurrentRoute('edit')) {
            $form
                ->add(
                    'hasBeenSent',
                    null,
                    [
                        'required' => false,
                        'disabled' => true,
                    ]
                )
            ;
            if ($this->getSubject()->getSendDate()) {
                $form
                    ->add(
                        'sendDate',
                        DatePickerType::class,
                        [
                            'format' => AbstractBase::DATE_FORM_TYPE_FORMAT,
                            'required' => false,
                            'disabled' => true,
                            'row_attr' => [
                                'style' => 'margin-bottom:30px',
                            ],
                        ]
                    )
                ;
            }
            $form
                ->add(
                    'hasBeenPaid',
                    null,
                    [
                        'required' => false,
                        'disabled' => false,
                    ]
                )
                ->add(
                    'paymentDate',
                    DatePickerType::class,
                    [
                        'format' => AbstractBase::DATE_FORM_TYPE_FORMAT,
                        'required' => false,
                        'disabled' => false,
                        'row_attr' => [
                            'style' => 'margin-bottom:30px',
                        ],
                    ]
                )
            ;
        }
        $form
            ->add(
                'isIntraCommunityInvoice',
                null,
                [
                    'required' => false,
                ]
            )
            ->add('customerReference')
            ->add('paymentComments')
            ->end()
            ->with(
                'Lines',
                [
                    'class' => 'col-md-12',
                    'box_class' => 'box box-success',
                ]
            )
            ->add(
                'invoiceLines',
                CollectionType::class,
                [
                    'label' => false,
                    'required' => true,
                    'error_bubbling' => true,
                    'by_reference' => false,
                ],
                [
                    'edit' => 'inline',
                    'inline' => 'table',
                ]
            )
            ->end()
        ;
    }

    /**
     * @param Invoice $object
     */
    public function prePersist($object): void
    {
        $this->commonPreActions($object);
    }

    /**
     * @param Invoice $object
     */
    public function preUpdate($object): void
    {
        $this->commonPreActions($object);
        $previousInvoice = $this->getInvoiceManager()->getPreviousInvoice($object);
        $nextInvoice = $this->getInvoiceManager()->getNextInvoice($object);
        if ($previousInvoice && $nextInvoice) {
            if ($previousInvoice->getDate() > $object->getDate()) {
                $object->setDate($nextInvoice->getDate());
            }
            if ($nextInvoice->getDate() < $object->getDate()) {
                $object->setDate($nextInvoice->getDate());
            }
        }
        if (!$nextInvoice && $previousInvoice && $previousInvoice->getDate() > $object->getDate()) {
            $object->setDate($previousInvoice->getDate());
        }
    }

    private function commonPreActions(Invoice $object): void
    {
        $object->calculateTotalBaseAmount();
    }
}
