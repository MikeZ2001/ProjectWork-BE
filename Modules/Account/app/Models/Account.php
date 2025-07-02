<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 */
class Account extends Model
{
    protected $fillable = [
        'type',
        'balance',
        'open_date',
        'close_date',
        'status'
    ];
}