<?php

declare(strict_types=1);

namespace App\Http\Requests\Model;

use Illuminate\Foundation\Http\FormRequest;

class UpdateModelRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'company_id' => ['sometimes', 'integer', 'exists:companies,id'],
            'name' => ['sometimes', 'string', 'max:255'],
        ];
    }
}