<?php

namespace App\Models;

class PanDetailDoc extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_detail_id',
        'doc_requirement',
        'doc_requirement_des',
        'attachment_user',
        'attachment_ams',
        'is_completed',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
    ];

    public $selectable = [
        '*',
    ];
}
