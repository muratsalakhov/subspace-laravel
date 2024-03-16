<?php

namespace App\Models;

use App\Events\HabitCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperHabit
 */
class Habit extends Model
{
    use HasFactory;

    /**
     * Количество галочек для каждой привычки
     */
    public const HABIT_CHECKS_COUNT = 18;

    protected $fillable = [
        'user_id',
        'name'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checks(): HasMany
    {
        return $this->hasMany(HabitCheck::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::created(static function ($habit) {
            event(new HabitCreated($habit));
        });
    }
}
