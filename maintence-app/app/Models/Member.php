<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Member extends Model
{
    protected $fillable = [
        'name',
        'birth_date',
        'start_date',
        'exit_date'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'start_date' => 'date',
        'exit_date' => 'date',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}