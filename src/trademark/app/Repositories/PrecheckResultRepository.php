<?php

namespace App\Repositories;

use App\Models\PrecheckResult;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PrecheckResultRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   PrecheckResult $precheckResult
     * @return  void
     */
    public function __construct(PrecheckResult $precheckResult)
    {
        $this->model = $precheckResult;
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

    /**
     * Query get data precheck results
     *
     * @param   array  $ids
     * @return  array
     */
    public function getPrecheckResult(array $ids): array
    {
        $data = $this->model->whereIn('precheck_product_id', $ids)
            ->get()
            ->toArray();

        return $data;
    }

    /**
     * GetPrecheckResultByPrecheckId
     *
     * @param  array $precheckId
     * @return Collection
     */
    public function getPrecheckResultByPrecheckId(array $precheckId): Collection
    {
        return $this->model->whereIn('precheck_id', $precheckId)
            ->orderBy('precheck_id', 'DESC')
            ->get();
    }
}
