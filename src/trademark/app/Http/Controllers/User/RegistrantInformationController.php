<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\TrademarkService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\MNationService;
use App\Services\MPrefectureService;
use App\Services\TrademarkInfoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use App\Services\AppTrademarkService;

class RegistrantInformationController extends Controller
{
    protected UserService $userService;

    protected TrademarkService $trademarkService;

    private MNationService $mNationService;

    private MPrefectureService $mPrefectureService;

    private AppTrademarkService $appTrademarkService;

    private TrademarkInfoService $trademarkInfoService;

    /**
     * Constructor
     *
     * @param   UserService      $userService
     * @param   TrademarkService $trademarkService
     * @param   mNationService      $mNationService
     * @param   mPrefectureService $mPrefectureService
     * @param   TrademarkInfoService $trademarkInfoService
     *
     * @return  void
     */
    public function __construct(
        UserService $userService,
        TrademarkService $trademarkService,
        MNationService $mNationService,
        MPrefectureService $mPrefectureService,
        TrademarkInfoService $trademarkInfoService
    )
    {
        $this->userService = $userService;
        $this->trademarkService = $trademarkService;
        $this->mNationService = $mNationService;
        $this->mPrefectureService = $mPrefectureService;
        $this->trademarkInfoService = $trademarkInfoService;
    }

    /**
     * Display a listing of the resource.
     *
     * @param integer $id
     *
     * @return View
     */
    public function enterRegistrantInformation($id): View
    {
        //nations
        $nations = $this->mNationService->list([]);

        //prefectures
        $prefectures = $this->mPrefectureService->list([]);

        return view('user.modules.common.trademark_registrant_information', compact(
            'nations',
            'prefectures'
        ));
    }

    /**
     * Get Data Click Copy
     *
     * @return JsonResponse
     */
    public function getDataClickCopy(): JsonResponse
    {
        $userLoginId = Auth::guard('web')->user()->id;
        $data = $this->userService->find($userLoginId);

        return response()->json([
            'data' => $data,
        ]);
    }

    /**
     * Get Data Click Copy
     *
     * @param Request $request
     * @param integer $trademark_id
     *
     * @return  RedirectResponse
     */
    public function updateInformation(Request $request, $trademark_id): RedirectResponse
    {
        try {
            $this->trademarkInfoService->updateOrCreateTrademarkInfo($request, $trademark_id);

            return redirect()->back();
        } catch (\Throwable $th) {
            return redirect()->back();
        }
    }
}
