<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 */
class Account extends Model
{
    protected $fillable = [
        'name',
        'type',
        'balance',
        'open_date',
        'close_date',
        'status'
    ];

    protected $casts = [
        'status' => AccountStatus::class,
        'type' => AccountType::class,
        'balance' => 'decimal:2'
    ];
}