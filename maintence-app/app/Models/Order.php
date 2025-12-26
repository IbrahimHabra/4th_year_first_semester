<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'model_id',
        'member_id',
        'customer_name',
        'add_date',
        'start_date',
        'finish_date',
        'on_hand_price',
        'notes'
    ];

    protected $casts = [
        'add_date' => 'datetime',
        'start_date' => 'datetime',
        'finish_date' => 'datetime',
        'on_hand_price' => 'decimal:2',
    ];

    public function deviceModel(): BelongsTo
    {
        return $this->belongsTo(DeviceModel::class, 'model_id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function bills(): HasMany
    {
        return $this->hasMany(Bill::class);
    }
}