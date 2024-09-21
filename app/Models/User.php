<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Ramsey\Uuid\Uuid;
use App\Models\Wallet;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    use HasFactory, Notifiable, HasApiTokens;

    protected $keyType = 'string';
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted() {
        static::creating(fn(User $user) => $user->id = (string) Uuid::uuid4());
    }

    public function transactions(): HasMany {
        return $this->hasMany(Transaction::class, 'users_id', 'id');
    }

    public function wallet(): HasOne {
        return $this->hasOne(Wallet::class, 'users_id', 'id');
    }

    public function createWallet(){
        Wallet::create([
            'users_id' => $this->id,
            'amount' => 0
        ]);
    }
}
