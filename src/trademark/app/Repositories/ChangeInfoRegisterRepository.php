<?php

namespace App\Repositories;

use App\Models\ChangeInfoRegister;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ChangeInfoRegisterRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   ChangeInfoRegister $changeInfoRegister
     * @return  void
     */
    public function __construct(ChangeInfoRegister $changeInfoRegister)
    {
        $this->model = $changeInfoRegister;
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
     * Get Change Info Register
     *
     * @param  mixed $trademarkId
     * @return Model
     */
    public function getChangeInfoRegisterKenrisha($trademarkId): ?Model
    {
        $changeInfoRegisterDraft = $this->model
            ->where('trademark_id', $trademarkId)->where('type', REGISTER)
            ->where('is_send', IS_SEND_FALSE)
            ->with(
                'trademark',
                'payment',
                'payment.payerInfo',
                'nation',
                'prefecture'
            )->orderBy('id', 'desc')
            ->first();

        return $changeInfoRegisterDraft;
    }

    /**
     * Get Change Info Register Kenrisha
     *
     * @param  mixed $trademarkId
     * @return Model
     */
    public function getChangeInfoRegister($trademarkId): ?Model
    {
        $changeInfoRegisterDraft = $this->model->with(
            'trademark',
            'payment',
            'payment.payerInfo',
            'nation',
            'prefecture'
        )->where('trademark_id', $trademarkId)->where('type', APPLICATION)->where('is_send', IS_SEND_FALSE)->orderBy('id', 'desc')->first();

        return $changeInfoRegisterDraft;
    }
}
