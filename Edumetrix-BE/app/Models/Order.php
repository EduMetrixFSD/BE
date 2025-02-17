<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'order_number', 'total_amount', 'status', 'payment_method', 'trade_no'];

    // 關聯：使用者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 關聯：訂單內的課程
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
