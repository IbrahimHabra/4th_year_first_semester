<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="RecordPartResource",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="part_name", type="string", example="Compressor"),
 * @OA\Property(property="modify_type", type="string", example="Restock"),
 * @OA\Property(property="amount", type="integer", example=10),
 * @OA\Property(property="date", type="string", format="date-time")
 * )
 */
class RecordPartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'part_name' => $this->part->name ?? 'Unknown',
            'modify_type' => $this->modifyType->name ?? 'Unknown',
            'amount' => $this->modified_amount,
            'date' => $this->modification_date->toIso8601String(),
        ];
    }
}