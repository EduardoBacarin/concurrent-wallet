<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Jobs\TransactionJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\TransactionRequest;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletController extends Controller {

    public function credit(TransactionRequest $request) {
        try {
            $transaction = DB::transaction(function () use ($request) {
                $transactionCreate = Transaction::create(["users_id" => Auth::user()->id, "amount" => $request->amount]);
                TransactionJob::dispatch(Auth::user(), $request->amount, $transactionCreate);
                return $transactionCreate;
            });
            return $this->responsePattern(200, true, "Amount credited successfully", ["transaction" => $transaction->id]);
        } catch (\Throwable $th) {
            Log::error("Credit transaction - Error", ['user' => Auth::user()->id, 'data' => $request->all(), "error" => $th]);
            return $this->responsePattern(400, false, "Amount cannot be credited");
        }
    }

    public function debit(TransactionRequest $request) {
        try {
            DB::beginTransaction();
            if (Wallet::where('users_id', Auth::user()->id)->sharedLock()->first()->amount < $request->amount) {
                Log::error("Debit transaction - Insufficient funds", ['user' => Auth::user()->id, 'data' => $request->all()]);
                return $this->responsePattern(406, false, "Insufficient funds");
            }

            $transactionCreate = Transaction::create(["users_id" => Auth::user()->id, "amount" => ($request->amount * -1)]);
            Wallet::where('users_id', Auth::user()->id)->lockForUpdate()->increment('amount', ($request->amount * -1));

            //TransactionJob::dispatch(Auth::user(), ($request->amount * -1), $transactionCreate);
            DB::commit();
            return $this->responsePattern(200, true, "Amount debited successfully", ["transaction" => $transactionCreate->id]);
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error("Debit transaction - Error", ['user' => Auth::user()->id, 'data' => $request->all(), "error" => $th]);
            return $this->responsePattern(400, false, "Amount cannot be debited");
        }
    }

    public function balance() {
        try {
            return $this->responsePattern(200, true, "Balance retrieved succesffully", ['amount' => Auth::user()->wallet->amount]);
        } catch (\Throwable $th) {
            return $this->responsePattern(400, false, "An error has occurred");
        }
    }
}
