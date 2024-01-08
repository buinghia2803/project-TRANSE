<?php

namespace App\Services;

use App\Models\MProduct;
use App\Models\MProductCode;
use App\Models\User;
use App\Repositories\MCodeRepository;
use App\Repositories\MPrefectureRepository;
use App\Repositories\MProductCodeRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Repositories\MProductRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MProductService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param MProductRepository $MProductRepository
     * @param MCodeRepository $mCodeRepository
     * @param MProductCodeRepository $mProductCodeRepository
     */
    public function __construct(
        MProductRepository $MProductRepository,
        MCodeRepository $mCodeRepository,
        MProductCodeRepository $mProductCodeRepository
    )
    {
        $this->repository = $MProductRepository;
        $this->mCodeRepository = $mCodeRepository;
        $this->mProductCodeRepository = $mProductCodeRepository;
    }

    /**
     * Get Related Name
     *
     * @param   array $keywords
     * @return  array
     */
    public function getRelatedName(array $keywords): array
    {
        $keywordDataMatch = [];
        $keywordDataNotMatch = [];

        foreach ($keywords as $key => $keyword) {
            if (!empty($keyword)) {
                $mProduct = $this->findByCondition([
                    'search_name' => $keyword,
                ])->whereIn('type', [MProduct::TYPE_ORIGINAL_CLEAN, MProduct::TYPE_REGISTERED_CLEAN])
                    ->with([
                        'mDistinction',
                        'mCode',
                        'parent.mDistinction',
                        'parent.mCode',
                    ])->get();

                $mProduct = $mProduct->map(function ($item) {
                    if (!empty($item->parent)) {
                        $item = $item->parent;
                    }

                    return $item;
                });

                $mProduct = $mProduct->unique();

                if ($mProduct->count() > 0) {
                    $keywordDataMatch[$key] = [
                        'keyword' => $keyword,
                        'products' => $mProduct,
                    ];
                } else {
                    $keywordDataNotMatch[$key] = [
                        'keyword' => $keyword,
                    ];
                }
            }
        }

        return compact(
            'keywordDataMatch',
            'keywordDataNotMatch'
        );
    }

    /**
     * Get Related Name
     *
     * @param   array $dataMatch
     * @return  array
     */
    public function filterDataSearchAI(array $dataMatch): array
    {
        $dataMatch = collect($dataMatch)->map(function ($item) {
            $blockCode = 3;
            $totalResult = 6;

            $productData = $item['products'] ?? [];
            $productResult = collect();
            $productCode = [];

            if (count($productData) > 0) {
                $productData = $productData->sortByDesc('total_order');
                foreach ($productData as $product) {
                    $codes = $product->mCode;
                    foreach ($codes as $code) {
                        if (count($productCode[$code->id] ?? []) < $blockCode && count($productResult) < $totalResult) {
                            $productCode[$code->id][] = $product;
                            $productResult[$product->id] = $product;
                        }
                    }
                }
            }

            $productArray = [];
            foreach ($productResult as $prod) {
                $productArray[] = $prod;
            }

            $item['products'] = collect($productArray);
            return $item;
        });

        return $dataMatch->toArray();
    }

    /**
     * Get data m_product
     *
     * @param array $idsProduct
     * @return mixed
     */
    public function getDataMproduct(array $idsProduct)
    {
        return $this->repository->getDataMproduct($idsProduct);
    }

    /**
     * Get data m_product
     *
     * @param array $idsProduct
     * @return mixed
     */
    public function getDataMProductChoosePlan(array $idsProduct)
    {
        return $this->repository->getDataMProductChoosePlan($idsProduct);
    }

    /**
     * Get data m_product
     *
     * @param array $idsProduct
     * @return mixed
     */
    public function getDataMProductu203c(array $idsProduct, $planDetailIds)
    {
        return $this->repository->getDataMProductu203c($idsProduct, $planDetailIds);
    }

    /**
     * Get Product common options
     *
     * @return mixed
     */
    public function getMProductCommonOptions()
    {
        return $this->repository->getMProductCommonOptions();
    }

    /**
     * Get product by support_first_time_id
     *
     * @param int $sftId
     * @return void
     */
    public function getSftSuitableProducts(int $sftId)
    {
        return $this->repository->getSftSuitableProducts($sftId);
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
        return $this->repository->generateProductCode($type, $distinctionID);
    }

    /**
     * Get Product App Trademark
     *
     * @param  array $mProductIds
     * @param  array $registerTrademarkProdIds
     * @return collection
     */
    public function getProductAppTrademark($mProductIds, $registerTrademarkProdIds): Collection
    {
        return $this->repository->getProductAppTrademark($mProductIds, $registerTrademarkProdIds);
    }

    /**
     * Get Product App Trademark is apply true
     *
     * @param  array $mProductIds
     * @param  array $registerTrademarkProdIds
     * @return collection
     */
    public function getProductAppTrademarkIsApplyTrue($mProductIds, $registerTrademarkProdIds): Collection
    {
        return $this->repository->getProductAppTrademarkIsApplyTrue($mProductIds, $registerTrademarkProdIds);
    }
    /**
     * Get Product App Trademark
     *
     * @param  array $mProductIds
     * @param  array $appTrademarkProdIds
     * @return collection
     */
    public function getProductAppTrademarkByAppTrademarkProd($mProductIds, $appTrademarkProdIds): Collection
    {
        return $this->repository->getProductAppTrademarkByAppTrademarkProd($mProductIds, $appTrademarkProdIds);
    }

    /**
     * GetInfoProdByRegisterTrademark
     *
     * @param int $registerTrademarkId
     * @return mixed
     */
    public function getInfoProdByRegisterTrademark(int $registerTrademarkId, $params = [])
    {
        return $this->repository->getInfoProdByRegisterTrademark($registerTrademarkId, $params);
    }

    /**
     * Get Info Prod By Register Trademark Query
     *
     * @param int $registerTrademarkId
     * @return mixed
     */
    public function getInfoProdByRegisterTrademarkQuery(int $registerTrademarkId)
    {
        return $this->repository->getInfoProdByRegisterTrademarkQuery($registerTrademarkId);
    }

    /**
     * GetDataSearchProduct
     *
     * @param array $inputs
     * @param $dataSession
     * @return mixed
     */
    public function getDataSearchProduct(array $inputs, $dataSession)
    {
        return $this->repository->getDataSearchProduct($inputs, $dataSession);
    }

    /**
     * EditDataGoodMasterProduct
     *
     * @param array $inputs
     * @return boolean
     */
    public function editDataGoodMasterProduct(array $inputs): bool
    {
        try {
            DB::beginTransaction();
            if (!empty($inputs['data'])) {
                foreach ($inputs['data'] as $item) {
                    //m_products
                    if (!empty($item['m_product_id'])) {
                        $product = $this->repository->find($item['m_product_id']);
                        $isParentValue = !empty($item['is_parent']) && empty($item['parent_product_number']) ? MProduct::IS_PARENT : MProduct::IS_NOT_PARENT;
                        $parentIdValue = null;
                        if (!$isParentValue && !empty($item['parent_product_number'])) {
                            $prodParent = $this->repository->findByCondition(['products_number' => $item['parent_product_number']])
                                ->where('id', '!=', $item['m_product_id'])
                                ->first();
                            if ($prodParent) {
                                $parentIdValue = $prodParent->id;
                            }
                        }
                        if ($product) {
                            $this->repository->update($product, [
                                'm_distinction_id' => $item['m_distinction_id'],
                                'name' => $item['m_product_name'],
                                'is_parent' => $isParentValue,
                                'parent_id' => $parentIdValue
                            ]);

                            //m_codes & m_product_codes
                            if (isset($item['m_codes']) && count($item['m_codes']) > 0) {
                                foreach ($item['m_codes'] as $itemCode) {
                                    if (!empty($itemCode['id'])) {
                                        $modelCode = $this->mCodeRepository->find($itemCode['id']);
                                        if ($modelCode) {
                                            if (!empty($itemCode['status_delete']) && $itemCode['status_delete'] == IS_DELETE) {
                                                //delete m_product_codes, m_codes
                                                MProductCode::where('m_product_id', $product->id)->where('m_code_id', $modelCode->id)->delete();
                                                $modelCode->delete();
                                            } else {
                                                if (!$this->checkExistsCodeName(['id' => $modelCode->id, 'name' => $itemCode['name']])) {
                                                    //update
                                                    if ($modelCode->name != $itemCode['name']) {
                                                        $this->mCodeRepository->update($modelCode, ['name' => $itemCode['name']]);
                                                    }
                                                }
                                            }
                                        }
                                    } else {
                                        //create code new
                                        if (!$this->checkExistsCodeName(['name' => $itemCode['name']])) {
                                            $newCode = $this->mCodeRepository->create([
                                                'admin_id' => auth()->user()->id,
                                                'name' => $itemCode['name']
                                            ]);
                                            if ($newCode) {
                                                $this->mProductCodeRepository->create([
                                                    'm_product_id' => $product->id,
                                                    'm_code_id' => $newCode->id
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
            DB::commit();

            return true;
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);

            return false;
        }
    }

    /**
     * CheckExistsCodeName
     *
     * @param $data
     * @return bool
     */
    public function checkExistsCodeName($data)
    {
        $code = $this->mCodeRepository->findByCondition($data)->first();
        if ($code) {
            return true;
        }
        return false;
    }
}
