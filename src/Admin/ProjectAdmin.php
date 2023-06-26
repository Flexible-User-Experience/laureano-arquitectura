<?php

namespace App\Admin;

use App\Entity\AbstractBase;
use App\Entity\ProjectCategory;
use App\Entity\Translations\ProjectTranslation;
use App\Enum\SortOrderEnum;
use App\Form\Type\GedmoTranslationsType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class ProjectAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'Project';
    protected $baseRoutePattern = 'web/project';

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        parent::configureDefaultSortValues($sortValues);
        $sortValues[DatagridInterface::SORT_ORDER] = SortOrderEnum::ASCENDING->value;
        $sortValues[DatagridInterface::SORT_BY] = 'position';
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('show')
            ->remove('batch')
        ;
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('name')
            ->add(
                'projectCategories',
                ModelFilter::class,
                [
                    'field_options' => [
                        'class' => ProjectCategory::class,
                        'choice_label' => 'name',
                        'query_builder' => $this->getEntityManager()->getRepository(ProjectCategory::class)->getAllSortedByNameQB(),
                    ],
                ]
            )
            ->add(
                'beginDate',
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
                'endDate',
                DateFilter::class,
                [
                    'field_type' => DatePickerType::class,
                    'field_options' => [
                        'widget' => 'single_text',
                        'format' => AbstractBase::DATE_PICKER_TYPE_FORMAT,
                    ],
                ]
            )
            ->add('summary')
            ->add('description')
            ->add('position')
            ->add('showInFrontend')
        ;
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add(
                'imageName',
                null,
                [
                    'editable' => false,
                    'header_style' => 'width:140px',
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                    'template' => '@App/admin/cells/list__cell_image_field.html.twig',
                ]
            )
            ->add(
                'name',
                FieldDescriptionInterface::TYPE_STRING,
                [
                    'editable' => true,
                ]
            )
            ->add(
                'projectCategories',
                FieldDescriptionInterface::TYPE_MANY_TO_MANY,
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
                            'fieldName' => 'projectCategories',
                        ],
                    ],
                ]
            )
            ->add(
                'beginDate',
                FieldDescriptionInterface::TYPE_DATE,
                [
                    'format' => AbstractBase::DATE_STRING_FORMAT,
                    'editable' => true,
                    'header_class' => 'text-center',
                    'row_align' => 'center',
                ]
            )
            ->add(
                'position',
                FieldDescriptionInterface::TYPE_INTEGER,
                [
                    'editable' => true,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
                ]
            )
            ->add(
                'showInFrontend',
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
                        'delete' => [],
                    ],
                    'header_style' => 'width:83px',
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
                'Image',
                [
                    'class' => 'col-md-3',
                    'box_class' => 'box box-success',
                ]
            )
            ->add(
                'imageFile',
                VichImageType::class,
                [
                    'required' => $this->isFormToCreateNewRecord(),
                    'allow_delete' => false,
                    'help' => 'Image Helper',
                ]
            )
            ->end()
            ->with(
                'General',
                [
                    'class' => 'col-md-5',
                    'box_class' => 'box box-success',
                ]
            )
            ->add(
                'name',
                TextType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'summary',
                TextType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'description',
                CKEditorType::class,
                [
                    'required' => false,
                ]
            )
            ->end()
            ->with(
                'Translations',
                [
                    'class' => 'col-md-4',
                    'box_class' => 'box box-success',
                ]
            )
            ->add(
                'translations',
                GedmoTranslationsType::class,
                [
                    'label' => false,
                    'required' => false,
                    'translatable_class' => ProjectTranslation::class,
                    'fields' => [
                        'summary' => [
                            'required' => true,
                            'field_type' => TextType::class,
                        ],
                        'description' => [
                            'required' => false,
                            'field_type' => CKEditorType::class,
                        ],
                    ],
                ]
            )
            ->end()
        ;
        if (!$this->isFormToCreateNewRecord()) {
            $form
                ->with(
                    'Extra Images',
                    [
                        'class' => 'col-md-7',
                        'box_class' => 'box box-success',
                    ]
                )
                ->add(
                    'images',
                    CollectionType::class,
                    [
                        'label' => false,
                        'required' => false,
                        'error_bubbling' => true,
                        'by_reference' => false,
                    ],
                    [
                        'edit' => 'inline',
                        'inline' => 'table',
                        'sortable' => 'position',
                        'order' => SortOrderEnum::ASCENDING->value,
                    ]
                )
                ->end()
            ;
        }
        $form
            ->with(
                'Controls',
                [
                    'class' => 'col-md-3',
                    'box_class' => 'box box-success',
                ]
            )
            ->add(
                'projectCategories',
                EntityType::class,
                [
                    'required' => true,
                    'multiple' => true,
                    'class' => ProjectCategory::class,
                    'choice_label' => 'name',
                    'help' => 'Project Categories Helper',
                    'query_builder' => $this->getEntityManager()->getRepository(ProjectCategory::class)->getAllSortedByNameQB(),
                ]
            )
            ->add(
                'beginDate',
                DatePickerType::class,
                [
                    'format' => AbstractBase::DATE_FORM_TYPE_FORMAT,
                    'required' => false,
                ]
            )
            ->add(
                'endDate',
                DatePickerType::class,
                [
                    'format' => AbstractBase::DATE_FORM_TYPE_FORMAT,
                    'required' => false,
                ]
            )
            ->add(
                'position',
                NumberType::class,
                [
                    'required' => true,
                    'help' => 'Position Helper',
                ]
            )
            ->add(
                'showInFrontend',
                null,
                [
                    'required' => false,
                ]
            )
            ->end()
        ;
    }
}
