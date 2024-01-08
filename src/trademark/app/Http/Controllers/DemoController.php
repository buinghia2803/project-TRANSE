<?php

namespace App\Http\Controllers;

use App\Services\GMO\GMOHelper;
use App\Services\GMO\GMOService;
use Illuminate\Support\Facades\Auth;

class DemoController extends Controller
{
    private GMOService $gmoService;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(GMOService $gmoService)
    {
        $this->gmoService = $gmoService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return  void
     */
    public function index()
    {
        // $this->testGMOPayment();
        // $this->testGMOGetInfoOrder('trademark-6327f09a75200');
        // Auth::guard('admin')->onceUsingId(2);
    }

    /**
     * Demo GMO Payment
     *
     * @return  void
     */
    public function testGMOPayment()
    {
        $orderID = GMOHelper::generateOrderID();

        // Create EntryTran before execute
        $entryTran = $this->gmoService->creditEntryTran([
            'OrderID' => $orderID,
            'Amount' => '1000',
            'Tax' => '100',
        ]);
        if (empty($entryTran)) {
            dd('Get EntryTran Response Data Fail');
        }

        // Execute transition
        $execData = $this->gmoService->creditExecTran($entryTran, [
            'OrderID'       => $orderID,
            'CardNo'        => '4111111111111111',
            'Expire'        => '2512',
            'SecurityCode'  => '123',
        ]);
        if (empty($execData)) {
            dd('Get ExecTran Response Data Fail');
        }
        dd($execData);
    }

    /**
     * Demo GMO get info order
     *
     * @return  void
     */
    public function testGMOGetInfoOrder($orderID)
    {
        $searchTradeMulti = $this->gmoService->SearchTradeMulti([
            'OrderID'       => $orderID,
            'PayType'       => '0'
        ]);
        dd($searchTradeMulti);
    }
}
