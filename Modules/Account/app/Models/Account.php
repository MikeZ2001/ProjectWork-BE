<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Model;

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