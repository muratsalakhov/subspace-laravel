<?php

namespace App\Http\Resources;

use App\Models\HabitCheck;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/** @mixin HabitCheck */
class HabitCheckResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'is_completed' => $this->is_completed,
        ];
    }
}
