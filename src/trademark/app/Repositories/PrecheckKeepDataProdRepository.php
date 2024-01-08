<?php

namespace App\Repositories;

use App\Models\MProduct;
use App\Models\PrecheckKeepDataProd;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PrecheckKeepDataProdResult;

class PrecheckKeepDataProdRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   PrecheckKeepDataProd $precheckKeepDataProd
     * @return  void
     */
    public function __construct(PrecheckKeepDataProd $precheckKeepDataProd)
    {
        $this->model = $precheckKeepDataProd;
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
            case 'type_precheck':
            case 'precheck_keep_data_id':
                return $query->where($column, $data);
            case 'ids':
                return $query->whereIn('id', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }


    /**
     * Query get data precheck keep data(groupBy distinction name)
     *
     * @param array $id
     *
     * @return  Builder
     */
    public function getPrecheckKeepDataProduct($id)
    {
        return MProduct::whereHas('precheckKeepDatas', function ($q) use ($id) {
            $q->where('precheck_keep_data_id', $id);
        })->with([
            'precheckKeepDataProd' => function ($q) use ($id) {
                $q->where('precheck_keep_data_id', $id);
            },
            'precheckKeepDataProd.precheckKeepDataProdResult' => function ($q) use ($id) {
                $q->where('precheck_keep_data_id', $id);
            },
            'mDistinction',
        ]);
    }

    /**
     * Query get data precheck keep data(groupBy code name)
     *
     * @param array $id
     *
     * @return  Builder
     */
    public function getPrecheckKeepDataProduct2($id)
    {
        return MProduct::whereHas('precheckKeepDatas', function ($q) use ($id) {
            $q->where('precheck_keep_data_id', $id);
        })
            ->leftJoin('m_product_codes', 'm_product_codes.m_product_id', 'm_products.id')
            ->leftJoin('m_code', 'm_code.id', 'm_product_codes.m_code_id')
            ->select('m_code.name as code_name', 'm_code.id as m_code_id', 'm_products.*')
            ->with([
                'precheckKeepDataProd' => function ($q) use ($id) {
                    $q->where('precheck_keep_data_id', $id);
                },
                'precheckKeepDataProd.precheckKeepDataProdResult' => function ($q) use ($id) {
                    $q->where('precheck_keep_data_id', $id);
                },
                'mDistinction',
                'code',
            ])->orderBy('code_name', 'ASC');
    }
}
