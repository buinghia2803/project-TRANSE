<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SFTComment extends Model
{
    use SoftDeletes;

    protected $table = 'sft_comments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'support_first_time_id',
        'admin_id',
        'type',
        'content',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'support_first_time_id',
        'type',
    ];

    public $selectable = [
        '*',
    ];

    /**
     * Type comment
     */
    const TYPE_COMMENT_INSIDER = 1;
    const TYPE_COMMENT_CUSTOMER = 2;

    public function admin()
    {
        return $this->belongsTo(Admin::class);
    }
}
