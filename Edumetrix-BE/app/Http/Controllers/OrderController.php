<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Cart;
use App\Models\OrderItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use TsaiYiHua\ECPay\Checkout; // 使用 laravel-ecpay 套件

class OrderController extends Controller
{
    protected $checkout;

    public function __construct(Checkout $checkout)
    {
        $this->checkout = $checkout;
    }

    public function createOrder(Request $request)
    {
        $user = auth()->user();
        $cartItems = Cart::where('user_id', $user->id)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => '購物車為空，無法結帳'], 400);
        }

        $totalAmount = 0;
        foreach ($cartItems as $cartItem) {
            if (!$cartItem->course) {
                return response()->json(['message' => '無效的商品'], 400);
            }
            $totalAmount += $cartItem->course->price * $cartItem->quantity;
        }

        if ($totalAmount <= 0) {
            return response()->json(['message' => '訂單金額無效'], 400);
        }

        // **生成唯一訂單編號**
        $merchantTradeNo = 'ECPAY' . time();

        // 創建訂單
        $order = Order::create([
            'user_id' => $user->id,
            'status' => 'pending',
            'total_price' => $totalAmount,
            'merchant_trade_no' => $merchantTradeNo,  // 確保這裡與資料表欄位名稱一致
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'payment_method' => 'Credit', // 假設使用信用卡支付
            'trade_no' => $merchantTradeNo, // 假設使用綠界的交易編號
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
            $formData = [
                'MerchantID'       => env('ECPAY_MERCHANT_ID'), 
                'PaymentType'      => 'aio',
                'TradeDesc'        => '訂單付款',
                'ChoosePayment'    => 'Credit',
                'UserId' => $user->id,
                'ItemDescription' => '訂單付款',
                'ItemName' => '訂單 ' . $merchantTradeNo,
                'TotalAmount' => $totalAmount,
                'PaymentMethod' => 'Credit',
                'MerchantTradeNo' => $merchantTradeNo,
                'MerchantTradeDate' => now()->format('Y/m/d H:i:s'),
                'ReturnURL' => url('/callback'),
                'ClientBackURL' => url('/success'),
            ];

            $paymentForm = $this->checkout->setPostData($formData)->send();

            return response()->json(['form' => $paymentForm]);

        } catch (\Exception $e) {
            Log::error('綠界支付錯誤: ' . $e->getMessage());
            // ❗ 如果支付請求失敗，刪除訂單，避免錯誤訂單
                $order->delete();
            return response()->json(['message' => '支付失敗', 'error' => $e->getMessage()], 500);
        }
       
    }

    public function handlePaymentCallback(Request $request)
    {
        Log::info('綠界支付回調: ', $request->all());


        if (!$request->has('MerchantTradeNo') || !$request->has('RtnCode')) {
            Log::error('缺少必要的參數', $request->all());  // 輸出回傳資料，便於排查
            return response()->json(['message' => '缺少必要參數'], 400);
        }
    
        // 驗證交易是否有效
        $order = Order::where('trade_no', $request->MerchantTradeNo)->first();

        if (!$order) {
            Log::error('找不到訂單：' . $request->MerchantTradeNo);
            return response()->json(['message' => '訂單不存在'], 400);
        }

        // **檢查交易結果**
    if (in_array($request->RtnCode, [1, 800])) {
        $order->update([
            'status' => 'paid',
            'paid_at' => now()
        ]);
    } else {
        $order->update(['status' => 'failed']);
    }

        return response()->json(['message' => '支付狀態已更新']);
    }
    public function getUserOrders()
    {
        $user = auth()->user();
        $orders = Order::where('user_id', $user->id)->with('orderItems.course')->get();

        return response()->json($orders);
    }

    public function getOrderDetails($id)
    {
        $user = auth()->user();
        $order = Order::where('id', $id)->where('user_id', $user->id)->with('orderItems.course')->first();

        if (!$order) {
        return response()->json(['message' => '訂單不存在'], 404);
        }

        return response()->json($order);
    }

    public function cancelOrder($id)
    {
        $user = auth()->user();
        $order = Order::where('id', $id)->where('user_id', $user->id)->first();

        if (!$order) {
            return response()->json(['message' => '訂單不存在'], 404);
        }

        if ($order->status !== 'pending') {
            return response()->json(['message' => '訂單已支付或已取消，無法取消'], 400);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json(['message' => '訂單已取消']);
    }

}
