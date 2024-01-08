<?php

namespace App\Repositories;

use App\Models\History;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class HistoryRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   History $history
     * @return  void
     */
    public function __construct(History $history)
    {
        $this->model = $history;
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
            case 'ids':
                return $query->whereIn('id', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
