<?php

namespace App\Models;

use App\Helpers\CommonHelper;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ReasonRefNumProd extends Model
{
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reason_id',
        'reason_no_id',
        'plan_correspondence_prod_id',
        'admin_id',
        'comment_patent_agent',
        'vote_reason_id',
        'rank',
        'is_choice',
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

    //拒絶理由通知対応/費用を選択するステータス. 0:false | 1: true
    const IS_NOT_CHOICE = 0;
    const IS_CHOICE = 1;

    /**
     * Plan Correspondence Prod
     *
     * @return HasOne
     */
    public function planCorrespondenceProd(): HasOne
    {
        return $this->hasOne(PlanCorrespondenceProd::class, 'id', 'plan_correspondence_prod_id');
    }

    /**
     * Return price with rank
     *
     * @param MPriceList $selectPlanA
     * @param MPriceList $selectPlanOther
     * @param Setting $setting
     */
    public function priceWithRank($selectPlanA, $selectPlanOther, $setting): string
    {
        if ($this->rank == 'A') {
            return CommonHelper::formatPrice($selectPlanA->base_price + ($selectPlanA->base_price * $setting->value / 100));
        }

        return CommonHelper::formatPrice($selectPlanOther->base_price + ($selectPlanOther->base_price * $setting->value / 100));
    }
}
