<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        // 驗證資料
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
        ]);

        // 創建新用戶
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // 生成 Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => '註冊成功',
            'user' => $user,
            'token' => $token,
        ], 201);
    }
}
