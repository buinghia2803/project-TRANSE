<?php

namespace App\Repositories;

use App\Models\RequiredDocumentMiss;
use Illuminate\Database\Eloquent\Builder;

class RequiredDocumentMissRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   RequiredDocumentMiss $requiredDocumentMiss
     * @return  void
     */
    public function __construct(RequiredDocumentMiss $requiredDocumentMiss)
    {
        $this->model = $requiredDocumentMiss;
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
