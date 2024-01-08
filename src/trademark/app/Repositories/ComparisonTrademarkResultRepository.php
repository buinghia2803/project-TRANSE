<?php

namespace App\Repositories;

use App\Models\ComparisonTrademarkResult;
use App\Models\MPriceList;
use App\Models\Setting;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ComparisonTrademarkResultRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   ComparisonTrademarkResult $comparisonTrademarkResult
     * @return  void
     */
    public function __construct(ComparisonTrademarkResult $comparisonTrademarkResult)
    {
        $this->model = $comparisonTrademarkResult;
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
            case 'trademark_id':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }

    /**
     * Get select plan price with service type.
     *
     * @param int $serviceType
     * @param string $packageType
     * @return MPriceList
     */
    public function getSelectPlanPrice(int $serviceType, string $packageType): ?MPriceList
    {
        $periodRegistration = MPriceList::where('service_type', $serviceType)
            ->where('package_type', $packageType)
            ->first();

        return $periodRegistration;
    }

    /**
     * Get comparsion tradeMark result authenticate
     *
     * @param $comparisonTrademarkResultId
     * @return Collection
     */
    public function getComparisonTradeMarkResultAuthenticate($comparisonTrademarkResultId)
    {
        return $this->model->where('id', $comparisonTrademarkResultId)->whereHas('trademark', function ($q) {
            $q->where('user_id', auth()->user()->id);
        })->first();
    }
}
