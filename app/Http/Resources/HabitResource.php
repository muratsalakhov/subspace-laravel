<?php

namespace App\Http\Resources;

use App\Models\Habit;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;


/** @mixin Habit */
class HabitResource extends JsonResource
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
            'name' => $this->name,
            'checks' => HabitCheckResource::collection($this->checks)
        ];
    }
}
