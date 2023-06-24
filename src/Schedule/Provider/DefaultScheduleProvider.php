<?php

namespace App\Schedule\Provider;

use App\Message\UpdateGoogleAnalyticsVisitsMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule('default')]
class DefaultScheduleProvider implements ScheduleProviderInterface
{
    /**
     * @throws \Exception
     */
    public function getSchedule(): Schedule
    {
        return (new Schedule())->add(
            RecurringMessage::every(
                '1 day',
                new UpdateGoogleAnalyticsVisitsMessage(),
                from: new \DateTimeImmutable('12:47', new \DateTimeZone('Europe/Madrid'))
            )->withJitter(3600)
//            RecurringMessage::every(
//                '120 seconds',
//                new UpdateGoogleAnalyticsVisitsMessage()
//            )
        );
    }
}
