<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function createOrder(Request $request)
    {
        $user = auth()->user();
        $cartItems = Cart::where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => '購物車為空，無法結帳'], 400);
        }

        $totalAmount = 0;
        foreach ($cartItems as $cartItem) {
            $totalAmount += $cartItem->course->price * $cartItem->quantity;
        }

        // **生成唯一訂單編號**
        $merchantTradeNo = 'ECPAY' . time();

        // 創建訂單
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total_price' => $totalAmount,
            'merchant_trade_no' => $merchantTradeNo,  // 存入唯一訂單編號
            'order_number' => 'ORD-' . strtoupper(Str::random(10)), // 生成訂單號碼
        ]);

        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'course_id' => $cartItem->course_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->course->price,
            ]);
        }

        Cart::where('user_id', $user->id)->delete();

        // **產生綠界支付表單**
        try {
            $obj = new \ECPay_AllInOne();
            $obj->ServiceURL  = env('ECPAY_SERVICE_URL', 'https://payment-stage.ecpay.com.tw/Cashier/AioCheckOut/V5');
            $obj->HashKey     = env('ECPAY_HASH_KEY');
            $obj->HashIV      = env('ECPAY_HASH_IV');
            $obj->MerchantID  = env('ECPAY_MERCHANT_ID');
            $obj->EncryptType = '1';

            $obj->Send['ReturnURL']     = url('/order/callback');
            $obj->Send['ClientBackURL'] = url('/order/success');
            $obj->Send['MerchantTradeNo']   = $merchantTradeNo;
            $obj->Send['MerchantTradeDate'] = now()->format('Y/m/d H:i:s');
            $obj->Send['TotalAmount']       = $totalAmount;
            $obj->Send['TradeDesc']         = "訂單付款";
            $obj->Send['ChoosePayment']     = \ECPay_PaymentMethod::Credit;

            foreach ($cartItems as $cartItem) {
                array_push($obj->Send['Items'], [
                    'Name'     => $cartItem->course->title,
                    'Price'    => (int) $cartItem->course->price,
                    'Currency' => "元",
                    'Quantity' => $cartItem->quantity,
                    'URL'      => "http://127.0.0.1:8000/course/{$cartItem->course_id}"
                ]);
            }

            // 產生表單
            $formHTML = $obj->CheckOutString();
            return response()->json(['form' => $formHTML]);

        } catch (\Exception $e) {
            Log::error('綠界支付錯誤: ' . $e->getMessage());
            return response()->json(['message' => '支付失敗', 'error' => $e->getMessage()], 500);
        }
    }

    public function handlePaymentCallback(Request $request)
    {
        Log::info('綠界支付回調: ', $request->all());

        // 檢查交易編號是否存在
        $order = Order::where('merchant_trade_no', $request->MerchantTradeNo)->first();

        if (!$order) {
            return response()->json(['message' => '訂單不存在'], 400);
        }

        // **處理付款狀態**
        if ($request->RtnCode == 1 || $request->RtnCode == 800) {
            $order->status = 'paid';
            $order->paid_at = now();
        } else {
            $order->status = 'failed';
        }

        $order->save();

        return response()->json(['message' => '支付狀態已更新']);
    }
}
