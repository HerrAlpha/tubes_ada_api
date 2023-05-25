<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\BaseController;
use App\Models\User;
use App\Services\FileManagementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseController
{
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email'         => 'required|email|exists:users,email',
            'password'      => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            Auth::attempt([
                'email'     => $request->email,
                'password'  => $request->password
            ]);

            if (!Auth::check()) {
                return $this->sendError('Email atau password yang anda masukkan salah.');
            }

            $user = Auth::user();

            $token = $user->createToken('Bearer Token');

            $accessToken = $token->accessToken;
            $expiresAt = $token->token->expires_at->diffInDays(now());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorException($e->getMessage());
        }

        return $this->sendResponse('Login berhasil.', [
            'token'             => $accessToken,
            'expired_token'     => $expiresAt,
            'user'              => $user->only('name', 'email', 'role', 'phone', 'profile_pict', 'created_at')
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|min:3|max:128',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|numeric|digits_between:12,14',
            'role'     => 'required|in:RESTO,INVESTOR',
            'password' => 'required|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors()->first());
        }

        DB::beginTransaction();
        try {
            $path = FileManagementService::uploadImage(file_get_contents('https://api.dicebear.com/6.x/personas/png?seed=' . $request->email), 'profile');

            $user = User::create([
                'name'          => $request->name,
                'email'         => $request->email,
                'phone'         => $request->phone,
                'password'      => bcrypt($request->password),
                'profile_pict'  => $path,
                'role'          => $request->role
            ]);

            $token          = $user->createToken('Bearer Token');

            $accessToken    = $token->accessToken;
            $expiresAt      = $token->token->expires_at->diffInDays(now());

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorException($e->getMessage());
        }

        return $this->sendResponse('Register berhasil.', [
            'token'             => $accessToken,
            'expired_token'     => $expiresAt,
            'user'              => $user->only('name', 'email', 'role', 'phone', 'profile_pict', 'created_at')
        ]);
    }

    public function logout()
    {
        DB::beginTransaction();
        try {
            Auth::user()->token()->revoke();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendErrorException($e->getMessage());
        }

        return $this->sendResponse('Logout berhasil.');
    }
}
