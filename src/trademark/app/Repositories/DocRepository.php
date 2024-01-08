<?php

namespace App\Repositories;

use App\Models\Doc;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class DocRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   Doc $doc
     * @return  void
     */
    public function __construct(Doc $doc)
    {
        $this->model = $doc;
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
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
