<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MNation extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "name"
    ];

    /**
     * Relationship User
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'info_nation_id');
    }

    /**
     * Relationship MPrefecture
     *
     * @return void
     */
    public function prefecture()
    {
        return $this->belongsTo(MPrefecture::class, 'id', 'm_nation_id');
    }
}
