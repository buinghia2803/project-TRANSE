<?php

namespace App\Repositories;

use App\Models\SFTSuitableProduct;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SFTSuitableProductRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   SFTSuitableProduct $sftSuitableProduct
     * @return  void
     */
    public function __construct(SFTSuitableProduct $sftSuitableProduct)
    {
        $this->model = $sftSuitableProduct;
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
            case 'admin_id':
            case 'm_product_id':
            case 'is_block':
            case 'is_choice_user':
            case 'support_first_time_id':
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }

    /**
     * Update is block column ajax
     *
     * @param array $inputs
     *  @return mixed
     */
    public function updateIsBlockAjax(array $inputs)
    {
        DB::beginTransaction();
        try {
            if (isset($inputs['ids']) && isset($inputs['is_block'])) {
                $idsArray = explode(',', $inputs['ids']);
                $this->model->whereIn('id', $idsArray)->update(['is_block' => $inputs['is_block']]);
                DB::commit();

                return true;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return false;
        }
    }
}
