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
    public function getSchedule(): Schedule
    {
        return (new Schedule())->add(
            RecurringMessage::every('1 day', new UpdateGoogleAnalyticsVisitsMessage(), from: '12:47')->withJitter(3600)
//            RecurringMessage::every(
//                '10 seconds',
//                new UpdateGoogleAnalyticsVisitsMessage()
//            )->withJitter(4)
        );
    }
}
