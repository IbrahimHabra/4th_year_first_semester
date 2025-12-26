<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Part extends Model
{
    protected $fillable = [
        'name',
        'price_default',
        'amount',
        'warning_amount'
    ];

    protected $casts = [
        'price_default' => 'decimal:2',
    ];

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }

    public function recordParts(): HasMany
    {
        return $this->hasMany(RecordPart::class);
    }
}