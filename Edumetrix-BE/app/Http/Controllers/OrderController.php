<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\OrderItem;
use GuzzleHttp\Client;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        // 確保用戶已登入
        $user = auth()->user();
        
        // 取得用戶的購物車
        $cartItems = Cart::where('user_id', $user->id)->get();
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => '購物車為空，無法結帳'], 400);
        }

        // 計算總金額
        $totalAmount = 0;
        foreach ($cartItems as $cartItem) {
            $totalAmount += $cartItem->course->price * $cartItem->quantity;
        }

        // 創建訂單
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total_price' => $totalAmount,
        ]);

        // 創建訂單細項
        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'course_id' => $cartItem->course_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->course->price,
            ]);
        }

        // 清空購物車
        Cart::where('user_id', $user->id)->delete();

        // 調用綠界支付 API
        $client = new Client();
        $response = $client->post('https://payment.ecpay.com.tw/Cashier/AioCheckOut/V5', [
            'form_params' => [
                'MerchantID' => env('ECPAY_MERCHANT_ID'),  // 綠界商戶 ID
                'TradeNo' => $order->id,
                'TotalAmount' => $totalAmount,
                'ItemDesc' => '訂單商品',  // 商品描述
                'ReturnURL' => url('/order/callback'),  // 支付完成後返回的 URL
                'NotifyURL' => url('/order/notify'),  // 支付通知 URL
            ]
        ]);

        // 處理支付回應，並更新訂單狀態
        $paymentUrl = (string)$response->getBody();  // 綠界回傳支付頁面的 URL
        return response()->json(['payment_url' => $paymentUrl]);
    }

    public function handlePaymentCallback(Request $request)
    {
        // 收到綠界支付結果回傳
        // 這裡會根據綠界回傳的支付結果來更新訂單狀態
        $order = Order::find($request->TradeNo);

        if ($request->RtnCode == 1) {
            $order->status = 'paid';  // 訂單支付成功
        } else {
            $order->status = 'failed';  // 訂單支付失敗
        }

        $order->save();

        return response()->json(['message' => '支付結果更新']);
    }
}
