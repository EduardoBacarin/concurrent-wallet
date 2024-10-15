<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class TransactionJob implements ShouldQueue {
    use Queueable;
    public $tries = 10;
    public function __construct(private User $user, private $amount, private Transaction $transaction) {
    }

    public function handle(): void {
        DB::transaction(function () {
            Wallet::where('users_id', $this->user->id)->lockForUpdate()->increment('amount', $this->amount);
        }, 10);
    }
}
