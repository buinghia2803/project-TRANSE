<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RequiredDocumentDetail extends BaseModel
{
    protected $table = 'required_document_details';

    const IS_NOT_CONFIRM = 1;
    const IS_CONFIRM = 1;

    const IS_COMPLETED_TRUE = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'required_document_id',
        'plan_detail_doc_id',
        'attachment_user',
        'is_completed',
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

    /**
     * Plan detail doc
     *
     * @return BelongsTo
     */
    public function planDetailDoc(): BelongsTo
    {
        return $this->belongsTo(PlanDetailDoc::class, 'plan_detail_doc_id', 'id');
    }
}
