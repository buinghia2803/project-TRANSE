<?php

namespace App\Repositories;

use App\Models\Agent;
use App\Models\AgentGroupMap;
use App\Models\AppTrademark;
use App\Models\Payment;
use App\Models\Trademark;
use App\Models\TrademarkInfo;
use App\Repositories\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TrademarkRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   Trademark $trademark
     * @return  void
     */
    public function __construct(Trademark $trademark)
    {
        $this->model = $trademark;
    }

    /**
     * Merge query
     *
     * @param   mixed $query
     * @param   mixed $column
     * @param   mixed $data
     * @return  mixed
     */
    public function mergeQuery($query, $column, $data)
    {
        if ($column == 'deleted' && $data) {
            $query->withTrashed();
        }
        switch ($column) {
            case 'id':
            case 'user_id':
            case 'trademark_number':
            case 'application_number':
            case 'type_trademark':
            case 'name_trademark':
            case 'image_trademark':
            case 'reference_number':
            case 'status_management':
                return $query->where($column, $data);
            case 'trademark_numbers':
                return $query->whereIn('trademark_number', $data);
            default:
                return $query;
        }
    }

    /**
     * Find trademark by id
     *
     * @param integer $id - id trademark
     * @return Model
     */
    public function findById(int $id): Model
    {
        return Trademark::where('id', $id)->with([
            'appTrademark' => function ($query) {
                return $query->with([
                    'trademarkInfo',
                    'appTrademarkProd' => function ($q) {
                        return $q->join('m_products', 'app_trademark_prods.m_product_id', '=', 'm_products.id')
                            ->join('m_distinctions', 'm_products.m_distinction_id', '=', 'm_distinctions.id')
                            ->select('m_products.*', 'app_trademark_prods.*', 'm_distinctions.name as distinction_name');
                    }
                ]);
            },
            'registerTrademark' => function ($query) {
                return $query->with([
                    'registerTrademarkProds' => function ($q) {
                        return $q->join('app_trademark_prods', 'register_trademark_prods.app_trademark_prod_id', '=', 'app_trademark_prods.id')
                            ->join('m_products', 'app_trademark_prods.m_product_id', '=', 'm_products.id')
                            ->join('m_distinctions', 'm_distinctions.id', '=', 'm_products.m_distinction_id')
                            ->select('register_trademark_prods.*', 'm_products.*', 'app_trademark_prods.*', 'm_distinctions.name as distinction_name');
                    },
                ]);
            },
        ])->first();
    }

    /**
     * Show Trademark
     *
     * @param  int $id
     * @return Model
     */
    public function showTrademark(int $id): Model
    {
        return Trademark::where('id', $id)->with([
            'user',
            'appTrademark' => function ($query) {
                return $query->with([
                    'trademarkInfo' => function ($q) {
                        return $q->orderBy('id', 'DESC')->first();
                    },
                    'appTrademarkProd' => function ($q) {
                        return $q->join('m_products', 'app_trademark_prods.m_product_id', '=', 'm_products.id')
                            ->join('m_distinctions', 'm_products.m_distinction_id', '=', 'm_distinctions.id')
                            ->select('m_products.*', 'app_trademark_prods.*', 'm_distinctions.name as distinction_name');
                    },
                ]);
            },
            'registerTrademark' => function ($query) {
                return $query->with([
                    'registerTrademarkProds' => function ($q) {
                        return $q->join('app_trademark_prods', 'register_trademark_prods.app_trademark_prod_id', '=', 'app_trademark_prods.id')
                            ->join('m_products', 'app_trademark_prods.m_product_id', '=', 'm_products.id')
                            ->join('m_distinctions', 'm_distinctions.id', '=', 'm_products.m_distinction_id')
                            ->select('register_trademark_prods.*', 'm_products.*', 'app_trademark_prods.*', 'm_distinctions.name as distinction_name');
                    },
                ]);
            },
        ])->first();
    }

    /**
     * Find Registrant Information
     *
     * @param  integer $id
     * @return mixed
     */
    public function findRegistrantInformation(int $id)
    {
        return $this->model->where('id', $id)->with([
            'appTrademark' => function ($query) {
                return $query->where('status', '!=', 1)->with([
                    'trademarkInfo' => function ($q) {
                        return $q->where('type', 1)->join('m_nations', 'trademark_infos.m_nation_id', '=', 'm_nations.id')
                            ->join('m_prefectures', 'trademark_infos.m_prefecture_id', '=', 'm_prefectures.id')
                            ->select(
                                'trademark_infos.*',
                                'm_nations.name as nations_name',
                                'm_prefectures.name as prefectures_name'
                            );
                    },
                ]);
            },
        ])->first();
    }

    /**
     * Get TradeMark Register
     *
     * @param  mixed $userId
     * @return mixed
     */
    public function getTradeMarkRegister($userId) {
        return $this->model->where('user_id', $userId)->with([
            'appTrademark' => function ($query) {
                return $query->join('trademark_infos', 'app_trademarks.id', '=', 'trademark_infos.target_id')->join('m_nations', 'trademark_infos.m_nation_id', '=', 'm_nations.id')
                ->join('m_prefectures', 'trademark_infos.m_prefecture_id', '=', 'm_prefectures.id')
                ->select(
                    'trademark_infos.*',
                    'app_trademarks.*',
                    'm_nations.name as nations_name',
                    'm_prefectures.name as prefectures_name'
                );
            },
        ])->get();
    }

    /**
     * Get Trademark Notice
     *
     * @param  mixed $trademarkId
     * @return void
     */
    public function getTrademarkNotice($trademarkId)
    {
        return $this->model->with('comparisonTrademarkResult')->whereHas('notice', function ($query) use ($trademarkId) {
            return $query->where('trademark_id', $trademarkId);
        })
            ->where('id', $trademarkId)
            ->first();
    }

    /**
     * Get Trademark Apply Document To Check
     *
     * @param  mixed $id
     * @param  mixed $identifierCodeType
     */
    public function getTrademarkApplyDocumentToCheck($id)
    {
        $query = DB::select('
        SELECT DISTINCT
            tm.id,
            tm.trademark_number,
            tm.application_date,
            tm.type_trademark,
            tm.name_trademark,
            tm.image_trademark,
            trademark_infos.target_id,
            payments.cost_print_application_one_distintion,
            payments.cost_print_application_add_distintion,
            payment_prods.payment_id,
            m_distinctions.name AS distinction_name,
            m_distinctions.id,
            m_products.name AS product_name,
            m_products.m_distinction_id,
            app_trademarks.trademark_id,
            app_trademarks.id as app_trademark_id,
            app_trademarks.comment_office,
            app_trademarks.status as app_trademark_status,
            payer_infos.payment_type
        FROM trademarks As tm
        JOIN payments ON payments.trademark_id = tm.id
        JOIN payment_prods ON payments.id = payment_prods.payment_id
        JOIN m_products ON m_products.id = payment_prods.m_product_id
        JOIN m_distinctions ON m_distinctions.id = m_products.m_distinction_id
        JOIN payer_infos ON payer_infos.id = payments.payer_info_id
        JOIN app_trademarks ON app_trademarks.trademark_id = tm.id
        JOIN trademark_infos ON trademark_infos.target_id = app_trademarks.id
        JOIN m_nations ON m_nations.id = trademark_infos.m_nation_id
        JOIN m_prefectures ON m_prefectures.id = trademark_infos.m_prefecture_id
        WHERE
            tm.id = ' . $id . '
            AND payments.trademark_id = tm.id
            AND payment_prods.payment_id = payments.id
            AND app_trademarks.trademark_id = tm.id
            AND payment_prods.m_product_id = m_products.id
            AND trademark_infos.target_id = app_trademarks.id
            AND m_products.id = payment_prods.m_product_id
            AND payments.type = ' .  Payment::TYPE_TRADEMARK . '
        ');
        $newCollection = [];
        foreach ($query as $item) {
            $trademarkInfo = TrademarkInfo::with('mPrefecture')->where('target_id', $item->app_trademark_id)->where('type', TrademarkInfo::TYPE_TRADEMARK)->get()->last();
            $newCollection['trademark_info_name'] = $trademarkInfo->name ?? '';
            $newCollection['prefecture_name'] = $trademarkInfo->mPrefecture->name ?? '';
            $newCollection['address_second'] = $trademarkInfo->address_second ?? '';
            $newCollection['address_three'] = $trademarkInfo->address_three ?? '';
            $newCollection['trademark_number'] = $item->trademark_number ?? '';
            $newCollection['application_date'] = $item->application_date ?? '';
            $newCollection['type_trademark'] = $item->type_trademark ?? '';
            $newCollection['name_trademark'] = $item->name_trademark ?? '';
            $newCollection['comment_office'] = $item->comment_office ?? '';
            $newCollection['image_trademark'] = $item->image_trademark ?? '';
            $newCollection['payment_type'] = $item->payment_type ?? '';
            $newCollection['trademark_id'] = $item->id ?? '';
            $newCollection['app_trademark_status'] = $item->app_trademark_status ?? '';
            $newCollection['app_trademark_id'] = $item->app_trademark_id ?? '';
            $newCollection['cost_print_application_one_distintion'] = $item->cost_print_application_one_distintion;
            $newCollection['cost_print_application_add_distintion'] = $item->cost_print_application_add_distintion;
            $newCollection['data'][] = $item;
        }
        if (count($newCollection)) {
            $newCollection['data'] = collect($newCollection['data'])->groupBy('distinction_name');
        }
        return $newCollection;
    }
}
