<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="DeviceModelResource",
 * @OA\Property(property="id", type="integer", example=10),
 * @OA\Property(property="name", type="string", example="Galaxy S24"),
 * @OA\Property(property="company_id", type="integer", example=5),
 * @OA\Property(property="company_name", type="string", example="Samsung"),
 * )
 */
class DeviceModelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'company_id' => $this->company_id,
            'company_name' => $this->company->name ?? null, // Returns company name alongside ID
        ];
    }
}