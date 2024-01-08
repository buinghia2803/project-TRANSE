<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MTApplicantArticle extends BaseModel
{
    protected $table = 'maching_result_applicant_articles';


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'maching_result_id',
        'applicant_division',
        'applicant_identification_number',
        'applicant_name',
    ];

    /**
     * Trademark
     *
     * @return BelongsTo
     */
    public function matchingResult(): BelongsTo
    {
        return $this->belongsTo(MatchingResult::class, 'maching_result_id', 'id');
    }
}
