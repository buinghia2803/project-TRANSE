<?php

namespace App\Repositories;

use App\Models\AppTrademark;
use App\Models\AppTrademarkProd;
use App\Models\MProduct;
use App\Models\TrademarkInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class AppTrademarkRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param AppTrademark $appTrademark
     * @return  void
     */
    public function __construct(AppTrademark $appTrademark)
    {
        $this->model = $appTrademark;
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
            case 'status':
            case 'trademark_id':
                return $query->where($column, $data);
            case 'type_pages':
                return $query->whereIn('type_page', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }

    /**
     * Get Product App Trademark
     *
     * @param mixed $trademarkId
     * @return Collection
     */
    public function getProductAppTrademark($trademarkId): Collection
    {
        $productAppTrademark = MProduct::whereHas('app_trademarks', function ($q) use ($trademarkId) {
            $q->whereHas('trademark', function ($query) {
                return $query->where('user_id', auth()->user()->id);
            })->where('trademark_id', $trademarkId)->where('status', '<>', 0);
        })->with([
            'mDistinction',
            'app_trademarks' => function ($q) use ($trademarkId) {
                $q->where('app_trademarks.trademark_id', $trademarkId)
                    ->select('app_trademarks.id', 'app_trademarks.trademark_id');
            }
        ])->get()
            ->groupBy('mDistinction.name');

        return $productAppTrademark;
    }

    /**
     * Get data product of u031 pass
     *
     * @param int $id
     * @return object
     */
    public function getDataProductByAppTrademark(int $id)
    {
        $data = AppTrademark::rightJoin('trademark_infos', 'app_trademarks.id', 'trademark_infos.target_id')
            ->leftJoin('trademarks', 'trademarks.id', 'app_trademarks.trademark_id')
            ->rightJoin('app_trademark_prods', 'app_trademarks.id', 'app_trademark_prods.app_trademark_id')
            ->rightJoin('m_products', 'app_trademark_prods.m_product_id', 'm_products.id')
            ->rightJoin('m_distinctions', 'm_products.m_distinction_id', 'm_distinctions.id')
            ->select(
                'trademark_infos.name as trademark_info_name',
                'm_distinctions.name as distinction_name',
                'm_distinctions.id as distinction_id',
                'm_products.name as product_name',
                'm_products.id as product_id'
            )
            ->where('trademarks.user_id', $id)
            ->where('trademark_infos.type', TrademarkInfo::TYPE_TRADEMARK)
            ->where('app_trademark_prods.is_apply', AppTrademarkProd::IS_APPLY)
            ->groupBy('trademark_infos.name', 'm_distinctions.name', 'm_products.name', 'm_products.id')->get();

        return $data;
    }

    /**
     * Check user max 50 record status = 0
     *
     * @return int
     */
    public function checkUserMax50Record()
    {
        $countAppTrademark = AppTrademark::where('status', AppTrademark::STATUS_UNREGISTERED_SAVE)->whereHas('trademark', function ($q) {
            return $q->where('user_id', auth()->user()->id);
        })->get()->count();

        return $countAppTrademark;
    }
}
