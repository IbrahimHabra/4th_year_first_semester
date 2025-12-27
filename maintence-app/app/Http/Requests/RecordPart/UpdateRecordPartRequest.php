<?php

declare(strict_types=1);

namespace App\Http\Requests\RecordPart;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRecordPartRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'part_id' => ['sometimes', 'integer', 'exists:parts,id'],
            'modify_type_id' => ['sometimes', 'integer', 'in:1,2,3,4'],
            'modified_amount' => ['sometimes', 'integer', 'min:1'],
            'modification_date' => ['sometimes', 'date'],
        ];
    }
}