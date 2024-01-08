<?php

namespace App\Repositories;

use App\Models\MTApplicantArticle;
use Illuminate\Database\Eloquent\Builder;

class MTApplicantArticleRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   MTApplicantArticle $MTApplicantArticle
     * @return  void
     */
    public function __construct(MTApplicantArticle $mtApplicantArticle)
    {
        $this->model = $mtApplicantArticle;
    }

    /**
     * @param   Builder $query
     * @param   string  $column
     * @param   mixed   $data
     *
     * @return  Builder
     */
    public function mergeQuery(Builder $query, string $column, $data): Builder
    {
        switch ($column) {
            case 'id':
                return $query->where($column, $data);
            default:
                return $query;
        }
    }
}
