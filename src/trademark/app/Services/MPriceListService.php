<?php

namespace App\Services;

use App\Models\MPriceList;
use App\Models\Setting;
use App\Repositories\MPriceListRepository;
use App\Repositories\SettingRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class MPriceListService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param MPriceListRepository $mPriceListRepository
     */
    public function __construct(MPriceListRepository $mPriceListRepository, SettingRepository $settingRepository)
    {
        $this->repository = $mPriceListRepository;
        $this->settingRepository = $settingRepository;
    }

    /**
     * Query get price common of precheck
     *
     * @param integer $service_type
     * @param string $package_type
     *
     * @return MPriceList
     */
    public function getPriceCommonOfPrecheck(int $service_type, string $package_type): MPriceList
    {
        return $this->repository->getPriceCommonOfPrecheck($service_type, $package_type);
    }

    /**
     * Get Price Includes Tax
     *
     * @param  mixed $service_type
     * @param  mixed $package_type
     * @return Float
     */
    public function getPriceIncludesTax(int $service_type, string $package_type): Float
    {
        $setting = $this->settingRepository->getSetting();
        $mPriceList = $this->getPriceCommonOfPrecheck($service_type, $package_type);
        $fee = $mPriceList->base_price + ($mPriceList->base_price * $setting->value) / 100;

        return $fee;
    }
}
