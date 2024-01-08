<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequiredDocumentComment extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'required_document_id',
        'from_send_doc',
        'content',
        'type_comment_step',
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

    const TYPE_COMMENT_STEP_1 = 1;
    const TYPE_COMMENT_STEP_2 = 2;

    const FROM_SEND_DOC_U204 = 'u204';
    const FROM_SEND_DOC_U204N = 'u204n';
}
