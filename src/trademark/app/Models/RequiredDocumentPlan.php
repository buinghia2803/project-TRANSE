<?php

namespace App\Models;

class RequiredDocumentPlan extends BaseModel
{
    protected $table = 'required_document_plans';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'required_document_id',
        'plan_id',
        'is_completed',
    ];

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'created_at',
        'updated_at',
    ];

    const IS_NOT_COMPLETED = 0;
    const IS_COMPLETED = 1;
}
