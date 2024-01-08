<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MPrefecture extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "m_nation_id",
        "name",
    ];

    /**
     * Relationship User
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id', 'info_prefectures_id');
    }

    /**
     * Relationship MNation
     *
     * @return void
     */
    public function nations()
    {
        return $this->hasOne(MNation::class, 'id', 'm_nation_id');
    }

    /**
     * MNation.
     *
     * @return BelongsTo
     */
    public function mNation(): BelongsTo
    {
        return $this->belongsTo(MNation::class);
    }
}
