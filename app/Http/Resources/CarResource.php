<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'license_plate' => $this->license_plate,
            'year' => $this->year,
            'color' => $this->color,
            'model' => [
                'id' => $this->carModel->id,
                'brand' => $this->carModel->brand,
                'model' => $this->carModel->model,
                'full_name' => $this->carModel->full_name,
            ],
            'comfort_category' => [
                'id' => $this->carModel->comfortCategory->id,
                'name' => $this->carModel->comfortCategory->name,
                'level' => $this->carModel->comfortCategory->level,
            ],
            'driver' => [
                'id' => $this->driver->id,
                'full_name' => $this->driver->full_name,
                'phone' => $this->driver->phone,
            ],
        ];
    }
}
