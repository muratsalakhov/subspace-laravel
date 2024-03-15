<?php

namespace App\Providers;

use App\Events\HabitCreated;
use App\Events\TimetableCreated;
use App\Listeners\CreateDefaultUserData;
use App\Listeners\CreateHabitChecks;
use App\Listeners\CreateTimetableSlots;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            CreateDefaultUserData::class
        ],
        HabitCreated::class => [
            CreateHabitChecks::class
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
