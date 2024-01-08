<?php

namespace App\Repositories;

use App\Models\RequiredDocumentDetail;
use Illuminate\Database\Eloquent\Builder;

class RequiredDocumentDetailRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   RequiredDocumentDetail $requiredDocumentDetail
     * @return  void
     */
    public function __construct(RequiredDocumentDetail $requiredDocumentDetail)
    {
        $this->model = $requiredDocumentDetail;
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
                return $query->where('id', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
