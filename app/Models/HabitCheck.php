<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin IdeHelperHabitCheck
 */
class HabitCheck extends Model
{
    use HasFactory;

    public function habit(): BelongsTo
    {
        return $this->belongsTo(Habit::class);
    }
}
