<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecordPart extends Model
{
    protected $fillable = [
        'part_id',
        'modify_type_id',
        'modification_date',
        'modified_amount'
    ];

    protected $casts = [
        'modification_date' => 'datetime',
    ];

    public function part(): BelongsTo
    {
        return $this->belongsTo(Part::class);
    }

    public function modifyType(): BelongsTo
    {
        return $this->belongsTo(ModifyType::class);
    }
}