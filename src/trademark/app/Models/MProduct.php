<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MProduct extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'm_distinction_id',
        'admin_id',
        'products_number',
        'name',
        'type',
        'rank',
        'total_order',
        'block',
        'is_parent',
        'parent_id',
        'is_check',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * Const type
     */
    const TYPE_ORIGINAL_CLEAN = 1;
    const TYPE_REGISTERED_CLEAN = 2;
    const TYPE_CREATIVE_CLEAN = 3;
    const TYPE_SEMI_CLEAN = 4;

    const IS_CHOICE_TRUE = 1;
    const IS_NOT_CHOICE = 0;

    /**
     * Const is_parent
     */
    const IS_PARENT = 1;
    const IS_NOT_PARENT = 0;

    /**
     * MDistinction
     *
     * @return HasOne
     */
    public function mDistinction(): HasOne
    {
        return $this->hasOne(MDistinction::class, 'id', 'm_distinction_id');
    }

    /**
     * Product code.
     *
     * @return HasMany
     */
    public function productCode(): HasMany
    {
        return $this->hasMany(MProductCode::class);
    }

    /**
     * Sft suitable product.
     *
     * @return HasOne
     */
    public function SftSuitableProduct(): HasOne
    {
        return $this->hasOne(SFTSuitableProduct::class);
    }

    /**
     * MProductCode
     *
     * @return HasMany
     */
    public function mProductCode(): HasMany
    {
        return $this->hasMany(MProductCode::class);
    }

    /**
     * MCode
     *
     * @return BelongsToMany
     */
    public function mCode(): belongsToMany
    {
        return $this->belongsToMany(MCode::class, 'm_product_codes', 'm_product_id', 'm_code_id');
    }

    /**
     * Prechecks
     *
     * @return BelongsToMany
     */
    public function prechecks()
    {
        return $this->belongsToMany(Precheck::class, 'precheck_products', 'm_product_id', 'precheck_id')->withPivot(['is_register_product', 'is_apply']);
    }

    /**
     * App_trademarks
     *
     * @return BelongsToMany
     */
    public function app_trademarks()
    {
        return $this->belongsToMany(AppTrademark::class, 'app_trademark_prods', 'm_product_id', 'app_trademark_id')->withPivot('is_apply');
    }
    /**
     * MProductCode
     *
     * @return BelongsToMany
     */
    public function code(): belongsToMany
    {
        return $this->belongsToMany(MCode::class, 'm_product_codes', 'm_product_id', 'm_code_id');
    }

    /**
     * PrecheckResults
     *
     * @return HasManyThrough
     */
    public function precheckResults(): hasManyThrough
    {
        return $this->hasManyThrough(PrecheckResult::class, PrecheckProduct::class);
    }

    /**
     * Precheck
     *
     * @return BelongsToMany
     */
    public function precheck(): BelongsToMany
    {
        return $this->belongsToMany(Precheck::class, 'precheck_products', 'm_product_id', 'precheck_id');
    }

    /**
     * PrecheckProduct
     *
     * @return HasMany
     */
    public function precheckProduct(): HasMany
    {
        return $this->hasMany(PrecheckProduct::class, 'm_product_id');
    }

    /**
     * PrecheckKeepDatas
     *
     * @return BelongsToMany
     */
    public function precheckKeepDatas(): BelongsToMany
    {
        return $this->belongsToMany(PrecheckKeepData::class, 'precheck_keep_data_prods', 'm_product_id', 'precheck_keep_data_id');
    }

    /**
     * PrecheckKeepDataProd
     *
     * @return HasMany
     */
    public function precheckKeepDataProd(): HasMany
    {
        return $this->HasMany(PrecheckKeepDataProd::class);
    }

    /**
     * Relation with plan_detail_products
     *
     * @return HasMany
     */
    public function planDetailProducts(): HasMany
    {
        return $this->hasMany(PlanDetailProduct::class);
    }

    /**
     * Relation with plan_detail_products
     *
     * @return HasOne
     */
    public function planDetailProduct(): HasOne
    {
        return $this->hasOne(PlanDetailProduct::class);
    }

    /**
     * App Trademark Prod
     *
     * @return HasOne
     */
    public function appTrademarkProd(): HasOne
    {
        return $this->HasOne(AppTrademarkProd::class);
    }

    /**
     * App Trademark Prod
     *
     * @return HasMany
     */
    public function registerTrademarkProds(): HasMany
    {
        return $this->HasMany(RegisterTrademarkProd::class);
    }

    /**
     * App Trademark Prod
     *
     * @return HasOne
     */
    public function registerTrademarkProd(): HasOne
    {
        return $this->HasOne(RegisterTrademarkProd::class);
    }

    public function parent(): HasOne
    {
        return $this->HasOne(MProduct::class, 'id', 'parent_id');
    }

    /**
     * Get first product by name
     *
     * @return Model
     */
    public static function getMProductByName($name)
    {
        return MProduct::where('name', $name)->first();
    }

    /**
     * Get Class color by type product
     *
     * @return string
     */
    public function getClassColorByTypeProduct(): string
    {
        $result = '';
        switch ($this->type) {
            case $this::TYPE_CREATIVE_CLEAN:
                $result = 'bg_yellow';
                break;
            case $this::TYPE_SEMI_CLEAN:
                $result = 'bg_pink';
                break;
            default:
                $result = '';
        }
        return $result;
    }

    /**
     * Get color row by plan detail prod.
     *
     * @return string
     */
    public function getColorRowByPlanDetailProd($roleAdd = null): string
    {
        $class = '';
        $roleAdds = $roleAdd ? collect($roleAdd) : $this->planDetailProducts->pluck('role_add')->unique();
        if ($roleAdds->count() == 1) {
            switch ($roleAdds[0]) {
                case ROLE_MANAGER:
                    $class = 'bg_yellow';
                    break;
                case ROLE_SUPERVISOR:
                    $class = 'bg_purple2';
                    break;
            }

            return $class;
        }

        return '';
    }

    /**
     * GetConditionFilters
     *
     * @return array
     */
    public function getConditionCompare(): array
    {
        return [
            EQUAL => __('labels.payment_all.equal'),
            IS_GREATER_THAN => __('labels.payment_all.is_greater_than'),
            IS_LESS_THAN => __('labels.payment_all.is_less_than'),
        ];
    }

    /**
     * GetConditionCompare
     *
     * @return array
     */
    public function getConditionFilters(): array
    {
        return [
            EQUAL => __('labels.payment_all.equal'),
            START_FROM => __('labels.payment_all.start_from'),
            CONSISTS_OF => __('labels.payment_all.consists_of'),
        ];
    }

    /**
     * GetOptionField
     *
     * @return array
     */
    public function getOptionField(): array
    {
        return [
            SEARCH_DISTINCTION_NAME => __('labels.distinction_name'),
            SEARCH_PRODUCT_NAME => __('labels.product_labels'),
            SEARCH_CODE_NAME => __('labels.code_labels'),
            SEARCH_CONCEPT => __('labels.qa_labels'),
        ];
    }
}
