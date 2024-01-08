<?php

namespace App\Services;

use App\Repositories\SFTSuitableProductRepository;
use Illuminate\Database\Eloquent\Collection;

class SFTSuitableProductService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param SFTSuitableProductRepository $SFTSuitableProductRepository
     */
    public function __construct(SFTSuitableProductRepository $SFTSuitableProductRepository)
    {
        $this->repository = $SFTSuitableProductRepository;
    }

    /**
     * Query get List.
     *
     * @param  array $input.
     * @return mixed
     */
    public function queryGetList(array $input)
    {
        $sftSuitableProduct = $this->findByCondition($input, [
            'mProduct',
            'mProduct.productCode' => function ($query) {
                return $query->join('m_code', 'm_product_codes.m_code_id', '=', 'm_code.id')
                            ->select('m_code.name as code_name', 'm_product_codes.*');
            }
        ])
            ->join('m_products', 'sft_suitable_products.m_product_id', '=', 'm_products.id')
            ->join('m_distinctions', 'm_products.m_distinction_id', '=', 'm_distinctions.id')
            ->select('m_products.*', 'sft_suitable_products.*', 'm_distinctions.name as distinction_name', 'm_distinctions.id as m_distinctions_id');

        return $sftSuitableProduct;
    }

    /**
     * Get List.
     *
     * @param  array $input.
     * @return Collection
     */
    public function getList(array $input): Collection
    {
        return $this->queryGetList($input)->orderBy('m_distinctions_id', 'asc')->get()->groupBy('distinction_name');
    }

    /**
     * Update is block column ajax
     *
     * @param array $inputs
     * @return boolean
     */
    public function updateIsBlockAjax(array $inputs): bool
    {
        return $this->repository->updateIsBlockAjax($inputs);
    }

    /**
     * Get List Data Suitable ProductOld
     *
     * @param  array $input.
     * @return Collection
     */
    public function getListDataSuitableProductOld(array $input): Collection
    {
        return $this->queryGetList($input)->get();
    }
}
