<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ComparisonTrademarkResult extends Model
{
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'maching_result_id',
        'trademark_id',
        'admin_id',
        'sending_noti_rejection_date',
        'response_deadline',
        'is_send_mail',
        'user_response_deadline',
        'is_cancel',
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

    const STEP_2 = 2;

    /**
     * Parse Sending Noti Rejecttion Date
     *
     * @return void
     */
    public function parseSendingNotiRejecttionDate()
    {
        return Carbon::parse($this->sending_noti_rejection_date)->format('Y年m月d日');
    }

    /**
     * Parse Response Deadline
     *
     * @return string
     */
    public function parseResponseDeadline($months = 0): string
    {
        return Carbon::parse($this->response_deadline)->addMonths($months)->format('Y年m月d日');
    }

    /**
     * Parse User Response Deadline
     *
     * @return string
     */
    public function parseUserResponseDeadline(): string
    {
        return Carbon::parse($this->user_response_deadline)->format('Y年m月d日');
    }

    /**
     * Plan Correspondences
     *
     * @return HasMany
     */
    public function planCorrespondences(): HasMany
    {
        return $this->hasMany(PlanCorrespondence::class);
    }

    /**
     * Plan Correspondence
     *
     * @return HasOne
     */
    public function planCorrespondence(): HasOne
    {
        return $this->hasOne(PlanCorrespondence::class);
    }

    /**
     * Trademark
     *
     * @return BelongsTo
     */
    public function trademark(): BelongsTo
    {
        return $this->belongsTo(Trademark::class, 'trademark_id', 'id');
    }

    /**
     * Maching results
     *
     * @return BelongsTo
     */
    public function machingResult(): BelongsTo
    {
        return $this->belongsTo(MatchingResult::class, 'maching_result_id', 'id');
    }

    /**
     * Get Response Deadline
     *
     * @return void
     */
    public function getResponseDeadline()
    {
        $responseDeadline = Carbon::parse($this->response_deadline)->subDays(RESPONSE_DEADLINE_DAY)->timestamp;
        $now = now()->timestamp;
        if ($responseDeadline >= $now) {
            return true;
        }

        return false;
    }

    /**
     * Get List Product
     *
     * @return Collection
     */
    public function getProducts(): Collection
    {
        $products = [];

        $planCorrespondence = $this->load([
            'planCorrespondence.planCorrespondenceProds.appTrademarkProd.mProduct.mDistinction',
            'planCorrespondence.planCorrespondenceProds.appTrademarkProd.mProduct.mCode',
            'planCorrespondence.planCorrespondenceProds.reasonRefNumProd',
            'planCorrespondence.planCorrespondenceProds.appTrademarkProd.appTrademark',
        ]);

        $planCorrespondence = $planCorrespondence->planCorrespondence;
        if (!empty($planCorrespondence)) {
            $planCorrespondenceProds = $planCorrespondence->planCorrespondenceProds;

            foreach ($planCorrespondenceProds as $planCorrespondenceProd) {
                $appTrademarkProd = $planCorrespondenceProd->appTrademarkProd;
                $reasonRefNumProds = $planCorrespondenceProd->reasonRefNumProds;

                if (!empty(request()->reason_no_id)) {
                    $reasonRefNumProd = $reasonRefNumProds->where('reason_no_id', request()->reason_no_id)->last();
                } else {
                    $reasonRefNumProd = $reasonRefNumProds->first();
                }

                if (!empty($appTrademarkProd)) {
                    $mProduct = $appTrademarkProd->mProduct;

                    if (!empty($mProduct)) {
                        $mProduct->plan_correspondence_prod = $planCorrespondenceProd;
                        $mProduct->app_trademark_prod = $appTrademarkProd;
                        $mProduct->planCorrespondence = $planCorrespondence;

                        $mProduct->reason_ref_num_prod = null;
                        $mProduct->rank = null;
                        $mProduct->comment_patent_agent = null;
                        $mProduct->vote_reason_id = [];
                        if (!empty($reasonRefNumProd)) {
                            $mProduct->reason_ref_num_prod = $reasonRefNumProd;
                            $mProduct->rank = $reasonRefNumProd->rank;
                            $mProduct->comment_patent_agent = $reasonRefNumProd->comment_patent_agent;
                            $mProduct->vote_reason_id = json_decode($reasonRefNumProd->vote_reason_id, true);
                        }

                        $products[] = $mProduct;
                    }
                }
            }
        }

        return collect($products)->unique('id');
    }

    /**
     * Get List Reason
     *
     * @return Collection
     */
    public function getReasons($reasonNoPresent): Collection
    {
        $data = [];

        $planCorrespondence = $this->load([
            'planCorrespondence.reasonNos.reasons.mLawsRegulation',
        ]);

        $planCorrespondence = $planCorrespondence->planCorrespondence;
        if (!empty($planCorrespondence)) {
            $reasonNos = $planCorrespondence->reasonNos;

            if (!empty($reasonNos)) {
                foreach ($reasonNos as $reasonNo) {
                    if ($reasonNo->id == $reasonNoPresent->id) {
                        $reasons = $reasonNo->reasons;

                        foreach ($reasons as $reason) {
                            $mLawsRegulation = $reason->mLawsRegulation;

                            if (!empty($mLawsRegulation)) {
                                $data[] = $reason;
                            }
                        }
                    }
                }
            }
        }

        return collect($data);
    }

    /**
     * Check Response Deadline
     *
     * @return boolean
     */
    public function checkResponseDeadline(): bool
    {
        $now = Carbon::now()->format('Ymd');
        $responseDeadline = Carbon::parse($this->response_deadline)->format('Ymd');
        if ($now > $responseDeadline) {
            return true;
        }

        return false;
    }

    /**
     * Check Response Deadline Alert
     *
     * @return bool
     */
    public function checkResponseDeadlineAlert(): bool
    {
        $responseDeadline = new DateTime($this->response_deadline);
        $condition1 = $this->getAddDayAndHours(3, 12);
        $condition2 = $this->getAddDayAndHours(10);
        if ($responseDeadline > $condition1 && $responseDeadline <= $condition2) {
            return true;
        }

        return false;
    }

    /**
     * Check Response Deadline Over
     *
     * @return bool
     */
    public function checkResponseDeadlineOver(): bool
    {
        $condition = $this->getAddDayAndHours(3, 12);
        $responseDeadline = new DateTime($this->response_deadline);
        if ($responseDeadline <= $condition) {
            return true;
        }

        return false;
    }

    /**
     * Get Add Day And Hours
     *
     * @param  mixed $days
     * @param  mixed $hours
     * @return object
     */
    public function getAddDayAndHours($days = 0, $hours = 0)
    {
        $now = new DateTime('now' . '+' . $days . 'day');
        $newNow = $now->modify('+' . $hours . 'hour');

        return $newNow;
    }

    /**
     * Check Response Deadline Alert
     *
     * @return bool
     */
    public function checkResponseDeadlineU210Alert(): bool
    {
        $condition1 = $this->getAddDayAndHours(3, 12);
        $condition2 = $this->getAddDayAndHours(10);
        $responseDeadline = new DateTime($this->response_deadline);
        if ($responseDeadline > $condition1 && $responseDeadline <= $condition2) {
            return true;
        }

        return false;
    }

    /**
     * Check Response Deadline Over
     *
     * @return bool
     */
    public function checkRegistrationOverdue(): bool
    {
        $condition = $this->getAddDayAndHours(3, 12);
        $responseDeadline = new DateTime($this->response_deadline);
        if ($responseDeadline <= $condition) {
            return true;
        }

        return false;
    }

    /**
     * Check Response Deadline Over
     *
     * @return bool
     */
    public function checkResponseDeadlineu210Over(): bool
    {
        $condition = $this->getAddDayAndHours(3, 12);
        $responseDeadline = new DateTime($this->response_deadline);
        if ($responseDeadline <= $condition) {
            return true;
        }

        return false;
    }

    /**
     * Check Condition Access Screen
     *
     * @return bool
     */
    public function checkConditionAccessScreenU210Alert()
    {
        $responseDeadline = new DateTime($this->response_deadline);
        $sendingNotiRejectionDate = $this->getAddDayAndMonth(40);
        if ($responseDeadline <= $sendingNotiRejectionDate) {
            return true;
        }

        return false;
    }

    /**
     * Check Overdue Register
     *
     * @return bool
     */
    public function checkOverdueRegister(): bool
    {
        $now = new DateTime;
        $sendingNotiRejectionDate1 = $this->getAddDayAndMonth(40, 2);
        $sendingNotiRejectionDate2 = $this->getAddDayAndMonth(40, 3);
        if ($now > $sendingNotiRejectionDate1 || $now > $sendingNotiRejectionDate2) {
            return true;
        }

        return false;
    }

    /**
     * Get Add Day And Month
     *
     * @param  mixed $days
     * @param  mixed $months
     * @return object
     */
    public function getAddDayAndMonth($days, $months = 0): object
    {
        $sendingNotiRejectionDate = new DateTime($this->sending_noti_rejection_date . '+' . $days . ' day');
        $newSendingNotiRejectionDate = $sendingNotiRejectionDate->modify('+' . $months . 'month');

        return $newSendingNotiRejectionDate;
    }

    /**
     * Show date format japanse
     *
     * @param string $timeString
     * @return string
     */
    public static function showDateFormatJapanese(string $timeString): String
    {
        return Carbon::parse($timeString)->locale('jp')->format('Y年m月d日');
    }

    /**
     * Check Response Deadline Payment CreditCard
     *
     * @param  string $paymentType
     * @return string
     */
    public function checkResponseDeadlinePaymentCreditCard($paymentType): string
    {
        $now = Carbon::now();
        $responseDeadline = new Carbon($this->response_deadline);
        $condition = ($responseDeadline->diff($now)->days);
        $result = '';
        switch ($paymentType) {
            case PAYMENT_TYPE_CREDIT:
                if ($condition <= 3) {
                    $result = ERROR_PAYMENT_TYPE_CREDIT;
                }
                break;
            case PAYMENT_TYPE_TRANSFER:
                if ($condition <= 5) {
                    $result = ERROR_PAYMENT_TYPE_TRANSFER;
                }
                break;
        }

        return $result;
    }
}
