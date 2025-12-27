<?php

declare(strict_types=1);

namespace App\Http\Requests\Part;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePartRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'price_default' => ['sometimes', 'numeric', 'min:0'],
            'amount' => ['sometimes', 'integer', 'min:0'],
            'warning_amount' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}