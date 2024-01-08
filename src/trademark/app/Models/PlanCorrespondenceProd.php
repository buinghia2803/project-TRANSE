<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class PlanCorrespondenceProd extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'plan_correspondence_id',
        'is_register',
        'app_trademark_prod_id',
        'completed_evaluation',
        'round',
        'updated_at',
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

    // const
    const IS_REGISTER = 1;
    const IS_REGISTER_FALSE = 0;
    const COMPLETED_EVALUATION_TRUE = 1;
    const COMPLETED_EVALUATION_FALSE = 0;
    /*
     * App Trademark Prod
     *
     * @return HasOne
     */
    public function appTrademarkProd(): HasOne
    {
        return $this->hasOne(AppTrademarkProd::class, 'id', 'app_trademark_prod_id');
    }

    /*
     * Reason Ref Num Prod
     *
     * @return HasOne
     */
    public function reasonRefNumProd(): HasOne
    {
        return $this->hasOne(ReasonRefNumProd::class, 'plan_correspondence_prod_id', 'id');
    }

    /*
     * Reason Ref Num Prod
     *
     * @return HasOne
     */
    public function reasonRefNumProds(): HasMany
    {
        return $this->hasMany(ReasonRefNumProd::class, 'plan_correspondence_prod_id', 'id');
    }
}
