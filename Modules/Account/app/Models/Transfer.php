<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transfer extends Model
{
    protected $fillable = [
        'from_account_id',
        'to_account_id',
        'description',
        'amount'
    ];

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }
}