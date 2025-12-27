<?php

declare(strict_types=1);

namespace App\Http\Requests\Member;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'start_date' => ['sometimes', 'date'],
            'exit_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}