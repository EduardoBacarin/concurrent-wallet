<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Ramsey\Uuid\Uuid;

class Wallet extends Model
{
    use HasFactory;

    protected $keyType = 'string';
    protected $fillable = [
        'amount',
        'users_id'
    ];

    protected static function booted() {
        static::creating(fn(Wallet $wallet) => $wallet->id = (string) Uuid::uuid4());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
}
