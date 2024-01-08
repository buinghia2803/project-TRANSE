<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MTypePlan extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'content',
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

    /**
     * Plan Details
     *
     * @return HasMany
     */
    public function planDetails(): HasMany
    {
        return $this->hasMany(PlanDetail::class);
    }

    /**
     * M Type Plan Docs
     *
     * @return HasMany
     */
    public function mTypePlanDocs(): HasMany
    {
        return $this->hasMany(MTypePlanDoc::class);
    }

    /**
     * M Type Plan Docs
     *
     * @return HasMany
     */
    public function mTypePlanDocEdits(): HasMany
    {
        return $this->hasMany(MTypePlanDoc::class, 'm_type_plan_doc_id_edit');
    }
}
