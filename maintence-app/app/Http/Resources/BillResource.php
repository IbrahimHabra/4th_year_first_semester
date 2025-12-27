<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="BillResource",
 * @OA\Property(property="id", type="integer"),
 * @OA\Property(property="part_name", type="string"),
 * @OA\Property(property="part_price", type="number"),
 * @OA\Property(property="part_amount", type="integer"),
 * @OA\Property(property="total_price", type="number")
 * )
 */
class BillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'part_id' => $this->part_id,
            'part_name' => $this->part->name ?? 'Unknown',
            'part_price' => $this->part_price,
            'part_amount' => $this->part_amount,
            'total_price' => $this->part_price, 
        ];
    }
}