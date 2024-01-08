<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MatchingResult extends BaseModel
{
    protected $table = 'maching_results';

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'trademark_id',
        'admin_id',

        'document_type',
        'unconfirmed_state',
        'computer_name',
        'user_name',
        'distinction_number',

        // relation-file
        'rf_input_check_result',
        'rf_application_receipt_list',

        // result
        'pi_result_software_message',
        'pi_result_level',
        'pi_result_communication_result',
        'pi_result_fd_and_cdr',

        'pi_law',
        'pi_document_name',
        'pi_document_code',
        'pi_file_reference_id',
        'pi_invention_title',

        // application-reference
        'pi_ar_registration_number',
        'pi_ar_application_number',
        'pi_ar_application_date',
        'pi_ar_international_application_number',
        'pi_ar_international_application_date',
        'pi_ar_reference_id',
        'pi_ar_appeal_reference_number',
        'pi_ar_appeal_reference_date',
        'pi_ar_number_of_annexation',

        // submission-date
        'pi_sd_date',
        'pi_sd_time',

        'pi_page',
        'pi_image_total',
        'pi_size',
        'pi_receipt_number',
        'pi_wad_message_digest_compare',

        // input-date
        'pi_ip_date',
        'pi_ip_time',

        'pi_html_file_name',

        // applicant-article
        'pi_aa_total',

        'pi_claims_total',
        'pi_abstract',

        // payment
        'pi_payment_account_number',
        'pi_payment_fee_code',
        'pi_payment_amount',

        // representation_image
        'pi_ri_tile',
        'pi_ri_file_name',

        // time-for-response
        'pi_tfr_division',
        'pi_tfr_period',

        'pi_dispatch_number',

        // dispatch-date
        'pi_dd_date',
        'pi_dd_time',
        'import_type',
    ];

    // const
    const ADD_4_DAY_WITH_PACK_A_OR_B = 4;
    const ADD_3_DAY_WITH_PACK_A_OR_B = 3;
    const MINUS_3_DAY = 3;

    // Import type
    const IMPORT_DEFAULT = 1;
    const IMPORT_ANKEN_TOP = 2;

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
     * Calculate Response Deadline
     *
     * @param int $day
     * @return Carbon
     */
    public function calculateResponseDeadline(int $day = 0): Carbon
    {
        return Carbon::parse($this->pi_dd_date)->addDay($this->pi_tfr_period)->addDay($day);
    }

    /**
     * Add Day pi_dd_date.
     *
     * @param int $day
     * @return Carbon
     */
    public function addDayPiDdDate(int $day = 0): Carbon
    {
        return Carbon::parse($this->pi_dd_date)->addDay($day);
    }

    /**
     * Comparison Trademark Result
     *
     * @return HasOne
     */
    public function comparisonTrademarkResult(): HasOne
    {
        return $this->hasOne(ComparisonTrademarkResult::class, 'maching_result_id', 'id');
    }
}
