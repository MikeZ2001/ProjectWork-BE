<?php

namespace Modules\Account\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'category_id'
    ];

    protected $casts = [
        'type' => TransactionType::class
    ];

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }
}