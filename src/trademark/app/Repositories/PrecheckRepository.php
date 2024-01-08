<?php

namespace App\Repositories;

use App\Models\MPriceList;
use App\Models\MProduct;
use App\Models\MyFolderProduct;
use App\Models\Precheck;
use App\Models\PrecheckComment;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class PrecheckRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   Precheck $precheck
     * @return  void
     */
    public function __construct(Precheck $precheck)
    {
        $this->model = $precheck;
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
                return $query->where($column, $data);
            case 'raw':
                return $query->whereRaw($data);
            default:
                return $query;
        }
    }

    /**
     * Get precheck of user
     *
     * @param integer $trademarkId
     * @return Collection
     */
    public function getPrecheckOfUser(int $trademarkId)
    {
        return $this->queryGetPrecheckOfUser($trademarkId)
                    ->orderBy('created_at', 'desc')
                    ->with('trademark')->first();
    }

    /**
     * Get precheck of user
     *
     * @param integer $trademarkId
     * @param integer $statusRegis
     * @return Collection
     */
    public function getPrecheckOfUserByStatusRegis(int $trademarkId, int $statusRegis)
    {
        return $this->queryGetPrecheckOfUser($trademarkId)
            ->where('status_register', $statusRegis)
            ->orderBy('created_at', 'desc')
            ->with('trademark')->first();
    }

    /**
     * Get precheck
     *
     * @param integer $trademarkId
     * @return Collection
     */
    public function getPrecheck(int $trademarkId)
    {
        return $this->model->where('trademark_id', $trademarkId)->whereHas('trademark', function ($q) {
            $q->whereHas('user', function ($q) {
                $q->where('id', auth()->user()->id);
            });
        })->orderBy('created_at', 'desc')->with([
            'trademark',
            'precheckComments' => function ($q) {
                $q->where('precheck_comments.type', PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS)
                    ->whereIn('precheck_comments.input_of_page', [PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN, PrecheckComment::INPUT_OF_PAGE_SHIKI]);
            }
        ])->first();
    }

    /**
     * Get precheck v2.
     *
     * @param integer $trademarkId
     * @param integer $precheckId
     * @return Collection
     */
    public function getPrecheckWithId(int $trademarkId, int $precheckId)
    {
        return $this->model->where('id', $precheckId)->where('trademark_id', $trademarkId)->whereHas('trademark', function ($q) {
            $q->whereHas('user', function ($q) {
                $q->where('id', auth()->user()->id);
            });
        })->orderBy('created_at', 'desc')->with([
            'trademark',
            'precheckComments' => function ($q) {
                $q->where('precheck_comments.type', PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS)
                    ->whereIn('precheck_comments.input_of_page', [PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN, PrecheckComment::INPUT_OF_PAGE_SHIKI]);
            }
        ])->first();
    }

    /**
     * Get info precheck show table of user
     *
     * @param int $idPrecheck
     * @return Collection
     */
    public function getInfoPrecheckShowTable(int $idPrecheck): Collection
    {
        return MProduct::whereHas('prechecks', function ($q) use ($idPrecheck) {
            $q->where('precheck_id', $idPrecheck);
        })->with([
            'precheckProduct' => function ($q) use ($idPrecheck) {
                $q->where('precheck_id', $idPrecheck)->with('precheckResult');
            },
            'mDistinction',
            'prechecks' => function ($q) use ($idPrecheck) {
                $q->where('prechecks.id', $idPrecheck)
                    ->select('prechecks.id', 'prechecks.trademark_id');
            }
        ])->get()
        ->groupBy('mDistinction.name');
    }

    /**
     * Query get info precheck show table of user
     *
     * @param int $trademarkId
     * @return Builder
     */
    public function queryGetPrecheckOfUser(int $trademarkId): Builder
    {
        return $this->model->where('trademark_id', $trademarkId)->whereHas('trademark', function ($q) {
            $q->whereHas('user', function ($q) {
                $q->where('id', auth()->user()->id);
            });
        });
    }

    /**
     * Get product of distinction
     *
     * @param int $idPrecheck
     *
     * @return Collection
     */
    public function getProductOfDistinction(int $idPrecheck)
    {
        $prechecks = Precheck::with(['products'])->find($idPrecheck);

        $products = $prechecks->products;
        $productIds = $products->pluck('id')->toArray();

        $folderID = request()->folder_id ?? null;
        if (!empty($folderID)) {
            $myFolderProducts = MyFolderProduct::where('my_folder_id', $folderID)->get();
            $myFolderProductIds = $myFolderProducts->pluck('m_product_id')->toArray();
            $productIds = array_merge($productIds, $myFolderProductIds);
        }
        $productIds = array_unique($productIds);
        $products = MProduct::whereIn('id', $productIds)
            ->with([
                'precheckProduct' => function ($q) use ($idPrecheck) {
                    $q->where('precheck_id', $idPrecheck)->with('precheckResult');
                },
                'mDistinction',
                'prechecks' => function ($q) use ($idPrecheck) {
                    $q->where('prechecks.id', $idPrecheck)
                        ->select('prechecks.id', 'prechecks.trademark_id');
                }
            ])
            ->get()
            ->sortBy('mDistinction.name')
            ->groupBy('mDistinction.name');

        return $products;
    }

    /**
     * Get price pack service
     *
     * @return array
     */
    public function getPricePackService()
    {
        $tax = Setting::first()->value ?? 1;

        $prices = MPriceList::where('service_type', MPriceList::APPLICATION)
            ->whereIn('package_type', [
                MPriceList::PACK_A_UP_3_ITEMS,
                MPriceList::PACK_B_UP_3_ITEMS,
                MPriceList::PACK_C_UP_3_ITEMS,
            ])
            ->select('*', \DB::raw("( base_price + base_price * $tax / 100 ) as base_price_multiplication_tax"))
            ->get()
            ->toArray();

        $priceFours = MPriceList::where('service_type', MPriceList::APPLICATION)
            ->whereIn('package_type', [
                MPriceList::PACK_A_EACH_3_ITEMS,
                MPriceList::PACK_B_EACH_3_ITEMS,
                MPriceList::PACK_C_EACH_3_ITEMS,
            ])
            ->select('*', \DB::raw("( base_price + base_price * $tax / 100 ) as base_price_multiplication_tax"))
            ->get()
            ->toArray();

        $result[] = $prices;
        $result[] = $priceFours;

        return $result;
    }

    /**
     * Get price pack service
     *
     * @param int $serviceTypePackA
     * @param string $packageTypePackA
     * @return array
     */
    public function getPriceOnePackService(int $serviceTypePackA, string $packageTypePackA)
    {
        $tax = Setting::first()->value ?? 1;

        $result = MPriceList::where('service_type', $serviceTypePackA)
            ->whereIn('package_type', [
                $packageTypePackA,
            ])
            ->select('*', \DB::raw("( base_price + base_price * $tax / 100 ) as base_price_multiplication_tax"))
            ->first()
            ->toArray();

        return $result;
    }

    /**
     * Get mail register cert repository
     *
     * @return Collection
     */
    public function getMailRegisterCertRepository()
    {
        $tax = Setting::first()->value ?? 1;

        $mailRegisterCert = MPriceList::where('service_type', MPriceList::AT_REGISTRATION)
            ->whereIn('package_type', [
                MPriceList::MAILING_CERTIFICATE_REGISTRATION,
            ])
            ->select('*', \DB::raw("( base_price + base_price * $tax / 100 ) as base_price_multiplication_tax"))
            ->first();

        return $mailRegisterCert;
    }

    /**
     * Get period registration repository
     *
     * @return Collection
     */
    public function getPeriodRegistrationPrecheck()
    {
        $tax = Setting::first()->value ?? 1;

        $periodRegistration = MPriceList::where('service_type', MPriceList::REGISTRATION)
            ->whereIn('package_type', [
                MPriceList::REGISTRATION_TERM_CHANGE,
            ])
            ->select('*', \DB::raw("( base_price + base_price * $tax / 100 ) as base_price_multiplication_tax"))
            ->first();

        return $periodRegistration;
    }

    /**
     * Get Product Precheck
     *
     * @param  mixed $trademarkId
     * @return Collection
     */
    public function getProductPrecheck($trademarkId): Collection
    {
        $productPrechecks = MProduct::whereHas('prechecks', function ($q) use ($trademarkId) {
            $q->whereHas('trademark', function ($query) {
                return $query->where('user_id', auth()->user()->id);
            })->where('trademark_id', $trademarkId);
        })->with([
            'precheckResults',
            'mDistinction',
            'prechecks' => function ($q) use ($trademarkId) {
                $q->where('prechecks.trademark_id', $trademarkId)
                    ->select('prechecks.id', 'prechecks.trademark_id');
            }
        ])->get()
            ->groupBy('mDistinction.name');

        return $productPrechecks;
    }
}
