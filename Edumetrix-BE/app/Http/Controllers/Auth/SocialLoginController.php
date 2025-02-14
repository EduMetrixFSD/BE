<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Str;

class SocialLoginController extends Controller
{
    /**
     * 使用 Google 登入
     */
    public function googleLogin(Request $request)
    {
        // 使用 Socialite 驗證 Google 的 OAuth Token 並獲取用戶信息
        // 使用 stateless() 以無狀態模式進行驗證，避免 CSRF 驗證問題
        $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->token);

        // 使用 firstOrCreate 方法檢查用戶是否已經存在
        // 如果不存在，則根據提供的 email 和 name 自動創建新用戶
        $user = User::firstOrCreate(
            ['email' => $googleUser->email], // 檢查條件：Email 唯一性
            [
                'name' => $googleUser->name, // 新用戶的名稱
                'password' => bcrypt(str_random(16)) // 自動生成一個隨機密碼，並進行加密
            ]
        );

        // 為用戶生成 Sanctum Token，該 Token 將用於後續 API 驗證
        $token = $user->createToken('auth_token')->plainTextToken;

        // 返回 JSON 格式的響應，包括登入成功信息、用戶資料和 Token
        return response()->json([
            'message' => '登入成功',
            'user' => $user,
            'token' => $token,
        ]);
    }
}
