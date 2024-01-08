<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\AppTrademarkService;
use App\Services\PrecheckService;
use App\Services\RegisterTrademarkService;
use App\Services\TrademarkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ApplicationProductController extends Controller
{
    protected $appTrademarkService;
    protected $registerTrademarkService;
    protected $precheckService;

    /**
     * Constructor
     *
     * @param   RegisterTrademarkService $registerTrademarkService
     * @param   AppTrademarkService $appTrademarkService
     * @param   PrecheckService $precheckService
     *
     * @return  void
     */
    public function __construct(
        RegisterTrademarkService $registerTrademarkService,
        AppTrademarkService $appTrademarkService,
        PrecheckService $precheckService,
        TrademarkService $trademarkService
    )
    {
        $this->registerTrademarkService = $registerTrademarkService;
        $this->appTrademarkService = $appTrademarkService;
        $this->precheckService = $precheckService;
        $this->trademarkService = $trademarkService;
    }

    /**
     * View App product list
     *
     * @param $id - app_trademark_id
     * @return View
     */
    public function viewAppProductList($id): View
    {
        $trademark = $this->trademarkService->findByCondition(['id' => $id])->first();

        if (!$trademark) {
            abort(404);
        }

        Auth::onceUsingID($trademark->user_id);

        $productList = [];
        $productRegisterTrademark = $this->registerTrademarkService->getProductTrademark($id);
        $productAppTrademark = $this->appTrademarkService->getProductAppTrademark($id);
        $productPrecheck = $this->precheckService->getProductPrecheck($id);
        if ($productRegisterTrademark->count() > 0) {
            $productList = $productRegisterTrademark;
        } elseif ($productAppTrademark->count() > 0) {
            $productList = $productAppTrademark;
        } elseif ($productPrecheck->count() > 0) {
            $productList = $productPrecheck;
        }

        return view('user.modules.app-product-list.list', compact('productList'));
    }
}
