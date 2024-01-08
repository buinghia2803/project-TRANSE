<?php

namespace App\Models;

use App\Helpers\FileHelper;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Trademark extends BaseModel
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'trademark_number',
        'application_number',
        'type_trademark',
        'name_trademark',
        'image_trademark',
        'reference_number',
        'status_management',
        'application_date',
        'comment_refusal',
        'is_refusal',
        'created_at',
        'block_by',
    ];

    /**
     * Const
     */
    const TRADEMARK_TYPE_LETTER = 1;
    const TRADEMARK_TYPE_OTHER = 2;
    const TRADEMARK_STATUS_MANAGEMENT = 1;
    const TRADEMARK_STATUS_NOT_MANAGEMENT = 0;

    /**
     * Const is refusal.
     */
    const IS_REFUSAL_NOT_REFUSAL = 1;
    const IS_REFUSAL_REFUSAL = 2;
    const IS_REFUSAL_CONFIRM = 3;

    /**
     * Get image trademark
     *
     * @return boolean
     */
    public function isTrademarkLetter(): bool
    {
        return $this->type_trademark == Trademark::TRADEMARK_TYPE_LETTER;
    }

    /**
     * App Trademark info
     *
     * @return HasOne
     */
    public function appTrademark(): HasOne
    {
        return $this->hasOne(AppTrademark::class);
    }

    /**
     * Register trademark info
     *
     * @return HasOne
     */
    public function registerTrademark(): HasOne
    {
        return $this->hasOne(RegisterTrademark::class)->latestOfMany();
    }

    /**
     * Register trademark info
     *
     * @return HasMany
     */
    public function registerTrademarks(): HasMany
    {
        return $this->hasMany(RegisterTrademark::class);
    }

    /**
     * User
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    /**
     * User Trademark
     *
     * @return HasOne
     */
    public function userTrademark(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    /**
     * List trademark type options
     */
    public static function listTradeMarkTypeOptions()
    {
        return [
            self::TRADEMARK_TYPE_LETTER => '文字',
            self::TRADEMARK_TYPE_OTHER => 'それ以外（装飾文字、ロゴ絵柄等）',
        ];
    }

    /**
     * User Trademark
     *
     * @return HasOne
     */
    public function supportFirstTime(): HasOne
    {
        return $this->hasOne(SupportFirstTime::class, 'trademark_id');
    }

    /**
     * Comparison Trademark Result
     *
     * @return HasOne
     */
    public function comparisonTrademarkResult(): HasOne
    {
        return $this->hasOne(ComparisonTrademarkResult::class, 'trademark_id');
    }

    /**
     * Comparison Trademark Results
     *
     * @return HasMany
     */
    public function comparisonTrademarkResults(): HasMany
    {
        return $this->hasMany(ComparisonTrademarkResult::class, 'trademark_id');
    }

    /**
     * Notice
     *
     * @return BelongsTo
     */
    public function notice(): BelongsTo
    {
        return $this->belongsTo(Notice::class, 'id', 'trademark_id');
    }

    /**
     * All Notice of trademark
     *
     * @return HasMany
     */
    public function notices(): HasMany
    {
        return $this->hasMany(Notice::class);
    }

    /*
     * Prechecks
     *
     * @return HasMany
     */
    public function prechecks(): HasMany
    {
        return $this->hasMany(Precheck::class, 'trademark_id');
    }

    /**
     * Free Histories
     *
     * @return HasMany
     */
    public function freeHistories(): HasMany
    {
        return $this->hasMany(FreeHistory::class, 'trademark_id');
    }

    /*
     * Free History
     *
     * @return HasOne
     */
    public function freeHistory(): HasOne
    {
        return $this->hasOne(FreeHistory::class, 'trademark_id', 'id');
    }

    /*
     * Maching Result
     *
     * @return HasOne
     */
    public function machingResult(): HasOne
    {
        return $this->hasOne(MatchingResult::class, 'trademark_id', 'id');
    }

    /*
     * Maching Result
     *
     * @return HasMany
     */
    public function machingResults(): HasMany
    {
        return $this->HasMany(MatchingResult::class);
    }

    /*
     * Plan Comments
     *
     * @return HasMany
     */
    public function planComments(): HasMany
    {
        return $this->hasMany(PlanComment::class, 'trademark_id');
    }

    /**
     * Relation of trademark with payment.
     *
     * @return HasOne
     */
    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    /**
     * Relation of trademark with payment.
     *
     * @return HasMany
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Change Info Registers
     *
     * @return void
     */
    public function changeInfoRegisters()
    {
        return $this->hasMany(ChangeInfoRegister::class, 'trademark_id');
    }

    /**
     * Trademark Documents
     *
     * @return void
     */
    public function trademarkDocuments()
    {
        return $this->hasMany(TrademarkDocument::class, 'trademark_id');
    }

    /**
     * Trademark renewal notice.
     *
     * @return void
     */
    public function trademarkRenewalNotices()
    {
        return $this->hasMany(TrademarkRenewalNotice::class);
    }

    /**
     * Get type name of trademark
     */
    public function getTypeName()
    {
        return $this->type_trademark == self::TRADEMARK_TYPE_LETTER ? '文字' : 'それ以外（装飾文字、ロゴ絵柄等）';
    }

    /**
     * Get image trademark
     *
     * @return string
     */
    public function getImageTradeMark(): string
    {
        return FileHelper::getImage($this->image_trademark);
    }

    /**
    * Trademark prod register CSC
    *
    * @return HasMany
    */
    public function registerTrademarkRenewals(): HasMany
    {
        return $this->hasMany(RegisterTrademarkRenewal::class);
    }

    /**
     * Get matching result from document name
     */
    public function getMatchingResultFrmDocName(int $type)
    {
        $documentName = '';
        switch ($type) {
            case N_FLOW_TYPE_APP:
                $documentName = '出願';
                break;
            case N_FLOW_TYPE_REASON_REFUSAL:
                $documentName = '拒絶理由通知対応';
                break;
            case N_FLOW_TYPE_REGISTRATION:
                $documentName = '登録査定';
                break;
            case N_FLOW_TYPE_FREE_HISTORY:
                $documentName = 'フリー履歴';
                break;
            default:
                return null;
        }

        return MatchingResult::where([
            'pi_document_name' => $documentName,
            'trademark_id' => $this->id
        ])->first();
    }

    /**
     * Get created fot tbl trademark not apply.
     *
     * @return string
     */
    public function getCreateAtNotApply(): string
    {
        if (isset($this->appTrademark) && $this->appTrademark) {
            return Carbon::parse($this->appTrademark->created_at)->format('Y/m/d');
        } elseif (isset($this->supportFirstTime) && $this->supportFirstTime) {
            return Carbon::parse($this->supportFirstTime->created_at)->format('Y/m/d');
        } elseif (isset($this->prechecks) && $this->prechecks->count()) {
            return isset($this->prechecks[0])
                ? Carbon::parse($this->prechecks->sortBy(['created_at', 'desc'])[0]->created_at)->format('Y/m/d')
                : '';
        }

        return '';
    }

    /**
     * Get products with relation
     *
     * @return array
     */
    public function getProductsWithRelation(): array
    {
        $trademarkProds = null;

        $this->load([
            'appTrademark.products',
            'appTrademark.appTrademarkProd',
            'supportFirstTime.StfSuitableProduct',
            'prechecks.products',
        ]);

        if (isset($this->appTrademark) && $this->appTrademark) {
            $trademarkProds = $this->appTrademark->appTrademarkProd;
        } elseif (isset($this->supportFirstTime) && $this->supportFirstTime) {
            $trademarkProds = $this->supportFirstTime->StfSuitableProduct;
        } elseif (isset($this->prechecks) && $this->prechecks->count()) {
            $trademarkProds = $this->prechecks->sortBy(['created_at', 'desc'])->first()->products;
        }

        if (!$trademarkProds) {
            return [];
        }
        $mProducts = $trademarkProds->map(function ($item) {
            if (isset($this->prechecks) && $this->prechecks->count()) {
                return $item;
            } else {
                return $item->mProduct;
            }
        });

        $products = $mProducts->groupBy('mDistinction.name');
        $content = '';
        foreach ($products as $distinction => $prod) {
            if ($prod->count() > 0 && $distinction) {
                $content = $content . $distinction . '：' . $prod->implode('name', ', ') . ($distinction < count($products) ? ', ' : '');
            }
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
        if ($strLength > $len) {
            for ($i = 0; $i < round($strLength / $len); $i++) {
                $arr[] = mb_substr($str, $start, $len);
                $start += $len;
            }
            return $arr;
        }

        return [$str];
    }

    /**
     * Get type trademark
     *
     * @return string
     */
    public function getTypeTrademark(): string
    {
        return $this->listTradeMarkTypeOptions()[$this->type_trademark] ?? '';
    }

    /**
     * Get color when anken expired.
     *
     * @return string
     */
    public function getClassBackground(): string
    {
        if (isset($this->comparisonTrademarkResult)) {
            if (now()->timestamp > Carbon::parse($this->comparisonTrademarkResult->response_deadline)->timestamp) {
                return 'bg_pink';
            } elseif (now() >= Carbon::parse($this->comparisonTrademarkResult->response_deadline)->subDays(10)
                && now()->timestamp <= Carbon::parse($this->comparisonTrademarkResult->response_deadline)->timestamp
            ) {
                return 'bg_green';
            } else {
                return '';
            }
        }

        return '';
    }

    /**
     * Get agent and check existence registerTrademark.
     *
     * @param int $registerTrademarkId
     * @return void
     */
    public function getAgentAndCheckExistenceRegisterTrademark(int $registerTrademarkId)
    {
        return $this->whereHas('registerTrademark', function ($q) use ($registerTrademarkId) {
            return $q->where('id', $registerTrademarkId);
        })->with([
            'registerTrademark.registerTrademarkProds.appTrademarkProd.mProduct',
            'appTrademark.agentGroup' => function ($q) {
                return $q->where('status_choice', AgentGroup::STATUS_CHOICE_TRUE);
            },
            'appTrademark.agentGroup.collectAgent' => function ($q) {
                return $q->where('type', AgentGroupMap::TYPE_NOMINATED);
            },
            'appTrademark.agentGroup.collectAgent.agent'
        ])->first();
    }

    /**
     * Check is cancel of trademark
     *
     * @return boolean
     */
    public function isCancel(): bool
    {
        $isCancel = false;

        $appTrademark = $this->appTrademark ?? null;
        if (!empty($appTrademark) && $appTrademark->is_cancel == AppTrademark::IS_CANCEL_TRUE) {
            $isCancel = true;
        }

        $registerTrademark = $this->registerTrademark ?? null;
        if (!empty($registerTrademark) && $registerTrademark->is_cancel == RegisterTrademark::IS_CANCEL) {
            $isCancel = true;
        }

        $ctr = $this->comparisonTrademarkResults->sortByDesc('id')->first();
        if (!empty($ctr) && $ctr->is_cancel == IS_CANCEL_TRUE) {
            $isCancel = true;
        }

        $planCorrespondences = $ctr->planCorrespondences ?? collect([]);
        if (!empty($planCorrespondences)) {
            $planCorrespondence = $planCorrespondences->sortByDesc('id')->first();

            if (!empty($planCorrespondence)) {
                $trademarkPlans = $planCorrespondence->trademarkPlans ?? collect([]);
                $trademarkPlan = $trademarkPlans->sortByDesc('id')->first();

                if (!empty($trademarkPlan) && $trademarkPlan->is_cancel == IS_CANCEL_TRUE) {
                    $isCancel = true;
                }
            }
        }

        return $isCancel;
    }

    /**
     * Format Application Number
     *
     * @return string|null
     */
    public function formatApplicationNumber(): ?string
    {
        if (empty($this->application_number)) {
            return null;
        }

        return __('labels.invoice.application_number_value', [
            'attribute' => mb_convert_kana(substr($this->application_number, 0, 4) . '-' . substr($this->application_number, 4), 'ASV'),
        ]);
    }
}
