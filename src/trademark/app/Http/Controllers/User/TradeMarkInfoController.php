<?php

namespace App\Http\Controllers\User;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\ChangeInfoRegister;
use App\Models\MPriceList;
use App\Models\TrademarkInfo;
use App\Services\ChangeInfoRegisterService;
use App\Services\Common\TrademarkTableService;
use App\Services\MNationService;
use App\Services\MPrefectureService;
use App\Services\MPriceListService;
use App\Services\RegisterTrademarkService;
use App\Services\TrademarkInfoService;
use App\Services\TrademarkService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;

class TradeMarkInfoController extends Controller
{
    protected $trademarkService;
    protected $mNationService;
    protected $mPrefectureService;
    protected $trademarkInfoService;
    protected $trademarkTableService;
    protected $mPriceListService;
    protected $changeInfoRegisterService;
    private RegisterTrademarkService $registerTrademarkService;

    public function __construct(
        TrademarkService $trademarkService,
        TrademarkInfoService $trademarkInfoService,
        MNationService $mNationService,
        MPrefectureService $mPrefectureService,
        TrademarkTableService $trademarkTableService,
        MPriceListService $mPriceListService,
        ChangeInfoRegisterService $changeInfoRegisterService,
        RegisterTrademarkService $registerTrademarkService
    )
    {
        $this->trademarkService = $trademarkService;
        $this->trademarkInfoService = $trademarkInfoService;
        $this->mNationService = $mNationService;
        $this->mPrefectureService = $mPrefectureService;
        $this->trademarkTableService = $trademarkTableService;
        $this->mPriceListService = $mPriceListService;
        $this->changeInfoRegisterService = $changeInfoRegisterService;
        $this->registerTrademarkService = $registerTrademarkService;
    }

    /**
     * Change address
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $currenUser = Auth::guard('web')->user();

        // Trademark
        $trademarks = $this->trademarkService->findByCondition([
            'status_management' => true,
            'user_id' => $currenUser->id ?? 0,
        ])
            ->doesnthave('registerTrademark')
            ->whereHas('appTrademark', function ($query) {
                $query->where('status', '<>', 0);
            })
            ->where('trademark_number', 'like', 'Q%')
            ->get();
        $trademarks = $this->trademarkService->formatListUser($trademarks);
        $trademarks = $trademarks->where('is_expired', false);

        $orderTrademarkField = $request->orderTrademarkField ?? 'soft_created_at';
        $orderTrademarkType = $request->orderTrademarkType ?? SORT_TYPE_DESC;
        $trademarks = CommonHelper::softCollection($trademarks, $orderTrademarkField, $orderTrademarkType);

        $page = $request->pageTrademark ?? 1;
        $trademarks = CommonHelper::paginate($trademarks, PAGE_LIMIT_50, $page, [
            'pageName' => 'pageTrademark',
        ]);

        // Register Trademark
        $orderRegisField = $request->orderRegisField ?? 'created_at';
        $orderRegisType = $request->orderRegisType ?? SORT_TYPE_DESC;

        if (in_array($orderRegisField, ['trademark_number'])) {
            $orderRegisField = 'trademarks.' . $orderRegisField;
        } else {
            $orderRegisField = 'register_trademarks.' . $orderRegisField;
        }

        $registerTrademarkPaginate = $this->registerTrademarkService->findByCondition([])
            ->join('trademarks', 'trademarks.id', 'register_trademarks.trademark_id')
            ->where('trademarks.user_id', $currenUser->id ?? 0)
            ->select('register_trademarks.*')
            ->orderBy($orderRegisField, $orderRegisType)
            ->paginate(PAGE_LIMIT_50, '[*]', 'pageRegis');
        $registerTrademarks = $this->registerTrademarkService->formatListUser($registerTrademarkPaginate->getCollection());

        // Url Back
        $urlBackDefault = route('user.top');
        $checkUrl = route('user.application-list.change-address');
        $backUrl = CommonHelper::setBackUrl($urlBackDefault, $checkUrl);

        return view('user.modules.trademark-info.index', compact(
            'trademarks',
            'registerTrademarks',
            'registerTrademarkPaginate',
            'backUrl',
        ));
    }

    /**
     * Change address 02
     *
     * @param $id - trademark_id
     * @return View
     */
    public function chageAddress02(Request $request, $id): View
    {
        $tradeMark = $this->trademarkService->findByCondition([
            'id' => $id,
            'user_id' => auth()->user()->id,
        ])->first();

        if (!$tradeMark) {
            abort(403);
        }
        $userInfo = auth()->user()->load(['nation', 'nation.prefecture']);
        $changeInfoRegisterDraft = $this->changeInfoRegisterService->getChangeInfoRegister($id);
        $tradeMarkInfos = $tradeMark->appTrademark ? $tradeMark->appTrademark->trademarkInfo : null;
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        $typeChangeName = TrademarkInfo::TYPE_CHANGE_NAME;
        $typeChangeAddress = TrademarkInfo::TYPE_CHANGE_ADDRESS;
        $typeChangeDouble = TrademarkInfo::TYPE_CHANGE_NAME_AND_ADDRESS;
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_8, $id);
        // Price Base
        $priceServiceChangeAddress = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REGISTRATION, MPriceList::APP_ADDRESS_CHANGE_PROCEDURES);
        $priceServiceChangeName = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::REGISTRATION, MPriceList::APP_NAME_CHANGE_PROCEDURE);
        $costBankTransferBase = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        // Price Includes Tax
        $priceServiceChangeAddressFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::REGISTRATION, MPriceList::APP_ADDRESS_CHANGE_PROCEDURES);
        $priceServiceChangeNameFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::REGISTRATION, MPriceList::APP_NAME_CHANGE_PROCEDURE);
        $costBankTransfer = $this->mPriceListService->getPriceIncludesTax(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        // Setting
        $setting = $this->mPriceListService->getSetting();
        $isChangeAddressFree = \App\Models\ChangeInfoRegister::IS_CHANGE_ADDRESS_FREE;

        $data = [
            'tradeMarkInfos' => $tradeMarkInfos,
            'prefectures' => $prefectures,
            'nations' => $nations,
            'typeChangeName' => $typeChangeName,
            'typeChangeAddress' => $typeChangeAddress,
            'typeChangeDouble' => $typeChangeDouble,
            'trademarkTable' => $trademarkTable,
            'priceServiceChangeAddress' => $priceServiceChangeAddress,
            'priceServiceChangeName' => $priceServiceChangeName,
            'priceServiceChangeNameFee' => $priceServiceChangeNameFee,
            'priceServiceChangeAddressFee' => $priceServiceChangeAddressFee,
            'costBankTransfer' => number_format($costBankTransfer, 0, '.', ''),
            'setting' => $setting,
            'costBankTransferBase' => $costBankTransferBase,
            'id' => $id,
            'changeInfoRegisterDraft' => $changeInfoRegisterDraft,
            'userInfo' => $userInfo,
            'isChangeAddressFree' => $isChangeAddressFree
        ];

        return view('user.modules.trademark-info.list_change_address02', $data);
    }

    /**
     * Get Change Address Kenrisha
     *
     * @param  mixed $request
     * @param  mixed $id - trademark_id
     * @return void
     */
    public function getChangeAddressKenrisha(Request $request, $id)
    {
        $tradeMark = $this->trademarkService->findByCondition([
            'id' => $id,
            'user_id' => auth()->user()->id,
        ])->first();

        if (!$tradeMark) {
            abort(403);
        }
        $userInfo = auth()->user()->load(['nation', 'nation.prefecture']);
        $changeInfoRegisterDraft = $this->changeInfoRegisterService->getChangeInfoRegisterKenrisha($id);
        $registerTrademark = $tradeMark->registerTrademark;
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        $typeChangeName = TrademarkInfo::TYPE_CHANGE_NAME;
        $typeChangeAddress = TrademarkInfo::TYPE_CHANGE_ADDRESS;
        $typeChangeDouble = TrademarkInfo::TYPE_CHANGE_NAME_AND_ADDRESS;
        $trademarkTable = $this->trademarkTableService->getTrademarkTable(TYPE_4, $id, [
            TRADEMARK_TABLE_KENRISHA => true,
        ]);
        // Price Base
        $priceServiceChangeAddress = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_ADDRESS_PROCEDURE);
        $priceServiceChangeName = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_NAME_PROCEDURE);
        $costBankTransferBase = $this->mPriceListService->getPriceCommonOfPrecheck(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        // Price Includes Tax
        $priceServiceChangeAddressFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_ADDRESS_PROCEDURE);
        $priceServiceChangeNameFee = $this->mPriceListService->getPriceIncludesTax(MPriceList::CHANGE_PROCEDURE, MPriceList::CHANGE_NAME_PROCEDURE);
        $costBankTransfer = $this->mPriceListService->getPriceIncludesTax(MPriceList::EACH_PAYMENT, MPriceList::BANK_TRANSFER_HANDLING);
        // Setting
        $setting = $this->mPriceListService->getSetting();
        $isChangeAddressFreeTrue = ChangeInfoRegister::IS_CHANGE_ADDRESS_FREE;

        $data = [
            'registerTrademark' => $registerTrademark,
            'prefectures' => $prefectures,
            'nations' => $nations,
            'typeChangeName' => $typeChangeName,
            'typeChangeAddress' => $typeChangeAddress,
            'typeChangeDouble' => $typeChangeDouble,
            'trademarkTable' => $trademarkTable,
            'priceServiceChangeAddress' => $priceServiceChangeAddress,
            'priceServiceChangeName' => $priceServiceChangeName,
            'priceServiceChangeNameFee' => $priceServiceChangeNameFee,
            'priceServiceChangeAddressFee' => $priceServiceChangeAddressFee,
            'costBankTransfer' => number_format($costBankTransfer, 0, '.', ''),
            'setting' => $setting,
            'costBankTransferBase' => $costBankTransferBase,
            'id' => $id,
            'changeInfoRegisterDraft' => $changeInfoRegisterDraft,
            'userInfo' => $userInfo,
            'tradeMark' => $tradeMark,
            'isChangeAddressFreeTrue' => $isChangeAddressFreeTrue
        ];

        return view('user.modules.trademark-info.list_change_address02kenrisha', $data);
    }

    /**
     * Get trademark info
     *
     * @param Request $request
     * @return void
     */
    public function getTradeMarkInfoAjax(Request $request)
    {
        if ($request->ajax()) {
            $trademarkInfoId = $request->trademark_info_id;
            $data = $this->trademarkInfoService->findByCondition(['id' => $trademarkInfoId], ['mNation:id,name', 'mPrefecture:id,name'])->first();

            return response()->json($data);
        }
    }

    /**
     * Create Payment , Payer Info and Change Info Registers
     *
     * @param  mixed $request
     * @param  mixed $id
     * @return void
     */
    public function store(Request $request, $id)
    {
        $trademark = $this->changeInfoRegisterService->createPayment($request->all(), $id);
        // Session::forget(SESSGION_LIST_CHANGE_ADDRESS);
        switch ($trademark['redirect_to']) {
            case REDIRECT_TO_COMMON_PAYMENT:
                return redirect()->route('user.payment.index', ['s' => $trademark['key_session'], 'sft_011' => 11]);
                break;
            case REDIRECT_TO_COMMON_QUOTE:
                return redirect()->route('user.quote', ['id' => $trademark['payment_id']]);
                break;
            case false:
                return redirect()->back()->with('messages', __('messages.common.errors.Common_E025'));
                break;
        }
    }
}
