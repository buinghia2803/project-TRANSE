<?php

namespace App\Repositories;

use App\Models\SFTKeepData;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;

class SFTKeepDataRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   SFTKeepData $sftKeepData
     * @return  void
     */
    public function __construct(SFTKeepData $sftKeepData)
    {
        $this->model = $sftKeepData;
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
            case 'support_first_time_id':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }
}
