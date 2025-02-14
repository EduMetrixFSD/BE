<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * 可批量賦值 (Mass Assignment) 的欄位。
     * 當使用 Eloquent 的 create() 或 fill() 方法時，
     * 只有在這個陣列列出的欄位才可被填充到資料表中。
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'provider',
        'provider_id',
    ];

    /**
     * 需要在序列化（轉換為 JSON 等）時隱藏的欄位。
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * 需要轉換型別的欄位。
     * 例如 'email_verified_at' 會自動被轉換為 Carbon 日期物件。
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 判斷使用者是否為第三方登入的使用者。
     * 如果 provider 欄位不為 null，就表示此使用者
     * 是透過第三方登入註冊。
     *
     * @return bool
     */
    public function isSocialUser()
    {
        return !is_null($this->provider);
    }
}
