<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterUser $request){
        try {
            if (User::where('email', $request->email)->first()) return $this->responsePattern(409, false, 'The email has already been taken');
            DB::transaction(function () use ($request){
                User::create($request->all());
            });
            return $this->responsePattern(201, true, "User has been created");
        } catch (\Throwable $th) {
            Log::error("Register failed", ["error" => $th->getMessage()]);
            return $this->responsePattern(400, false, "User creation failed");
        }
    }

    public function login(LoginRequest $request){
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) return $this->responsePattern(404, false, 'User not found');
            if (!Hash::check($request->password, $user->password)) return $this->responsePattern(401, false, 'Wrong e-mail or password');
            return $this->responsePattern(200, true, 'Login was successful', ['token' => $user->createToken('api')->accessToken]);
        } catch (\Throwable $th) {
            Log::error("Login failed", ["error" => $th->getMessage()]);
            return $this->responsePattern(400, false, "Login has failed, contact support team");
        }
    }

    public function logout(){
        try {
            Auth::logout();
            return $this->responsePattern(200, true, 'Logout successfully');
        } catch (\Throwable $th) {
            Log::error("Logout failed", ["error" => $th->getMessage()]);
            return $this->responsePattern(400, false, "Logout has failed, contact support team");
        }
    }
}
