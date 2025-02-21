<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    public function addToCart(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $user = auth()->user();
        $courseId = $request->course_id;

        // 檢查購物車內是否已經有該課程
        $cartItem = Cart::where('user_id', $user->id)
                        ->where('course_id', $courseId)
                        ->first();

        if ($cartItem) {
            return response()->json(['message' => '此課程已在購物車內'], 400);
        }

        // 加入購物車
        Cart::create([
            'user_id' => $user->id,
            'course_id' => $courseId,
        ]);

        return response()->json(['message' => '成功加入購物車']);
    }

    public function viewCart()
    {
        $user = auth()->user();

        $cartItems = Cart::where('user_id', $user->id)
                        ->with('course') // 取得課程詳細資料
                        ->get();

        return response()->json($cartItems);
    }

    public function removeFromCart($id)
    {
        $cartItem = Cart::find($id);

        if (!$cartItem) {
            return response()->json(['message' => '購物車項目不存在'], 404);
        }

        $cartItem->delete();
        return response()->json(['message' => '成功移除購物車項目']);
    }

    public function clearCart()
    {
        $user = auth()->user();
        Cart::where('user_id', $user->id)->delete();

        return response()->json(['message' => '購物車已清空']);
    }



}
