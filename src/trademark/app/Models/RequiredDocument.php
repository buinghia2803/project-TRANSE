<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;

class RequiredDocument extends BaseModel
{
    protected $table = 'required_documents';

    const IS_NOT_SEND = 0;
    const IS_SEND = 1;

    const IS_NOT_CONFIRM = 0;
    const IS_CONFIRM = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trademark_plan_id',
        'response_deadline',
        'is_confirm',
        'is_send',
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

    const IS_CONFIRM_TRUE = 1;

    /**
     * Required document Detail
     *
     * @return HasMany
     */
    public function RequiredDocumentDetails(): HasMany
    {
        return $this->hasMany(RequiredDocumentDetail::class);
    }

    /**
     * Required document Detail
     *
     * @return HasMany
     */
    public function RequiredDocumentMiss(): HasMany
    {
        return $this->hasMany(RequiredDocumentMiss::class);
    }

    /**
     * Required document plan
     *
     * @return HasMany
     */
    public function RequiredDocumentPlans(): HasMany
    {
        return $this->hasMany(RequiredDocumentPlan::class);
    }

    /**
     * Required document Comment
     *
     * @return HasMany
     */
    public function RequiredDocumentComments(): HasMany
    {
        return $this->hasMany(RequiredDocumentComment::class);
    }
}
