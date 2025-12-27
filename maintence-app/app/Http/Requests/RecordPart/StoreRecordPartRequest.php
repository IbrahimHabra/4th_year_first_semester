<?php

declare(strict_types=1);

namespace App\Http\Requests\RecordPart;

use App\Models\Part;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreRecordPartRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'part_id' => ['required', 'integer', 'exists:parts,id'],
            'modify_type_id' => ['required', 'integer', 'in:1,2,3,4'], // 1=Add, 2,3,4=Subtract
            'modified_amount' => ['required', 'integer', 'min:1'],
            'modification_date' => ['required', 'date'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->modify_type_id != 1) { // If consuming/wasting
                    $part = Part::find($this->part_id);
                    if ($part && $part->amount < $this->modified_amount) {
                        $validator->errors()->add('modified_amount', "Insufficient stock. Current: {$part->amount}");
                    }
                }
            }
        ];
    }
}