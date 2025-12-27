<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @OA\Schema(
 * schema="MemberResource",
 * title="Member Resource",
 * @OA\Property(property="id", type="integer", example=1),
 * @OA\Property(property="name", type="string", example="Ibrahim"),
 * @OA\Property(property="birth_date", type="string", format="date", example="1999-01-01"),
 * @OA\Property(property="start_date", type="string", format="date", example="2025-01-01"),
 * @OA\Property(property="exit_date", type="string", format="date", example="2026-01-01", nullable=true),
 * @OA\Property(property="created_at", type="string", format="date-time")
 * )
 */
class MemberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'birth_date' => $this->birth_date?->format('Y-m-d'),
            'start_date' => $this->start_date?->format('Y-m-d'),
            'exit_date' => $this->exit_date?->format('Y-m-d'),
            'created_at' => $this->created_at->toIso8601String(),
        ];
    }
}