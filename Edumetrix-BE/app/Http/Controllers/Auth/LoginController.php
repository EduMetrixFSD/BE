<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        // 驗證資料
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // 確認用戶是否存在
        $user = Auth::attempt($request->only('email', 'password'));

        if (!$user) {
            return response()->json(['message' => '無效的憑據'], 401);
        }

        // 檢查是否勾選記住我
        // 如果用戶選擇了「記住我」，Token 有效期將被設置為 30 天後。
        // 如果用戶沒有選擇「記住我」，Token 有效期將被設置為 2 小時後。
        $expiration = $request->remember_me ? now()->addDays(30) : now()->addHours(2);

        // 生成 Token
        $token = auth()->user()->createToken('auth_token', ['*'], $expiration)->plainTextToken;

        return response()->json([
            'message' => '登入成功',
            'user' => auth()->user(),
            'token' => $token,
            // 用來記錄或告知前端這個 Token 的過期時間
            'expires_at' => $expiration,
        ]);
    }
}
