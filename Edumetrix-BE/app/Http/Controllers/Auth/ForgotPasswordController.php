<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Laravel 提供的 密碼重置功能的工具類
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    /**
     * 發送「忘記密碼」的連結到用戶信箱
     */
    public function forgotPassword(Request $request)
    {
        // 驗證輸入的 email
        $request->validate(['email' => 'required|email']);
        // 確保用戶存在
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['message' => '找不到該電子郵件的用戶'], 404);
        }

        // 產生 Token 並存入 `password_resets` 表
        $token = Str::random(60);
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'email' => $request->email,
                'token' => Hash::make($token),
                'created_at' => Carbon::now()
            ]
        );

        // 這裡應該實際發送郵件，但 API 方式可以直接返回 token 來測試
        return response()->json([
            'message' => '密碼重設連結已產生',
            'token' => $token  // 這只是測試，正式應該發送郵件
        ]);
    }
}
