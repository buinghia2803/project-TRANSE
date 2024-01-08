<?php

namespace App\Repositories;

use App\Models\RequiredDocument;
use Illuminate\Database\Eloquent\Builder;

class RequiredDocumentRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   RequiredDocument $requiredDocument
     * @return  void
     */
    public function __construct(RequiredDocument $requiredDocument)
    {
        $this->model = $requiredDocument;
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
