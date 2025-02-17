<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'course_id', 'price', 'quantity'];

    // 關聯：訂單
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // 關聯：課程
    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
