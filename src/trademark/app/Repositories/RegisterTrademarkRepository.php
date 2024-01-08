<?php

namespace App\Repositories;

use App\Models\RegisterTrademark;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RegisterTrademarkRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   RegisterTrademark $registerTrademark
     * @return  void
     */
    public function __construct(RegisterTrademark $registerTrademark)
    {
        $this->model = $registerTrademark;
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
            case 'ids':
                return $query->whereIn('id', $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }

    /**
     * Get Product Trademark
     *
     * @param  mixed $trademarkId
     * @return Collection
     */
    public function getProductTrademark($trademarkId): Collection
    {
        $authId = auth()->user()->id;
        $query = DB::select("
        SELECT trademarks.user_id ,rt.id, rt.trademark_id,rt.deleted_at ,rtp.app_trademark_prod_id,mp.name AS name_product, mp.m_distinction_id, md.name
        FROM register_trademarks AS rt
        JOIN trademarks ON trademarks.id = rt.trademark_id
        JOIN register_trademark_prods AS rtp ON rt.id = rtp.register_trademark_id
        JOIN app_trademark_prods AS atp ON atp.id = rtp.app_trademark_prod_id
        JOIN m_products AS mp ON atp.m_product_id = mp.id
        JOIN m_distinctions AS md ON md.id = mp.m_distinction_id
        WHERE rt.trademark_id = $trademarkId AND rt.deleted_at is null AND trademarks.user_id = $authId
        ");
        $collection = collect($query)->groupBy('name');

        return $collection;
    }

    /**
     * Get Register Trademark Of User
     *
     * @param int $registerTrademarkId
     * @return Collection
     */
    public function getRegisterTrademarkOfUser(int $registerTrademarkId)
    {
        return $this->model->where('id', $registerTrademarkId)
            ->whereHas('trademark', function ($q) {
                $q->where('user_id', auth()->user()->id);
            })->first();
    }
}
