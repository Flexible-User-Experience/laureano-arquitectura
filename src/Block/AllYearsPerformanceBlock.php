<?php

namespace App\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AllYearsPerformanceBlock extends AbstractBlockService
{
    public function execute(BlockContextInterface $blockContext, Response $response = null): Response
    {
        return $this->renderResponse(
            $blockContext->getTemplate(),
            [
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'title' => 'All Years Performance',
            ],
            $response
        );
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'title' => 'All Years Performance',
            'content' => 'Default content',
            'template' => '@App/admin/block/all_years_performance.html.twig',
        ]);
    }
}
