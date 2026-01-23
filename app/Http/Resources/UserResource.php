<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'position' => $this->when($this->relationLoaded('position'), function () {
                return [
                    'id' => $this->position?->id,
                    'name' => $this->position?->name,
                    'allowed_comfort_categories' => $this->when(
                        $this->position?->relationLoaded('comfortCategories'),
                        function () {
                            return $this->position->comfortCategories->map(function ($category) {
                                return [
                                    'id' => $category->id,
                                    'name' => $category->name,
                                    'level' => $category->level,
                                ];
                            });
                        }
                    ),
                ];
            }),
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
