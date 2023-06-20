<?php

namespace App\Message\Handler;

use App\Manager\GoogleAnalyticsManager;
use App\Message\UpdateGoogleAnalyticsVisitsMessage;
use App\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateGoogleAnalyticsVisitsMessageHandler
{
    private LoggerInterface $logger;
    private UserRepository $ur;
    private GoogleAnalyticsManager $gam;

    public function __construct(LoggerInterface $logger, UserRepository $ur, GoogleAnalyticsManager $gam)
    {
        $this->logger = $logger;
        $this->ur = $ur;
        $this->gam = $gam;
    }

    public function __invoke(UpdateGoogleAnalyticsVisitsMessage $message)
    {
        $this->logger->info('[UpdateGoogleAnalyticsVisitsMessageHandler] hit');
        $users = $this->ur->findAll();
        if (count($users) > 0) {
            $optimalUser = null;
            foreach ($users as $user) {
                if ($user->getGoogleCredentialsAccepted() && $user->getGoogleAccessToken()) {
                    $optimalUser = $user;

                    break;
                }
            }
            if ($optimalUser) {
                $this->logger->info('[UpdateGoogleAnalyticsVisitsMessageHandler] optimal user: '.$optimalUser->getUsername());
                $hasBeenFetched = $this->gam->fetchYesterdayVisits($optimalUser);
                if ($hasBeenFetched) {
                    $this->logger->info('[UpdateGoogleAnalyticsVisitsMessageHandler] new page visits fetch');
                } else {
                    $this->logger->error('[UpdateGoogleAnalyticsVisitsMessageHandler] no new page visits fetch!');
                }
            } else {
                $this->logger->error('[UpdateGoogleAnalyticsVisitsMessageHandler] no optimal user found!');
            }
        }
    }
}
