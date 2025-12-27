<?php

declare(strict_types=1);

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'customer_name' => ['sometimes', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
            'on_hand_price' => ['nullable', 'numeric', 'min:0'], 
            'member_id' => ['sometimes', 'integer', 'exists:members,id'],
            'model_id' => ['sometimes', 'integer', 'exists:models,id'],
        ];
    }
}