<?php

namespace App\Repositories;

use App\Models\MProduct;
use App\Models\Precheck;
use App\Models\PrecheckProduct;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PrecheckProductRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param PrecheckProduct $precheckProduct
     * @return  void
     */
    public function __construct(PrecheckProduct $precheckProduct)
    {
        $this->model = $precheckProduct;
    }

    /**
     * @param Builder $query
     * @param string $column
     * @param mixed $data
     *
     * @return  Builder
     */
    public function mergeQuery(Builder $query, string $column, $data): Builder
    {
        switch ($column) {
            case 'id':
            case 'precheck_id':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }

    /**
     * Get data precheck products (precheck simple)
     *
     * @param array $id
     *
     * @return  array
     */
    public function getPrecheckProduct(array $id): array
    {
        $precheckProduct = $this->getData($id)->get()->groupBy('code_name');
        $data = [
            'precheckProduct' => $precheckProduct,
        ];

        return $data;
    }

    /**
     * Get data product register
     *
     * @param int $trademarkid
     * @return array
     */
    public function getPrecheckProductRegister(int $trademarkId)
    {
        $productNotRegister = $this->getDataRegister($trademarkId, PrecheckProduct::IS_NOT_PRECHECK_PRODUCT)
            ->groupBy(
                'precheck_products.m_product_id',
                'is_register_product',
                'code_name',
                'distinction_name',
                'product_name',
            )->orderBy('m_product_id')->get();
        $precheckProductRegister = $this->getDataRegister($trademarkId, PrecheckProduct::IS_PRECHECK_PRODUCT)
            ->groupBy(
                'precheck_products.m_product_id',
                'is_register_product',
                'code_name',
                'distinction_name',
                'product_name'
            )->orderBy('m_product_id')->get();

        $data = [
            'productNotRegister' => $productNotRegister,
            'precheckProductRegister' => $precheckProductRegister,
        ];

        return $data;
    }

    /**
     * Get data product register of trademark present
     *
     * @param int $id
     * @return array
     */
    public function getPrecheckProductIsRegister(int $id)
    {
        $productRegister = Precheck::rightJoin('precheck_products', 'precheck_products.precheck_id', 'prechecks.id')
            ->leftJoin('precheck_results', 'precheck_results.precheck_product_id', 'precheck_products.id')
            ->leftJoin('m_code', 'm_code.id', 'precheck_results.m_code_id')
            ->select('m_code.name as code_name', 'm_code.id')
            ->where('prechecks.trademark_id', $id)->where('precheck_products.is_register_product', 1)->groupBy('precheck_results.m_code_id', 'm_code.name')->get();

        return $productRegister;
    }

    /**
     * Get data precheck products (precheck unique)
     *
     * @param array $id
     *
     * @return  array
     */
    public function getPrecheckProduct2(array $id): array
    {
        $precheckProduct = $this->getDataUnique($id)->get()->groupBy('mDistinction.name');

        $data = [
            'precheckProduct' => $precheckProduct,
        ];

        return $data;
    }

    /**
     * Query get data (precheck simple and precheck similar)
     *
     * @param array $id
     *
     * @return  Builder
     */
    public function getData(array $id)
    {
        $data = MProduct::whereHas('prechecks', function ($q) use ($id) {
            $q->whereIn('precheck_id', $id);
        })
            ->leftJoin('m_product_codes', 'm_product_codes.m_product_id', 'm_products.id')
            ->leftJoin('m_code', 'm_code.id', 'm_product_codes.m_code_id')
            ->select('m_code.name as code_name', 'm_code.id as m_code_id', 'm_products.*')
            ->with([
                'precheckProduct' => function ($q) use ($id) {
                    $q->whereIn('precheck_id', $id);
                },
                'precheckProduct.precheckResult' => function ($q) use ($id) {
                    $q->whereIn('precheck_id', $id);
                },
                'precheckProduct.precheckResult.precheck',
                'precheckProduct.product.mDistinction',
                'mDistinction',
                'code',
            ])->orderBy('code_name');
        return $data;
    }



    /**
     * Get data precheck products (precheck similar)
     *
     * @param array $id
     *
     * @return  array
     */
    public function getPrecheckProductSimilar(array $id): array
    {
        $precheckProduct = $this->getData($id);
        $precheckProductIds = $this->model->whereIn('precheck_id', $id)->select('id')->get()->toArray();
        $precheckRegister = $this->getPrecheckProductIsRegister($id)->get()->groupBy('code_name');
        $dataIds = [];
        foreach ($precheckProductIds as $key => $value) {
            $dataIds[] = $value['id'];
        }
        $data = [
            'precheckProduct' => $precheckProduct,
            'ids' => $dataIds,
            'precheckRegister' => $precheckRegister,
        ];

        return $data;
    }

    /**
     * Query get data (precheck simple and precheck similar)
     *
     * @param int $id
     * @param int $type
     *
     * @return  Builder
     */
    public function getDataRegister(int $id, int $type)
    {
         $data = Precheck::rightJoin('precheck_products', 'precheck_products.precheck_id', 'prechecks.id')
             ->leftJoin('m_products', 'm_products.id', 'precheck_products.m_product_id')
             ->leftJoin('m_product_codes', 'm_product_codes.m_product_id', 'm_products.id')
             ->leftJoin('m_code', 'm_code.id', 'm_product_codes.m_code_id')
             ->leftJoin('m_distinctions', 'm_distinctions.id', 'm_products.m_distinction_id')
             ->select('m_code.name as code_name', 'm_code.id as m_code_id', 'm_products.*')
             ->select(
                 'precheck_products.m_product_id',
                 'precheck_products.is_register_product',
                 'm_products.name as product_name',
                 'm_code.name as code_name',
                 'm_distinctions.name as distinction_name'
             )->with(['products', 'precheckProducts'])
             ->where('prechecks.trademark_id', $id)->where('precheck_products.is_register_product', $type);

        return $data;
    }

    /**
     * Query get data (precheck select check unique)
     *
     * @param array $ids
     *
     * @return  array
     */
    public function getPrecheckProductUnique(array $ids): array
    {
        $precheckProduct = $this->getDataUnique($ids)->get()->groupBy('mDistinction.name');
        $precheckProductIds = $this->model->whereIn('precheck_id', $ids)->select('id')->get();
        $dataIds = [];
        foreach ($precheckProductIds as $key => $value) {
            $dataIds[] = $value['id'];
        }
        $data = [
            'precheckProduct' => $precheckProduct,
        ];

        return $data;
    }

    /**
     * Query get data (precheck select check unique)
     *
     * @param array $ids
     *
     * @return  mixed
     */
    public function test(array $ids)
    {
        $precheckProduct = $this->getDataUnique($ids)->get();
        return $precheckProduct;
    }

    /**
     * GetPrecheckProductConfirm
     *
     * @param array $ids
     * @return Collection
     */
    public function getPrecheckProductConfirm(array $ids): Collection
    {
        return $this->model->leftJoin('m_product_codes', 'm_product_codes.m_product_id', 'precheck_products.m_product_id')
            ->leftJoin('m_products', 'm_products.id', 'precheck_products.m_product_id')
            ->leftJoin('m_code', 'm_code.id', 'm_product_codes.m_code_id')
            ->select(
                'm_code.name as code_name',
                'm_code.id as code_id',
                'm_products.name as product_name',
                'precheck_products.*',
            )->whereIn('precheck_products.precheck_id', $ids)
            ->with('precheck', 'product.mDistinction', 'product.code', 'product.precheck', 'precheckResult.precheck', 'product.precheck.precheckResult')
            ->orderBy('code_name')->get();
    }


    /**
     * Query get data (precheck unique)
     *
     * @param array $id
     *
     * @return  Builder
     */
    public function getDataUnique(array $id): Builder
    {
        $data = MProduct::whereHas('prechecks', function ($q) use ($id) {
            $q->whereIn('precheck_id', $id);
        })
            ->with([
                'precheckProduct' => function ($q) use ($id) {
                    $q->whereIn('precheck_id', $id);
                },
                'precheckProduct.precheckResult' => function ($q) use ($id) {
                    $q->whereIn('precheck_id', $id);
                },
                'precheckProduct.product.mDistinction',
                'mDistinction',
                'code',
            ]);
        return $data;
    }

    /**
     * Get data precheck modal
     *
     * @param object $idPrecheckSimple
     * @param object $idPrecheckSelect
     *
     * @return  array
     */
    public function getPrecheckProductModal(object $idPrecheckSimple, object $idPrecheckSelect): array
    {
        $idPrecheck = array_merge($idPrecheckSimple->toArray(), $idPrecheckSelect->toArray());

        $dataPresent = $this->getDataUnique([$idPrecheckSelect[0]])->get()->groupBy('mDistinction.name');
        $dataPrevious = $this->getDataUnique($idPrecheck)->get()->groupBy('mDistinction.name');
        return [$dataPresent, $dataPrevious];
    }

    /**
     * Query get data precheck modal
     *
     * @param int $idPrecheckSimple
     * @param int $idPrecheckSelect
     *
     * @return  object
     */
    public function queryPrecheckProductModal(int $idPrecheckSimple, int $idPrecheckSelect): object
    {
        return $this->model
            ->leftJoin('m_products', 'm_products.id', 'precheck_products.m_product_id')
            ->leftJoin('m_distinctions', 'm_products.m_distinction_id', 'm_distinctions.id')
            ->leftJoin('precheck_results', 'precheck_products.id', 'precheck_results.precheck_product_id')
            ->select(
                'm_distinctions.name as distinction_name',
                'm_products.name as product_name',
                'precheck_results.result_similar_simple',
                'precheck_results.result_identification_detail',
                'precheck_results.result_similar_detail',
                'm_products.m_distinction_id',
                'precheck_products.is_register_product',
                'precheck_products.*'
            )
            ->whereIn('precheck_products.precheck_id', [$idPrecheckSimple, $idPrecheckSelect])
            ->orderBy('m_distinctions.id', 'ASC')
            ->with('precheck', 'product.mDistinction', 'product.code', 'product.precheck')
            ->get();
    }
}
