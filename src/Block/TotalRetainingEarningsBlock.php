<?php

namespace App\Block;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TotalRetainingEarningsBlock extends AbstractBlockService
{
    public function execute(BlockContextInterface $blockContext, Response $response = null): Response
    {
        return $this->renderResponse(
            $blockContext->getTemplate(),
            [
                'block' => $blockContext->getBlock(),
                'settings' => $blockContext->getSettings(),
                'title' => 'Total Retaining Earnings',
            ],
            $response
        );
    }

    public function configureSettings(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'title' => 'Total Retaining Earnings',
            'content' => 'Default content',
            'template' => '@App/admin/block/total_retaining_earnings.html.twig',
        ]);
    }
}
