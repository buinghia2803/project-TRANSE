<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Precheck extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'trademark_id',
        'type_precheck',
        'pack',
        'is_cancel',
        'is_mailing_regis_cert',
        'period_registration',
        'flag_role',
        'status_register',
        'is_confirm',
    ];

    // Flag role
    const FLAG_ROLE_1 = 1;
    const FLAG_ROLE_2 = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    const TYPE_CHECK_SIMPLE = 1;
    const TYPE_CHECK_SELECT = 2;

    /**
     * Redirect to
     */
    const REDIRECT_TO_QUOTE = 'QUOTE';
    const REDIRECT_TO_ANKEN_TOP = 'ANKEN_TOP';
    const REDIRECT_TO_COMMON_PAYMENT = 'GTCP';

    /**
     * Const type precheck
     */
    const TYPE_PRECHECK_SIMPLE_REPORT = 1;
    const TYPE_PRECHECK_DETAILED_REPORT = 2;

    /**
     * Const Redirect
     */
    const REDIRECT_TO_U021C = 'u021c';
    const REDIRECT_TO_SUGGEST_AI = 'u020b';

    /**
     * Const status_register
     */
    const NOT_STATUS_REGISTER = 1;
    const HAS_STATUS_REGISTER = 2;

    const STATUS_REGISTER_SAVE = 2;

    const IS_CONFIRM_TRUE = 1;
    const IS_CONFIRM_FALSE = 0;

    /**
     * Payer Information
     *
     * @return BelongsTo
     */
    public function trademark(): BelongsTo
    {
        return $this->belongsTo(Trademark::class);
    }

    /**
     * PrecheckProduct
     *
     * @return HasMany
     */
    public function precheckProduct(): HasMany
    {
        return $this->hasMany(PrecheckProduct::class);
    }

     /**
     * List type precheck options
     */
    public static function listTypePrecheckOptions()
    {
        return [
            self::TYPE_PRECHECK_SIMPLE_REPORT => __('labels.precheck.simple_report'),
            self::TYPE_PRECHECK_DETAILED_REPORT => __('labels.precheck.detailed_report'),
        ];
    }

    /**
     * Get Text Precheck Type
     *
     * @return string
     */
    public function getTextPrecheckType(): string
    {
        return $this->listTypePrecheckOptions()[$this->type_precheck];
    }

    /**
     * Products
     *
     * @return BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(MProduct::class, 'precheck_products', 'precheck_id', 'm_product_id');
    }

    /**
     * Precheck results
     *
     * @return HasMany
     */

    public function precheckResults(): HasMany
    {
        return $this->hasMany(PrecheckResult::class, 'precheck_id', 'id');
    }

    /**
     * Precheck products
     *
     * @return HasMany
     */
    public function precheckProducts(): HasMany
    {
        return $this->hasMany(PrecheckProduct::class, 'precheck_id', 'id');
    }

    /**
     * Precheck comments
     *
     * @return HasMany
     */
    public function precheckComments(): HasMany
    {
        return $this->hasMany(PrecheckComment::class, 'precheck_id', 'id');
    }

    /**
     * PrecheckResult
     *
     * @return HasOne
     */
    public function precheckResult(): HasOne
    {
        return $this->hasOne(PrecheckResult::class);
    }
}
