<?php

declare(strict_types=1);

namespace App\Http\Requests\Part;

use Illuminate\Foundation\Http\FormRequest;

class StorePartRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'price_default' => ['required', 'numeric', 'min:0'],
            'amount' => ['required', 'integer', 'min:0'],
            'warning_amount' => ['required', 'integer', 'min:0'],
        ];
    }
}