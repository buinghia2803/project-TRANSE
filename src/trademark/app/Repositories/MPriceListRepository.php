<?php

namespace App\Repositories;

use App\Models\MPriceList;
use Illuminate\Database\Eloquent\Builder;

use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class MPriceListRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   MPriceList $mPriceList
     * @return  void
     */
    public function __construct(MPriceList $mPriceList)
    {
        $this->model = $mPriceList;
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
            case 'name':
                return $query->search($column, $data);
            case 'service_type':
            case 'package_type':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }

    /**
     * Query get price common of precheck
     *
     * @param integer $service_type
     * @param string $package_type
     *
     * @return Collection
     */
    public function getPriceCommonOfPrecheck(int $service_type, string $package_type)
    {
        return $this->model->where([
            ['service_type', $service_type],
            ['package_type', $package_type],
        ])->first();
    }
}
