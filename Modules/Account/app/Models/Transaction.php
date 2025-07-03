<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 */
class Transaction extends Model
{
    protected $fillable = [
        'amount',
        'type',
        'date',
        'description',
    ];

    protected $casts = [
        'type' => TransactionType::class
    ];
}