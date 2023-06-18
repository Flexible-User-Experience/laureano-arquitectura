<?php

namespace App\Admin;

use App\Entity\AbstractBase;
use App\Entity\Expense;
use App\Entity\ExpenseCategory;
use App\Entity\Provider;
use App\Enum\SortOrderEnum;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\DoctrineORMAdminBundle\Filter\DateFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelFilter;
use Sonata\Form\Type\DatePickerType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Tbbc\MoneyBundle\Form\Type\SimpleMoneyType;
use Vich\UploaderBundle\Form\Type\VichFileType;

final class ExpenseAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'Expense';
    protected $baseRoutePattern = 'enterprise/expense';

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        parent::configureDefaultSortValues($sortValues);
        $sortValues[DatagridInterface::SORT_ORDER] = SortOrderEnum::DESCENDING->value;
        $sortValues[DatagridInterface::SORT_BY] = 'date';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->add('duplicate', $this->getRouterIdParameter().'/duplicate')
            ->add('pdf', $this->getRouterIdParameter().'/pdf')
            ->remove('batch')
            ->remove('show')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
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
                'provider',
                ModelFilter::class,
                [
                    'field_options' => [
                        'class' => Provider::class,
                        'choice_label' => 'fiscalName',
                        'query_builder' => $this->getEntityManager()->getRepository(Provider::class)->getAllSortedByNameQB(),
                    ],
                ]
            )
            ->add(
                'expenseCategory',
                ModelFilter::class,
                [
                    'field_options' => [
                        'class' => ExpenseCategory::class,
                        'choice_label' => 'name',
                        'query_builder' => $this->getEntityManager()->getRepository(ExpenseCategory::class)->getAllSortedByNameQB(),
                    ],
                ]
            )
            ->add('description')
            ->add('taxBaseAmount')
            ->add('totalAmount')
            ->add('taxPercentage')
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
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
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
                'provider',
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
                            'fieldName' => 'provider',
                        ],
                    ],
                ]
            )
            ->add(
                'expenseCategory',
                FieldDescriptionInterface::TYPE_MANY_TO_ONE,
                [
                    'editable' => false,
                    'sortable' => true,
                    'associated_property' => 'getName',
                    'route' => [
                        'name' => 'edit',
                    ],
                    'sort_field_mapping' => [
                        'fieldName' => 'name',
                    ],
                    'sort_parent_association_mappings' => [
                        [
                            'fieldName' => 'expenseCategory',
                        ],
                    ],
                ]
            )
            ->add(
                'description',
                FieldDescriptionInterface::TYPE_STRING,
                [
                    'editable' => true,
                ]
            )
            ->add(
                'taxBaseAmount',
                null,
                [
                    'editable' => false,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                    'template' => '@App/admin/cells/list__cell_tax_base_money_field.html.twig',
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
                            'template' => '@App/admin/cells/list__action_expense_pdf_button.html.twig',
                        ],
                        'duplicate' => [
                            'template' => '@App/admin/cells/list__action_expense_duplicate_button.html.twig',
                        ],
                        'delete' => [],
                    ],
                    'header_style' => 'width:144px',
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
            ->add(
                'date',
                DatePickerType::class,
                [
                    'format' => AbstractBase::DATE_FORM_TYPE_FORMAT,
                    'required' => true,
                ]
            )
            ->add(
                'provider',
                EntityType::class,
                [
                    'required' => false,
                    'class' => Provider::class,
                    'choice_label' => 'fiscalName',
                    'query_builder' => $this->getEntityManager()->getRepository(Provider::class)->getAllSortedByNameQB(),
                ]
            )
            ->add(
                'expenseCategory',
                EntityType::class,
                [
                    'required' => false,
                    'class' => ExpenseCategory::class,
                    'choice_label' => 'name',
                    'query_builder' => $this->getEntityManager()->getRepository(ExpenseCategory::class)->getAllSortedByNameQB(),
                ]
            )
            ->add('description')
            ->end()
            ->with(
                'Documents',
                [
                    'class' => 'col-md-5',
                    'box_class' => 'box box-success',
                ]
            )
            ->add(
                'document',
                VichFileType::class,
                [
                    'label_attr' => [
                        'class' => 'hidden',
                    ],
                    'required' => false,
                    'allow_delete' => true,
                    'download_uri' => !$this->isFormToCreateNewRecord() ? $this->getRouteGenerator()->generate('download_media_inline_expense_document', ['id' => $this->id($this->getSubject())]) : false,
                ]
            )
            ->end()
            ->with(
                'Amounts',
                [
                    'class' => 'col-md-3',
                    'box_class' => 'box box-success',
                ]
            )
            ->add(
                'taxBase',
                SimpleMoneyType::class,
                [
                    'required' => false,
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
                    'required' => false,
                    'disabled' => true,
                    'amount_options' => [
                        'label' => false,
                    ],
                ]
            )
            ->add(
                'taxPercentage',
                IntegerType::class,
                [
                    'required' => false,
                ]
            )
            ->end()
            ->with(
                'Controls',
                [
                    'class' => 'col-md-3',
                    'box_class' => 'box box-success',
                ]
            )
            ->add(
                'hasBeenPaid',
                null,
                [
                    'required' => false,
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
            ->end()
        ;
    }

    /**
     * @param Expense $object
     */
    public function prePersist($object): void
    {
        $this->commonPreActions($object);
    }

    /**
     * @param Expense $object
     */
    public function preUpdate($object): void
    {
        $this->commonPreActions($object);
    }

    private function commonPreActions(Expense $object): void
    {
        $object->calculateTotalBaseAmount();
    }
}
