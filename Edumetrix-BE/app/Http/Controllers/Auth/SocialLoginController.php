<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SocialLoginController extends Controller
{
    /**
     * 使用 Google 登入
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Google 登入回調
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();

            // 檢查用戶是否已存在
            $user = User::where('social_id', $googleUser->id)->where('provider', 'google')->first();

            if (!$user) {
                // 建立新用戶
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'provider' => 'google',
                    'social_id' => $googleUser->id,
                    'password' => Hash::make(uniqid()), // 產生隨機密碼
                    'avatar' => $googleUser->avatar,
                ]);
            }

            // laravel 登入該用戶
            Auth::login($user);

            // 產生 Sanctum Token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => '登入成功',
                'user' => $user,
                'token' => $token,
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Google 登入失敗'], 500);
        }
    }
}
