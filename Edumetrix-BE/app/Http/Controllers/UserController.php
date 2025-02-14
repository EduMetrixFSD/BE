<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
    * 獲取當前用戶資訊
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\JsonResponse
    */
    public function getUser(Request $request)
    {
        
        return response()->json([
            'success' => true,
            'data' => $request->user(),
        ]);
    }

    /**
     * 更新用戶個人資料
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|unique:users,email,' . $request->user()->id,
        ]);

        $user = $request->user();
        $user->update($request->only('name', 'email'));

        return response()->json([
            'success' => true,
            'message' => '個人資料更新成功',
            'data' => $user,
        ]);
    }

    /**
     * 獲取用戶課程清單（假設用戶與課程之間存在多對多或一對多的關聯）
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserCourses(Request $request)
    {
        $courses = $request->user()->courses; // 假設 User 模型有 courses 關聯
        return response()->json([
            'success' => true,
            'data' => $courses,
        ]);
    }
}
