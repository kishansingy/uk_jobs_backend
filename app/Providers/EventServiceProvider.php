<?php

namespace App\Providers;

use App\Events\ApplicationStatusChanged;
use App\Events\AppointmentBooked;
use App\Events\JobImported;
use App\Listeners\ClassifyImportedJob;
use App\Listeners\NotifyUserOnApplicationChange;
use App\Listeners\SendAppointmentConfirmation;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        JobImported::class => [
            ClassifyImportedJob::class,
        ],
        ApplicationStatusChanged::class => [
            NotifyUserOnApplicationChange::class,
        ],
        AppointmentBooked::class => [
            SendAppointmentConfirmation::class,
        ],
    ];

    public function boot(): void {}

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
