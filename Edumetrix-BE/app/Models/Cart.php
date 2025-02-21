<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'course_id', 'quantity', 'locked_price'];

    // 關聯：使用者
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // 關聯：課程
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // 計算購物車項目的總價
    public function getTotalPrice()
    {
        // 如果有鎖定價格，使用鎖定的價格
        return $this->quantity * ($this->locked_price ?? $this->course->price);
    }

    // 增加商品數量
    public function incrementQuantity()
    {
        $this->quantity++;
        $this->save();
    }
}

