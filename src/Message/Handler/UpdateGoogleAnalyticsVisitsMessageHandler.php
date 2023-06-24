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
    private LoggerInterface $queueLogger;
    private UserRepository $ur;
    private GoogleAnalyticsManager $gam;

    public function __construct(LoggerInterface $queueLogger, UserRepository $ur, GoogleAnalyticsManager $gam)
    {
        $this->queueLogger = $queueLogger;
        $this->ur = $ur;
        $this->gam = $gam;
    }

    public function __invoke(UpdateGoogleAnalyticsVisitsMessage $message): void
    {
        $this->queueLogger->info('[UpdateGoogleAnalyticsVisitsMessageHandler] hit');
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
                $this->queueLogger->info('[UpdateGoogleAnalyticsVisitsMessageHandler] optimal user: '.$optimalUser->getUsername());
                $hasBeenFetched = $this->gam->fetchYesterdayVisits($optimalUser);
                if ($hasBeenFetched) {
                    $this->queueLogger->info('[UpdateGoogleAnalyticsVisitsMessageHandler] new page visits fetch');
                } else {
                    $this->queueLogger->info('[UpdateGoogleAnalyticsVisitsMessageHandler] ERROR! no new page visits fetch!');
                }
            } else {
                $this->queueLogger->info('[UpdateGoogleAnalyticsVisitsMessageHandler] ERROR! no optimal user found!');
            }
        }
    }
}
