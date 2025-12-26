<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_name' => $this->customer_name,
            'device_model' => $this->deviceModel->name ?? null,
            'assigned_member' => $this->member->name ?? null,
            'add_date' => $this->add_date->toIso8601String(),
            'status' => 'pending', 
            'notes' => $this->notes,
        ];
    }
}