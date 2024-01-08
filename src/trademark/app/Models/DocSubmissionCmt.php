<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class DocSubmissionCmt extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'doc_submission_id',
        'admin_id',
        'content',
        'type',
        'type_comment_of_step',
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
    // const type
    const TYPE_INTERNAL_COMMENT = 1;

    // const type comment of step
    const TYPE_COMMENT_OF_STEP_1 = 1;
    const TYPE_COMMENT_OF_STEP_2 = 2;
    const TYPE_COMMENT_OF_STEP_3 = 3;
    const TYPE_COMMENT_OF_STEP_4 = 4;
    const TYPE_COMMENT_OF_STEP_5 = 5;
    const TYPE_COMMENT_OF_STEP_6 = 6;

    /**
     * Parse CreatedAt
     *
     * @return string
     */
    public function parseCreatedAt(): string
    {
        return Carbon::parse($this->created_at)->format('Y/m/d');
    }
}
