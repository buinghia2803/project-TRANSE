<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class AppTrademark extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        "trademark_id",
        "admin_id",
        "agent_group_id",
        "cancellation_deadline",
        "comment_office",
        "status",
        "pack",
        "is_mailing_regis_cert",
        "period_registration",
        "is_cancel",
        "type_page",
    ];

    // const pack
    const PACK_A = 1;
    const PACK_B = 2;
    const PACK_C = 3;
    const APPLY = 1;
    const NOT_APPLY = 0;

    // status
    const STATUS_UNREGISTERED_SAVE = 0;
    const STATUS_WAITING_FOR_ADMIN_CONFIRM = 1;
    const STATUS_WAITING_FOR_USER_CONFIRM = 2;
    const STATUS_ADMIN_CONFIRM = 3;

    /**
     * Const is_mailing_regis_cert
     */
    const IS_MAILING_REGIS_CERT_TRUE = 1;
    const IS_MAILING_REGIS_CERT_FAlSE = 0;

    const PERIOD_REGISTRATION_TRUE = 2;
    const PERIOD_REGISTRATION_FALSE = 1;

    /**
     * Const period_registration
     */
    const PERIOD_REGISTRATION_5_YEAR = 1;
    const PERIOD_REGISTRATION_10_YEAR = 2;

    /**
     * Page types
     */
    const PAGE_TYPE_U011B = 1;
    const PAGE_TYPE_U011B_31 = 2;
    const PAGE_TYPE_U021B = 3;
    const PAGE_TYPE_U021B_31 = 4;
    const PAGE_TYPE_U031 = 5;
    const PAGE_TYPE_U031EDIT = 6;
    const PAGE_TYPE_U031B = 7;
    const PAGE_TYPE_U031C = 8;
    const PAGE_TYPE_U031EDIT_WITH_NUMBER = 9;
    const PAGE_TYPE_U031D = 10;

    /**
     *  Is cancel const
     */
    const IS_CANCEL_FALSE = 0;
    const IS_CANCEL_TRUE = 1;

    /**
     * Redirect to
     */
    const REDIRECT_TO_QUOTE = 'QUOTE';
    const REDIRECT_TO_ANKEN_TOP = 'ANKEN_TOP';
    const REDIRECT_TO_COMMON_PAYMENT = 'GTCP';
    const REDIRECT_TO_GMO_THANK_YOU = 'GMOTK';
    const REDIRECT_TO_U021 = 'U021';

    const COMPLETED_EVALUATION_0 = 0;
    /**
     * Payer Information
     *
     * @return BelongsTo
     */
    public function trademark(): BelongsTo
    {
        return $this->belongsTo(Trademark::class, 'trademark_id', 'id');
    }

    /**
     * Relation of App Trademark with trademark information
     *
     * @return BelongsTo
     */
    public function trademarkInfo(): HasMany
    {
        return $this->hasMany(TrademarkInfo::class, 'target_id', 'id')->where('type', TrademarkInfo::TYPE_TRADEMARK);
    }

    /**
     * Relation of App Trademark Prod
     *
     * @return HasMany
     */
    public function appTrademarkProd(): HasMany
    {
        return $this->hasMany(AppTrademarkProd::class);
    }

    /**
     * Relation of App Trademark Prod
     *
     * @return BelongsTo
     */
    public function agentGroup(): BelongsTo
    {
        return $this->belongsTo(AgentGroup::class, 'agent_group_id');
    }

    /**
     * Products
     *
     * @return BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(MProduct::class, 'app_trademark_prods', 'app_trademark_id', 'm_product_id');
    }

    /**
     * Get Pack name
     *
     * @return null|string
     */
    public function getPackName(): ?string
    {
        switch ($this->pack) {
            case self::PACK_A:
                $packName = 'パックA  （3商品名まで、商標出願）';
                break;
            case self::PACK_B:
                $packName = 'パックB  （3商品名まで、商標出願＋登録手続）';
                break;
            case self::PACK_C:
                $packName = 'パックC （3商品名まで、商標出願＋拒絶理由通知対応＋登録手続）';
                break;
            default:
                $packName = null;
        }

        return $packName;
    }

    /**
     * Get Short Pack name
     *
     * @return null|string
     */
    public function getShortPackName(): ?string
    {
        switch ($this->pack) {
            case self::PACK_A:
                $packName = 'パックA';
                break;
            case self::PACK_B:
                $packName = 'パックB';
                break;
            case self::PACK_C:
                $packName = 'パックC';
                break;
            default:
                $packName = null;
        }

        return $packName;
    }

    /**
     * Get package type of m price up to 3 items.
     *
     * @return string
     */
    public function getPackageTypeMPriceUp3()
    {
        switch ($this->pack) {
            case self::PACK_A:
                $packageType = MPriceList::PACK_A_UP_3_ITEMS;
                break;
            case self::PACK_B:
                $packageType = MPriceList::PACK_B_UP_3_ITEMS;
                break;
            case self::PACK_C:
                $packageType = MPriceList::PACK_C_UP_3_ITEMS;
                break;
        }

        return $packageType;
    }

    /**
     * Get package type of m price up to 3 items.
     *
     * @return string
     */
    public function getPackageTypeMPriceEach3()
    {
        switch ($this->pack) {
            case self::PACK_A:
                $packageType = MPriceList::PACK_A_EACH_3_ITEMS;
                break;
            case self::PACK_B:
                $packageType = MPriceList::PACK_B_EACH_3_ITEMS;
                break;
            case self::PACK_C:
                $packageType = MPriceList::PACK_C_EACH_3_ITEMS;
                break;
        }

        return $packageType;
    }

    /**
     * Get product and distinction.
     *
     * @return array
     */
    public function getProducts(): array
    {
        $appTrademarkProds = $this->appTrademarkProd;
        $mProducts = $appTrademarkProds->map(function ($item) {
            return $item->mProduct;
        });

        $products = $mProducts->groupBy('mDistinction.name');
        $content = '';
        foreach ($products as $distinction => $prod) {
            $content = $content . $distinction . '：' . $prod->implode('name', ', ') . ($distinction < count($products) ? ', ' : '');
        }

        $results = $this->breakStr($content, 35);

        return $results;
    }

    /**
     * Slice string with length.
     *
     * @param string $str
     * @param int $len
     * @return array
     */
    public function breakStr(string $str, int $len): array
    {
        $arr = [];
        $strLength = mb_strlen($str, 'UTF-8');
        $start = 0;
        for ($i = 0; $i < round($strLength / $len); $i++) {
            $arr[] = mb_substr($str, $start, $len);
            $start += $len;
        }

        return $arr;
    }

    /**
     * Get Pack Detail
     *
     * @return null|string
     */
    public function getPackDetail(): ?string
    {
        $packDetail = null;
        switch ($this->pack) {
            case self::PACK_A:
                $packDetail = __('labels.payment_table.pack_a_detail');
                break;
            case self::PACK_B:
                $packDetail = __('labels.payment_table.pack_b_detail');
                break;
            case self::PACK_C:
                $packDetail = __('labels.payment_table.pack_c_detail');
                break;
        }

        return $packDetail;
    }

    /**
     * Check Cancellation Deadline
     *
     * @return boolean
     */
    public function checkCancellationDeadline(): bool
    {
        $deadlineCancel = Carbon::parse($this->cancellation_deadline)->format('YmdHm');
        $now = Carbon::now()->format('YmdHm');
        if ($now > $deadlineCancel) {
            return true;
        }

        return false;
    }

    /**
     * Get Agent
     *
     * @return ?Model
     */
    public function getAgent(): ?Model
    {
        $appTrademark = $this->load('agentGroup.agentGroupMaps.agent');
        $agentGroup = $appTrademark->agentGroup ?? null;
        if (!$agentGroup || ($agentGroup && !$agentGroup->agentGroupMaps) || ($agentGroup && $agentGroup->status_choice !== AgentGroup::STATUS_CHOICE_TRUE)) {
            return null;
        }
        $agentGroupMaps = $agentGroup->agentGroupMaps->where('type', AgentGroupMap::TYPE_NOMINATED)->first();

        return $agentGroupMaps->agent ?? null;
    }
}
