<?php

// src/Scheduler/DynamicScheduleProvider.php
namespace App\Scheduler;

use App\Scheduler\Handler\UpdateSortieEtatHandler;
use App\Scheduler\Message\UpdateSortieEtatMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;

#[AsSchedule(name: 'default')]
class DynamicScheduleProvider implements ScheduleProviderInterface
{
    private ?Schedule $schedule = null;

    public function getSchedule(): Schedule
    {
        return (new Schedule())->add(
            RecurringMessage::every('1 day', new UpdateSortieEtatMessage())
        );

    }

    public function clearAndAddMessages(): void
    {
        // clear the current schedule and add new recurring messages
        $this->schedule?->clear();
        $this->schedule?->add(
            RecurringMessage::cron('@hourly', new DoActionMessage()),
            RecurringMessage::cron('@daily', new DoAnotherActionMessage()),
        );
    }
}
?>