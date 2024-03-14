<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Habit;
use App\Models\Note;
use App\Models\Task;
use App\Policies\HabitPolicy;
use App\Policies\NotePolicy;
use App\Policies\TaskPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Task::class => TaskPolicy::class,
        Note::class => NotePolicy::class,
        Habit::class => HabitPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        //
    }
}
