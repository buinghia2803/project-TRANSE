<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Authentication extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "user_id",
        "type",
        "value",
        "token",
        "code",
        "created_at",
    ];

    // 1: 会員登録
    const REGISTER = 1;
    // 2: 退会
    const WITHDRAW = 2;
    // 3: パスワード再設定
    const RESET_PASSWORD = 3;
    // 4: 新しいパスワード・登録メールアドレス再設定
    const RESET_PASSWORD_AND_CHANGE_EMAIL = 4;
    // 5: 登録メールアドレスの変更
    const CHANGE_EMAIL = 5;

    /**
     * User
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    /**
     * Check Expired Time for authentication
     *
     * @param $authentication
     * @return boolean
     */
    public function hasExpiredTime(): bool
    {
        // Check time around 30 minute
        $time = strtotime(date('Y-m-d H:i:s'));
        $createdAt = strtotime($this->created_at);
        if ($time - $createdAt >= 60 * 30) {
            return true;
        }

        return false;
    }
}
