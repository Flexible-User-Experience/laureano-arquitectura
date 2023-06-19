<?php

namespace App\Admin;

use App\Entity\Project;
use App\Enum\SortOrderEnum;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class ProjectImageAdmin extends AbstractBaseAdmin
{
    protected $classnameLabel = 'Project Image';
    protected $baseRoutePattern = 'web/project-image';

    protected function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::PAGE] = 1;
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
                'project',
                null,
                [
                    'editable' => false,
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
                'position',
                FieldDescriptionInterface::TYPE_INTEGER,
                [
                    'editable' => true,
                    'header_class' => 'text-right',
                    'row_align' => 'right',
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
                    'required' => true,
                    'help' => 'Short Image Helper',
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
                'position',
                NumberType::class,
                [
                    'required' => true,
                ]
            )
            ->add(
                'project',
                EntityType::class,
                [
                    'class' => Project::class,
                    'query_builder' => $this->em->getRepository(Project::class)->getAllSortedByNameQB(),
                    'multiple' => false,
                    'required' => true,
                    'attr' => [
                        'hidden' => true,
                    ],
                ]
            )
            ->end()
        ;
    }
}
