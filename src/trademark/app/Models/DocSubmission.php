<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DocSubmission extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trademark_plan_id',
        'is_register_change_info',
        'is_written_opinion',
        'description_written_opinion',
        'data_a205',
        'admin_id',
        'is_confirm',
        'flag_role',
        'is_reject',
        'filing_date',
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
     * Const is_written_opinion
     */
    const IS_WRITTEN_OPINION_FALSE = 0;
    const IS_WRITTEN_OPINION_TRUE = 1;

    /**
     * Const is_reject
     */
    const IS_REJECT_FALSE = 0;
    const IS_REJECT_TRUE = 1;

    /**
     * Const flag_role
     */
    const FLAG_ROLE_1 = 1;
    const FLAG_ROLE_2 = 2;

    /**
     * Const is_confirm
     */
    const IS_CONFIRM_FALE = 0;
    const IS_CONFIRM = 1;

    /**
     * Doc Submission Attach Properties
     *
     * @return HasMany
     */
    public function docSubmissionAttachProperties(): HasMany
    {
        return $this->hasMany(DocSubmissionAttachProperty::class, 'doc_submission_id', 'id');
    }
    /*
     * Doc Submission Cmt
     *
     * @return HasMany
     */
    public function docSubmissionCmts(): HasMany
    {
        return $this->hasMany(DocSubmissionCmt::class);
    }

    /*
     * Format Data A205
     *
     * @return mixed
     */
    public function formatDataA205()
    {
        if (empty($this->data_a205)) {
            return null;
        }

        $dataA205 = json_decode($this->data_a205, true);

        $docSubmissionAttachProperties = [];
        foreach ($dataA205['doc_submission_attach_properties'] as $key => $value) {
            $value['docSubmissionAttachments'] = collect($value['doc_submission_attachments'] ?? []);

            $docSubmissionAttachProperties[$key] = $value;
        }

        $dataA205['docSubmissionAttachProperties'] = $docSubmissionAttachProperties;

        return json_decode(json_encode($dataA205));
    }
}
