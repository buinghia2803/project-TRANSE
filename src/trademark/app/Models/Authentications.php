<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Authentications extends Model
{
    use HasFactory;

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

    const FORGOT_PASSWORD = 3;
    const FORGOT_PASSWORD_NO_EMAIL = 4;

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
