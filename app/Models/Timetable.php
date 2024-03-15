<?php

namespace App\Models;

use App\Events\TimetableCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @mixin IdeHelperTimetable
 */
class Timetable extends Model
{
    use HasFactory;

    /**
     * Количество слотов расписания в день
     */
    public const TIMETABLE_SLOTS_COUNT = 7;

    protected $fillable = [
        'user_id',
        'day_of_week'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(TimetableSlot::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::created(static function (Timetable $timetable) {
            event(new TimetableCreated($timetable));
        });
    }
}
