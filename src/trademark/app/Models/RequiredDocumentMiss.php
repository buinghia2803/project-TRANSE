<?php

namespace App\Models;

class RequiredDocumentMiss extends BaseModel
{
    protected $table = 'required_document_miss';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'required_document_id',
        'plan_id',
        'description_docs_miss',
        'from_send_doc',
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
}
