<?php

namespace App\Repositories;

use App\Models\MCode;
use App\Models\MDistinction;
use App\Models\MProduct;
use App\Models\PlanDetail;
use App\Models\PlanDetailProduct;
use App\Models\RegisterTrademark;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class MProductRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param MProduct $MProduct
     * @return  void
     */
    public function __construct(MProduct $MProduct)
    {
        $this->model = $MProduct;
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
            case 'm_distinction_id':
            case 'admin_id':
            case 'products_number':
            case 'name':
            case 'type':
                return $query->where($column, $data);
            case 'types':
                return $query->whereIn('type', $data);
            case 'ids':
                return $query->whereIn('id', $data);
            case 'search_name':
                return $query->where('name', 'REGEXP', $data);
            case 'keyword':
                return $query->where('name', 'REGEXP', $data)
                    ->whereIn('type', [ORIGINAL_CLEAN, REGISTER_CLEAN]);
            default:
                return $query;
        }
    }

    /**
     * Get data m_product
     *
     * @param array $idsProduct
     * @return Collection
     */
    public function getDataMproduct(array $idsProduct)
    {
        return $this->model->whereIn('id', $idsProduct)
            ->with([
                'mDistinction:id,name', 'appTrademarkProd' => function ($query) {
                    return $query->orderBy('id', 'desc');
                }, 'appTrademarkProd.planCorrespondenceProd'
            ])
            ->select('id', 'm_distinction_id', 'name')
            ->get()
            ->groupBy('mDistinction.name')
            ->sortKeys();
    }

    /**
     * Get data m_product
     *
     * @param array $idsProduct
     * @return Collection
     */
    public function getDataMProductChoosePlan(array $idsProduct)
    {
        return $this->model->whereIn('id', $idsProduct)
            ->with(['mDistinction:id,name', 'planDetailProduct.planDetail', 'planDetailProducts.planDetail'])
            ->select('id', 'm_distinction_id', 'name')
            ->get()
            ->groupBy('mDistinction.name')
            ->sortKeys();
    }

    /**
     * Get data m_product
     *
     * @param array $idsProduct
     * @return Collection
     */
    public function getDataMProductu203c(array $idsProduct, $planDetailIds)
    {
        return $this->model->whereIn('id', $idsProduct)
            ->with([
                'mDistinction:id,name', 'planDetailProducts' => function ($query) use ($planDetailIds) {
                    return $query->whereIn('plan_detail_id', $planDetailIds)->with('planDetail');
                }
            ])
            ->select('id', 'm_distinction_id', 'name')
            ->get()
            ->groupBy('mDistinction.name')
            ->sortKeys();
    }

    /**
     * Get Product common options
     *
     * @return mixed
     */
    public function getMProductCommonOptions()
    {
        return $this->model->whereIn('type', [MProduct::TYPE_ORIGINAL_CLEAN, MProduct::TYPE_REGISTERED_CLEAN])->pluck('name', 'id');
    }

    /**
     * Get product by support_first_time_id
     *
     * @param int $sftId
     * @return void
     */
    public function getSftSuitableProducts(int $sftId)
    {
        return $this->model->whereHas('SftSuitableProduct', function ($q) use ($sftId) {
            $q->where('support_first_time_id', $sftId);
        })->select('id', 'name')->get();
    }


    /**
     * Generate product number.
     *
     * @param int $type .
     * @param int $distinctionID
     * @return string
     */
    public function generateProductCode(int $type, int $distinctionID): string
    {
        $productNumber = '';

        $product = $this->findByCondition(['m_distinction_id' => $distinctionID])->orderBy('id', 'desc')->first();

        $number = $distinctionID . str_pad((int) substr($product->products_number, -4) + 1, 4, 0, STR_PAD_LEFT);

        switch ($type) {
            case ORIGINAL_CLEAN:
                return $productNumber = '0' . $number;
            case REGISTER_CLEAN:
                return $productNumber = '1' . $number;
            case CREATIVE_CLEAN:
                return $productNumber = '2' . $number;
            case SEMI_CLEAN:
                return $productNumber = '3' . $number;
        }

        return $productNumber;
    }

    /**
     * Get data product common a205 shu02
     *
     * @param int $trademarkPlanId
     * @return Collection
     */
    public function getDataProductCommonA205Shu02($trademarkPlanId)
    {
        return DB::table('m_products')
            ->join('plan_detail_products', 'm_products.id', 'plan_detail_products.m_product_id')
            ->join('plan_details', 'plan_detail_products.plan_detail_id', 'plan_details.id')
            ->join('plans', 'plan_details.plan_id', 'plans.id')
            ->join('m_distinctions', 'm_products.m_distinction_id', 'm_distinctions.id')
            ->where('plans.trademark_plan_id', $trademarkPlanId)
            ->where('plan_details.is_choice', PlanDetail::IS_CHOICE)
            ->where('plan_detail_products.is_choice', PlanDetailProduct::IS_CHOICE)
            ->where('plan_detail_products.is_deleted', false)
            ->select(
                'm_distinctions.name as m_distinction_name',
                'm_products.id',
                'm_products.m_distinction_id',
                'm_products.name',
                'plan_detail_products.id as plan_detail_product_id',
                'plan_detail_products.role_add',
                'plan_detail_products.deleted_at as plan_detail_product_deleted_at'
            )
            ->get()
            ->groupBy('m_distinction_name');
    }

    /**
     * Get Product App Trademark
     *
     * @param  array $mProductIds
     * @return collection|null
     */
    public function getProductAppTrademark($mProductIds, $registerTrademarkProdIds): ?Collection
    {
        return $this->model->whereIn('id', $mProductIds)
            ->with(['mDistinction:id,name', 'registerTrademarkProd' => function ($query) use ($registerTrademarkProdIds) {
                return $query->whereIn('id', $registerTrademarkProdIds);
            }
        ])
            ->select('id', 'm_distinction_id', 'name')
            ->get()
            ->groupBy('mDistinction.name')
            ->sortKeys();
    }

    /**
     * Get Product App Trademark
     *
     * @param  array $mProductIds
     * @param  array $appTrademarkProdIds
     * @return collection
     */
    public function getProductAppTrademarkByAppTrademarkProd($mProductIds, $appTrademarkProdIds): ?Collection
    {
        return $this->model->whereIn('id', $mProductIds)
            ->with(['mDistinction:id,name', 'appTrademarkProd' => function ($query) use ($appTrademarkProdIds) {
                return $query->whereIn('id', $appTrademarkProdIds);
            }
            ])
            ->select('id', 'm_distinction_id', 'name')
            ->get()
            ->groupBy('mDistinction.name')
            ->sortKeys();
    }

    /**
     * Get Product App Trademark is apply true
     *
     * @param  array $mProductIds
     * @return collection|null
     */
    public function getProductAppTrademarkIsApplyTrue($mProductIds, $registerTrademarkProdIds): ?Collection
    {
        return $this->model->whereIn('id', $mProductIds)
            ->with([
                'mDistinction:id,name',
                'registerTrademarkProd' => function ($query) use ($registerTrademarkProdIds) {
                    return $query->whereIn('id', $registerTrademarkProdIds)
                                 ->where('is_apply', RegisterTrademark::IS_APPLY_TRUE);
                }
            ])
            ->whereHas('registerTrademarkProd', function ($query) use ($registerTrademarkProdIds) {
                return $query->whereIn('id', $registerTrademarkProdIds)
                    ->where('is_apply', RegisterTrademark::IS_APPLY_TRUE);
            })
            ->select('id', 'm_distinction_id', 'name')
            ->get()
            ->groupBy('mDistinction.name')
            ->sortKeys();
    }

    /**
     * GetInfoProdByRegisterTrademark Query
     *
     * @param int $registerTrademarkId
     * @param array $params
     * @return collection|null
     */
    public function getInfoProdByRegisterTrademarkQuery(int $registerTrademarkId, array $params = [])
    {
        $query = $this->model->join('app_trademark_prods', 'm_products.id', 'app_trademark_prods.m_product_id')
                ->join('register_trademark_prods', 'app_trademark_prods.id', 'register_trademark_prods.app_trademark_prod_id')
                ->join('register_trademarks', 'register_trademark_prods.register_trademark_id', 'register_trademarks.id')
                ->leftJoin('m_distinctions', 'm_products.m_distinction_id', 'm_distinctions.id')
                ->where('register_trademarks.id', $registerTrademarkId);
        if (isset($params['is_apply']) && !empty($params['is_apply'])) {
            $query = $query->where('register_trademark_prods.is_apply', $params['is_apply']);
        }

        return $query->select(
            'm_products.*',
            'register_trademark_prods.id as register_trademark_prod_id',
            'register_trademark_prods.is_apply',
            'app_trademark_prods.id as app_trademark_prods_id',
            'm_distinctions.name as m_distinction_name'
        );
    }

    /**
     * GetInfoProdByRegisterTrademark
     *
     * @param int $registerTrademarkId
     * @param array $params
     * @return collection|null
     */
    public function getInfoProdByRegisterTrademark(int $registerTrademarkId, array $params = [])
    {
        return $this->getInfoProdByRegisterTrademarkQuery($registerTrademarkId, $params)
                    ->get()
                    ->groupBy('m_distinction_name');
    }

    /**
     * Get Data Search Product
     *
     * @param array $inputs
     * @param $dataSession
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getDataSearchProduct(array $inputs, $dataSession)
    {
        $query = $this->model;
        if (!empty($inputs['no_code'])) {
            $query = $query->doesntHave('mCode');
        }
        if (!empty($dataSession['dataSearch'])) {
            if (!empty($dataSession['type'])) {
                $query = $query->where('type', $dataSession['type']);
            }
            foreach ($dataSession['dataSearch'] as $item) {
                if (!empty($item['value_search'])) {
                    $query = $this->queryGetDataSearchProduct($query, $item);
                }
            }
        }
        $query = $this->getQuerySortSearchDataProduct($query, $inputs);

        return $query->with([
            'mDistinction',
            'parent',
            'mCode' => function ($q) use ($dataSession) {
                if ($dataSession) {
                    foreach ($dataSession['dataSearch'] as $item) {
                        if (!empty($item['value_search'])) {
                            if ($item['field_search'] == SEARCH_CONCEPT) {
                                $conditionSearch = $item['condition_search'];
                                switch ($conditionSearch) {
                                    case IS_GREATER_THAN:
                                        $typeSearch = '>=';
                                        break;
                                    case IS_LESS_THAN:
                                        $typeSearch = '<=';
                                        break;
                                    default:
                                        $typeSearch = '=';
                                }
                                $q->where(DB::raw('CAST(branch_number AS UNSIGNED)'), '>', 1)
                                    ->where(DB::raw('CAST(branch_number AS UNSIGNED)'), $typeSearch, $item['value_search']);
                            }
                        }
                    }
                }
            }
        ])->get();
    }

    /**
     * QueryGetDataSearchProduct
     *
     * @param $query
     * @param $item
     * @return mixed
     */
    public function queryGetDataSearchProduct($query, $item)
    {
        $conditionSearch = $item['condition_search'];
        switch ($item['field_search']) {
            case SEARCH_DISTINCTION_NAME:
                $query = $query->whereHas('mDistinction', function ($q) use ($item, $conditionSearch) {
                    switch ($conditionSearch) {
                        case IS_GREATER_THAN:
                            $fieldData = DB::raw('CAST(name AS UNSIGNED)');
                            $typeSearch = '>=';
                            break;
                        case IS_LESS_THAN:
                            $fieldData = DB::raw('CAST(name AS UNSIGNED)');
                            $typeSearch = '<=';
                            break;
                        default:
                            $fieldData = 'name';
                            $typeSearch = '=';
                    }
                    $q->where($fieldData, $typeSearch, $item['value_search']);
                });
                break;
            case SEARCH_PRODUCT_NAME:
                $query = $this->getQueryBuilderConditionSearch($query, $item, 'name');
                break;
            case SEARCH_CODE_NAME:
                $query = $this->getQueryBuilderConditionSearch($query, $item, 'name', 'mCode');
                break;
            case SEARCH_CONCEPT:
                $query = $query->where('is_parent', MProduct::IS_PARENT)->has('mCode')->whereHas('mCode', function ($j) {
                    $j->where(DB::raw('CAST(branch_number AS UNSIGNED)'), '>', 1);
                });

                $query = $this->getQueryBuilderConditionSearch($query, $item, DB::raw('CAST(branch_number AS UNSIGNED)'), 'mCode');
                break;
        }

        return $query;
    }


    /**
     * GetQueryBuilderConditionSearch
     *
     * @param $query
     * @param $item
     * @param $column
     * @param $relationShip
     * @return mixed
     */
    public function getQueryBuilderConditionSearch($query, $item, $column, $relationShip = null)
    {
        $valueSearch = $item['value_search'];
        switch ($item['condition_search']) {
            case EQUAL:
                $typeSearch = '=';
                $clauseSearch = $valueSearch;
                break;
            case START_FROM:
                $typeSearch = LIKE;
                $clauseSearch = $valueSearch . '%';
                break;
            case CONSISTS_OF:
                $typeSearch = LIKE;
                $clauseSearch = '%' . $valueSearch . '%';
                break;
            case IS_GREATER_THAN:
                $typeSearch = '>=';
                $clauseSearch = $valueSearch;
                break;
            case IS_LESS_THAN:
                $typeSearch = '<=';
                $clauseSearch = $valueSearch;
                break;
        }

        if ($relationShip) {
            $query = $query->whereHas($relationShip, function ($q) use ($column, $typeSearch, $clauseSearch) {
                return $q->where($column, $typeSearch, $clauseSearch);
            });
        } else {
            $query = $query->where($column, $typeSearch, $clauseSearch);
        }

        return $query;
    }

    /**
     * Get Query Sort Search Data Product
     *
     * @param $query
     * @param $inputs
     * @return mixed
     */
    public function getQuerySortSearchDataProduct($query, $inputs)
    {
        if (!empty($inputs['m_product_id']) && $this->checkTypeSort($inputs['m_product_id'])) {
            $query = $query->orderBy('id', $inputs['m_product_id']);
        }
        if (!empty($inputs['products_number']) && $this->checkTypeSort($inputs['products_number'])) {
            $query = $query->orderBy('products_number', $inputs['products_number']);
        }

        if (!empty($inputs['m_distinction_name']) && $this->checkTypeSort($inputs['m_distinction_name'])) {
            $query = $query->orderBy('m_distinction_id', $inputs['m_distinction_name']);
        }

        return $query;
    }

    /**
     * CheckTypeSort
     *
     * @param $typeSort
     * @return bool
     */
    public function checkTypeSort($typeSort)
    {
        return in_array($typeSort, [SORT_BY_ASC, SORT_BY_DESC]);
    }
}
