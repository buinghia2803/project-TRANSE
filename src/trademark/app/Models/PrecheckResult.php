<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrecheckResult extends BaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'admin_id',
        'precheck_product_id',
        'precheck_id',
        'm_code_id',
        'result_similar_simple',
        'result_identification_detail',
        'result_similar_detail',
        'is_block_identification',
        'is_block_similar',
    ];


    /**
     * Const list
     */
    const TYPE_SUBMIT_CREATE = 1;
    const TYPE_SUBMIT_CREATE_NOTIF = 2;
    const LIKELY_TO_BE_REGISTERED = 1;
    const LOOK_FORWARD_TO_REGISTERING = 2;
    const LESS_LIKELY_TO_BE_REGISTERED = 3;
    const DIFFICULT_TO_REGISTER = 4;
    const PRECHECK_RESULT_SIMPLE_NO_IDENTICAL = 1;
    const PRECHECK_RESULT_SIMPLE_SAME = 2;

    const RESULT_SIMILAR_SIMPLE_YES = 1;
    const RESULT_SIMILAR_SIMPLE_NO = 2;

    const RESULT_IDENTIFICATION_DETAIL_LIKELY = 1;
    const RESULT_IDENTIFICATION_DETAIL_HOPEFUL = 2;
    const RESULT_IDENTIFICATION_DETAIL_LOW = 3;
    const RESULT_IDENTIFICATION_DETAIL_DIFFICULT = 4;

    const RESULT_SIMILAR_DETAIL_LIKELY = 1;
    const RESULT_SIMILAR_DETAIL_HOPEFUL = 2;
    const RESULT_SIMILAR_DETAIL_LOW = 3;
    const RESULT_SIMILAR_DETAIL_DIFFICULT = 4;

    /**
     * The attributes that can be order by.
     *
     * @var array
     */
    public $sortable = [
        'id',
        'admin_id',
    ];

    public $selectable = [
        '*',
    ];

    /**
     * PrecheckProduct
     *
     * @return BelongsTo
     */
    public function precheckProduct(): BelongsTo
    {
        return $this->belongsTo(PrecheckProduct::class, 'precheck_product_id', 'id');
    }

    /**
     * List result similar simple options
     *
     * @return array
     */
    public static function listResultSmilarSimpleOptions(): array
    {
        return [
            self::RESULT_SIMILAR_SIMPLE_YES => '有',
            self::RESULT_SIMILAR_SIMPLE_NO => 'なし'
        ];
    }

    /**
     * List result identification detail options
     *
     * @return array
     */
    public static function listResultIdentificationDetailOptions(): array
    {
        return [
            self::RESULT_IDENTIFICATION_DETAIL_LIKELY => '○',
            self::RESULT_IDENTIFICATION_DETAIL_HOPEFUL => '△',
            self::RESULT_IDENTIFICATION_DETAIL_LOW => '▲',
            self::RESULT_IDENTIFICATION_DETAIL_DIFFICULT => '×',
        ];
    }

    /**
     * List result identification detail options
     *
     * @return array
     */
    public static function listResultSimilarDetailOptions(): array
    {
        return [
            self::RESULT_SIMILAR_DETAIL_LIKELY => '○',
            self::RESULT_SIMILAR_DETAIL_HOPEFUL => '△',
            self::RESULT_SIMILAR_DETAIL_LOW => '▲',
            self::RESULT_SIMILAR_DETAIL_DIFFICULT => '×',
        ];
    }

    /**
     * List ranking anphabet
     *
     * @return array
     */
    public static function listRanking(): array
    {
        return [
            self::RESULT_SIMILAR_DETAIL_LIKELY => 'A',
            self::RESULT_SIMILAR_DETAIL_HOPEFUL => 'B',
            self::RESULT_SIMILAR_DETAIL_LOW => 'C',
            self::RESULT_SIMILAR_DETAIL_DIFFICULT => 'D',
        ];
    }

    /**
     * Get result detail precheck
     *
     * @param mixed $identification
     * @param mixed $similar
     * @return string
     */
    public static function getResultDetailPrecheck($identification, $similar): string
    {
        $key = $identification;
        if ($identification < $similar) {
            $key = $similar;
        }
        return self::listRanking()[$key];
    }

    /**
     * Precheck
     *
     * @return BelongsTo
     */
    public function precheck(): BelongsTo
    {
        return $this->belongsTo(Precheck::class, 'precheck_id');
    }



    /**
     * Get result idẹtification detail
     *
     * @return string
     */
    public function getResultIdentificationDetail()
    {
        $result = '';
        switch ($this->result_identification_detail) {
            case 1:
                $result = '○';
                break;
            case 2:
                $result = '△';
                break;
            case 3:
                $result = '▲';
                break;
            case 4:
                $result = '×';
                break;
            default:
                $result = '－';
        }

        return $result;
    }

    /**
     * Get result similar simple
     *
     * @return string
     */
    public function getResultSimilarSimple()
    {
        $result = '';
        switch ($this->result_similar_simple) {
            case 1:
                $result = '同一あり';
                break;
            case 2:
                $result = '同一なし';
                break;
            default:
                $result = '';
        }

        return $result;
    }

    /**
     * Get result similar detail
     *
     * @return string
     */
    public function getResultSimilarDetail()
    {
        $result = '';
        switch ($this->result_similar_detail) {
            case 1:
                $result = '○';
                break;
            case 2:
                $result = '△';
                break;
            case 3:
                $result = '▲';
                break;
            case 4:
                $result = '×';
                break;
            default:
                $result = '';
        }

        return $result;
    }
}
