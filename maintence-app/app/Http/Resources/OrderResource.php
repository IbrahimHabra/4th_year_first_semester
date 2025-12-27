<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="OrderResource",
 * @OA\Property(property="id", type="integer", example=15),
 * @OA\Property(property="customer_name", type="string", example="Adel Kharma"),
 * @OA\Property(property="device_model", type="string", example="Samsung S24"),
 * @OA\Property(property="assigned_member", type="string", example="Ibrahim"),
 * @OA\Property(property="add_date", type="string", format="date-time", example="2025-12-06T10:00:00Z"),
 * @OA\Property(property="status", type="string", example="pending", description="pending, started, or finished"),
 * @OA\Property(property="notes", type="string", example="Screen crack", nullable=true)
 * )
 */
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