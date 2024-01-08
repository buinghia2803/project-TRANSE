<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DocSubmissionAttachProperty extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'doc_submission_id',
        'name',
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

    public $selectable = [
        '*',
    ];

    /**
     * Doc submission
     *
     * @return BelongsTo
     */
    public function docSubmission(): BelongsTo
    {
        return $this->belongsTo(DocSubmission::class, 'doc_submission_id', 'id');
    }

    /**
     * Doc Submission Attachments
     *
     * @return HasMany
     */
    public function docSubmissionAttachments(): HasMany
    {
        return $this->hasMany(DocSubmissionAttachment::class, 'doc_submission_attach_property_id', 'id');
    }
}
