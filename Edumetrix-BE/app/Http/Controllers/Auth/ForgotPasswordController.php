<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// Laravel 提供的 密碼重置功能的工具類
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /**
     * 發送「忘記密碼」的連結到用戶信箱
     */
    public function forgotPassword(Request $request)
    {
        // 驗證輸入的 email
        $request->validate(['email' => 'required|email']);

        // 處理 發送密碼重設連結 的核心方法
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // 用於處理用戶點擊「忘記密碼」後的請求。
        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => '密碼重設連結已發送'])
            : response()->json(['message' => '無法發送重設連結'], 500);
    }
}
