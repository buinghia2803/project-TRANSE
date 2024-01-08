<?php

namespace App\Repositories;

use App\Models\Admin;
use App\Models\MPriceList;
use App\Models\MProduct;
use App\Models\MyFolderProduct;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\SFTComment;
use App\Models\SFTSuitableProduct;
use App\Models\SupportFirstTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SupportFirstTimeRepository extends BaseRepository
{
    /**
     * Initializing the instances and variables
     *
     * @param   SupportFirstTime $supportFirstTime
     * @return  void
     */
    public function __construct(SupportFirstTime $supportFirstTime)
    {
        $this->model = $supportFirstTime;
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
     * Get list support first time
     *
     * @param  array $input.
     * @return Model
     */
    public function getSupportFirstTime($id)
    {
        $sft = $this->model->with(
            'trademark',
            'StfSuitableProduct',
            'StfSuitableProduct.mProduct',
            'StfContentProduct',
            'stfComment'
        )->where('id', $id)->first();

        return $sft;
    }

    /**
     * Get distinction form suitable products.
     *
     * @param int $id - sft_id
     */
    public function getDistinctionRepository(int $id): Collection
    {
        $sft = $this->getSupportFirstTime($id);
        $productIds = $sft->StfSuitableProduct->pluck('m_product_id')->toArray();

        $folderID = request()->folder_id ?? null;
        if (!empty($folderID)) {
            $myFolderProducts = MyFolderProduct::where('my_folder_id', $folderID)->get();
            $myFolderProductIds = $myFolderProducts->pluck('m_product_id')->toArray();
            $productIds = array_merge($productIds, $myFolderProductIds);
        }
        $productIds = array_unique($productIds);

        $products = MProduct::whereIn('id', $productIds)
            ->with([
                'mDistinction:id,name',
                'SftSuitableProduct' => function ($query) use ($id) {
                    return $query->where('support_first_time_id', $id);
                }
            ])
            ->get()
            ->groupBy('mDistinction.name')
            ->sortKeys();

        return $products;
    }

    public function getProductRepository($id)
    {
        $sft = $this->getSupportFirstTime($id);
        $suitableProductId = $sft->StfSuitableProduct->pluck('m_product_id')->toArray();

        $product = MProduct::whereIn('id', $suitableProductId)
            ->select('id')
            ->get()
            ->toArray();

        return $product;
    }

    /**
     * Get Price Pack Service
     *
     * @return void
     */
    public function getPricePackService()
    {
        $pricePackage = $this->getPricePackRepository();
        $countPricePackage = count($pricePackage);
        $percentTax = $this->getSetting();
        $feeTax = $percentTax->value / 100;
        for ($i = 0; $i < $countPricePackage; $i++) {
            for ($j = 0; $j < $countPricePackage + 1; $j++) {
                $pricePackage[$i][$j]['base_price'] = $pricePackage[$i][$j]['base_price'] + ($pricePackage[$i][$j]['base_price'] * $feeTax);
            }
        }

        return $pricePackage;
    }

    /**
     * Get price of pack
     *
     * @return array
     */
    public function getPricePackRepository(): array
    {
        $price = MPriceList::where('service_type', MPriceList::APPLICATION)
            ->whereIn('package_type', [
                MPriceList::PACK_A_UP_3_ITEMS,
                MPriceList::PACK_B_UP_3_ITEMS,
                MPriceList::PACK_C_UP_3_ITEMS,
            ])
            ->get()
            ->toArray();

        $priceFour = MPriceList::where('service_type', MPriceList::APPLICATION)
            ->whereIn('package_type', [
                MPriceList::PACK_A_EACH_3_ITEMS,
                MPriceList::PACK_B_EACH_3_ITEMS,
                MPriceList::PACK_C_EACH_3_ITEMS,
            ])
            ->get()
            ->toArray();

        $result[] = $price;
        $result[] = $priceFour;

        return $result;
    }

    /**
     * Get mailing register certification.
     *
     * @return MPriceList
     */
    public function getMailRegisterCertRepository(): MPriceList
    {
        return MPriceList::where('service_type', MPriceList::AT_REGISTRATION)
            ->whereIn('package_type', [
                MPriceList::MAILING_CERTIFICATE_REGISTRATION,
            ])
            ->first();
    }

    /**
     * Get Submit Fee
     *
     * @return MPriceList
     */
    public function getFeeSubmit(): MPriceList
    {
        return MPriceList::where('service_type', MPriceList::REGISTRATION)
            ->whereIn('package_type', [
                MPriceList::REGISTRATION_UP_3_PRODS,
            ])
            ->first();
    }

    /**
     * Get info support first time edit admin
     *
     * @param int $id - support_first_time_id
     * @return mixed
     */
    public function getInfoSupportFirstTimeEdit(int $id)
    {
        return $this->model->where('id', $id)
            ->with([
                'StfContentProduct:id,support_first_time_id,name,is_choice_admin',
                'StfSuitableProduct' => function ($q) {
                    $q->with([
                        'mProduct' => function ($q) {
                            $q->with('mCode');
                        },
                        'mDistinction'
                    ])->withTrashed();
                },
                'stfComment' => function ($q) {
                    $q->whereHas('admin', function ($q) {
                        $q->where('role', Admin::ROLE_ADMIN_TANTO);
                    })->where('type', SFTComment::TYPE_COMMENT_INSIDER);
                },
                'admin:id,name',
                'sftKeepData' => function ($q) {
                    $q->with('sftKeepDataProds', function ($queri) {
                        $queri->with(
                            [
                                'sftKeepDataProdCodes',
                                'sftSuitableProduct' => function ($j) {
                                    $j->with([
                                        'mDistinction',
                                        'mProduct' => function ($q) {
                                            $q->with('mCode:id,name');
                                        }
                                    ]);
                                }
                            ]
                        );
                    });
                }
            ])
            ->first();
    }
}
