<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Doc extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'app_trademark_id',
        'admin_id',
        'attachment_xml',
        'attachment_pdf',
        'sending_noti_rejection_date',
        'response_deadline',
        'type',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'admin_id',
        'type',
        'created_at',
        'updated_at',
    ];

    public $selectable = [
        '*',
    ];
}
