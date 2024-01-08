<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommonHelper;
use App\Http\Controllers\Controller;
use App\Models\ChangeInfoRegister;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\NoticeDetailBtn;
use App\Services\Common\NoticeService as CommonNoticeService;
use App\Services\ChangeInfoRegisterService;
use App\Services\MatchingResultService;
use App\Services\MNationService;
use App\Services\MPrefectureService;
use App\Services\NoticeDetailService;
use App\Services\TrademarkInfoService;
use App\Services\TrademarkService;
use App\Services\RegisterTrademarkService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChangeAddressController extends Controller
{
    private TrademarkInfoService $trademarkInfoService;
    private TrademarkService $trademarkService;
    private ChangeInfoRegisterService $changeInfoRegisterService;
    private MNationService $mNationService;
    private MPrefectureService $mPrefectureService;
    private CommonNoticeService $commonNoticeService;
    private RegisterTrademarkService $registerTrademarkService;
    private NoticeDetailService $noticeDetailService;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        TrademarkService $trademarkService,
        TrademarkInfoService $trademarkInfoService,
        ChangeInfoRegisterService $changeInfoRegisterService,
        MNationService $mNationService,
        MPrefectureService $mPrefectureService,
        CommonNoticeService $commonNoticeService,
        RegisterTrademarkService $registerTrademarkService,
        NoticeDetailService $noticeDetailService,
        MatchingResultService $matchingResultService
    )
    {
        $this->middleware('permission:extension_period.sendSessionConfirm')->only(['sendSessionConfirm']);
        $this->middleware('permission:extension_period.showConfirmRegistration')->only(['showConfirmRegistration']);
        $this->middleware('permission:extension_period.updateInfo')->only(['updateInfo']);
        $this->middleware('permission:extension_period.showDocumentRegistration')->only(['showDocumentRegistration']);
        $this->middleware('permission:extension_period.saveDataDocument')->only(['saveDataDocument']);
        $this->middleware('permission:change_address.postUpdateChangeAddress')->only(['postUpdateChangeAddress']);


        $this->trademarkInfoService = $trademarkInfoService;
        $this->trademarkService = $trademarkService;
        $this->changeInfoRegisterService = $changeInfoRegisterService;
        $this->mNationService = $mNationService;
        $this->mPrefectureService = $mPrefectureService;
        $this->commonNoticeService = $commonNoticeService;
        $this->registerTrademarkService = $registerTrademarkService;
        $this->noticeDetailService = $noticeDetailService;
        $this->matchingResultService = $matchingResultService;
    }

    /**
     * Show Registration
     *
     * @param  Request $request
     * @param  int $id - trademark_id?change_info_register_id & trademark_info_id
     * @return void
     */
    public function showRegistration(Request $request, $id)
    {
        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(404);
        }

        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();
        $data = $this->getDataChangeAddress($request->all(), $trademark);
        return view('admin.modules.change_address.registration_change_address', compact('data', 'nations', 'prefectures'));
    }

    /**
     * Get Data Change Address
     *
     * @param  array $request
     * @param  model $trademark
     * @return array
     */
    public function getDataChangeAddress($request, $trademark)
    {
        $trademark->load(['appTrademark']);
        $appTrademark = $trademark->appTrademark;
        $trademarkInfo = null;
        if (isset($appTrademark) && isset($request['trademark_info_id']) && $request['trademark_info_id']) {
            $trademarkInfo = $this->trademarkInfoService->findByCondition([
                'id' => $request['trademark_info_id'],
                'target_id' => $appTrademark->id,
            ])->first();

            if ($trademarkInfo) {
                $trademarkInfo->load('mNation', 'mPrefecture');
            } else {
                abort(404);
            }
        }

        $changeInfoRegister = null;
        if (isset($request['change_info_register_id']) && $request['change_info_register_id']) {
            $changeInfoRegister = $this->changeInfoRegisterService->findByCondition([
                'id' => $request['change_info_register_id'],
                'trademark_id' => $trademark->id,
            ])->first();
            if ($changeInfoRegister) {
                $changeInfoRegister->load('nation', 'prefecture', 'trademarkInfo');
            } else {
                abort(404);
            }
        }
        $registerTrademark = null;
        if (isset($request['register_trademark_id']) && $request['register_trademark_id']) {
            $registerTrademark = $this->registerTrademarkService->findByCondition([
               'id' => $request['register_trademark_id'],
               'trademark_id' => $trademark->id,
            ])->first();
            if ($registerTrademark) {
                $registerTrademark->load('trademarkInfo');
            } else {
                abort(404);
            }
        }
        if (isset($trademarkInfo) && $trademarkInfo) {
            $data = [
                'matching_result_id' => $request['matching_result_id'] ?? '',
                'trademark_info_id' => $trademarkInfo->id ?? 0,
                'name' => $trademarkInfo->name ?? '',
                'nation_name' => isset($trademarkInfo) && isset($trademarkInfo->mNation) ? $trademarkInfo->mNation->name : '',
                'nation_id' => $trademarkInfo->m_nation_id ?? 0,
                'prefecture_name' => isset($trademarkInfo->mPrefecture) ? $trademarkInfo->mPrefecture->name : '',
                'm_prefecture_id' => $trademarkInfo->m_prefecture_id ?? '',
                'address_second' => $trademarkInfo->address_second ?? '',
                'address_three' => $trademarkInfo->address_three ?? '',
                'type_acc' => $trademarkInfo->type_acc ?? '',
                'input_name' => $trademarkInfo->name ?? '',
                'input_nation_id' => $trademarkInfo->m_nation_id ?? 0,
                'input_m_prefecture_id' => $trademarkInfo->m_prefecture_id ?? 0,
                'input_address_second' => $trademarkInfo->address_second ?? '',
                'input_address_three' => $trademarkInfo->address_three ?? '',
                'trademarkInfo' => $trademarkInfo,
                'is_updated' => $trademarkInfo->is_updated == IS_UPDATED_TRUE ? true : false,
                'from_page' => A301
            ];
        } elseif (isset($changeInfoRegister) && $changeInfoRegister) {
            $data = [
                'change_info_register_id' => $changeInfoRegister->id ?? 0,
                'name' => isset($changeInfoRegister->trademarkInfo) ? $changeInfoRegister->trademarkInfo->name : '',
                'nation_name' => isset($changeInfoRegister->trademarkInfo) && isset($changeInfoRegister->trademarkInfo->mNation)
                    ? $changeInfoRegister->trademarkInfo->mNation->name : '',
                'nation_id' => isset($changeInfoRegister->trademarkInfo) ? $changeInfoRegister->trademarkInfo->m_nation_id : 0,
                'prefecture_name' => isset($changeInfoRegister->trademarkInfo) && isset($changeInfoRegister->trademarkInfo->mPrefecture) ?
                    $changeInfoRegister->trademarkInfo->mPrefecture->name : '',
                'm_prefecture_id' => isset($changeInfoRegister->trademarkInfo) ? $changeInfoRegister->trademarkInfo->m_prefectures_id : '',
                'address_second' => isset($changeInfoRegister) && isset($changeInfoRegister->trademarkInfo) ? $changeInfoRegister->trademarkInfo->address_second : '',
                'address_three' => isset($changeInfoRegister->trademarkInfo) ? $changeInfoRegister->trademarkInfo->address_three : '',
                'type_acc' => isset($changeInfoRegister->trademarkInfo) ? $changeInfoRegister->trademarkInfo->type_acc : '',
                'input_name' => $changeInfoRegister->name ?? '',
                'input_nation_id' => $changeInfoRegister->m_nation_id ?? 0,
                'input_m_prefecture_id' => $changeInfoRegister->m_prefectures_id ?? 0,
                'input_address_second' => $changeInfoRegister->address_second ?? '',
                'input_address_three' => $changeInfoRegister->address_three ?? '',
                'changeInfoRegister' => $changeInfoRegister,
                'is_updated' => $changeInfoRegister->is_updated == IS_UPDATED_TRUE ? true : false,
                'from_page' => U000LIST_CHANGE_ADDRESS_02
            ];
        } elseif (isset($registerTrademark) && $registerTrademark) {
            $trademarkInfo = $registerTrademark->trademarkInfo;
            $data = [
              'register_trademark_id' => $registerTrademark->id,
              'name' => isset($trademarkInfo) ? $trademarkInfo->name : '',
              'nation_name' => $trademarkInfo->mNation->name,
              'nation_id' => $trademarkInfo->m_nation_id,
              'prefecture_name' => $trademarkInfo->mPrefecture->name,
              'm_prefecture_id' => $trademarkInfo->m_prefecture_id,
              'address_second' => $trademarkInfo->address_second,
              'address_three' => $trademarkInfo->address_three,
              'type_acc' => $registerTrademark->info_type_acc,
              'input_name' => $registerTrademark->trademark_info_name,
              'input_nation_id' => $registerTrademark->trademark_info_nation_id,
              'input_m_prefecture_id' => $registerTrademark->trademark_info_address_first,
              'input_address_second' => $registerTrademark->trademark_info_address_second,
              'input_address_three' => $registerTrademark->trademark_info_address_three,
              'registerTrademark' => $registerTrademark,
              'is_updated' => $registerTrademark->is_updated == IS_UPDATED_TRUE ? true : false,
               'from_page' => U302
            ];
        }
        $data['user_id'] = $trademark->user_id;
        $data['trademark_id'] = $trademark->id;

        $oldAddressInfo = '';
        if ($data['nation_id'] == NATION_JAPAN_ID) {
            $oldAddressInfo .= $data['prefecture_name'] ?? '';
            $oldAddressInfo .= $data['address_second'] ?? '';
            $oldAddressInfo .= $data['address_three'] ?? '';
        } else {
            $oldAddressInfo .= $data['address_three'] ?? '';
        }
        $data['old_address_info'] = $oldAddressInfo;

        return $data;
    }

    /**
     * Update Info
     *
     * @param  mixed $request
     * @return RedirectResponse
     */
    public function sendSessionConfirm(Request $request): RedirectResponse
    {
        $params = $request->all();
        $key = Str::random(11);
        if ($request['trademark_info_id']) {
            $trademarkInfo = $this->trademarkInfoService->find($request['trademark_info_id']);
        } elseif ($request['change_info_register_id']) {
            $changeInfoRegister = $this->changeInfoRegisterService->find($request['change_info_register_id']);
            $changeInfoRegister->load('trademarkInfo');
            $trademarkInfo = $changeInfoRegister->trademarkInfo;
        } elseif ($request['register_trademark_id']) {
            $registerTrademark = $this->registerTrademarkService->find($request['register_trademark_id'])->load('trademarkInfo');
            $trademarkInfo = $registerTrademark->trademarkInfo;
        }
        $dataSession = [
            'type_acc' => $params['type_acc'],
            'name' => $params['name'],
            'm_nation_id' => $params['m_nation_id'],
            'm_prefecture_id' => $params['m_prefecture_id'],
            'address_second' => $params['address_second'],
            'address_three' => $params['address_three'],
            'trademark_info_id' => $request['trademark_info_id'],
            'change_info_register_id' => $request['change_info_register_id'],
            'register_trademark_id' => $request['register_trademark_id'],
            'from_page' => $params['from_page'] ?? '',
            'trademark_id' => $params['trademark_id'] ?? '',
            'matching_result_id' => $params['matching_result_id'] ?? '',
        ];
        Session::put($key, $dataSession);
        if (isset($params['trademark_info_id']) && $params['trademark_info_id']) {
            $route = route('admin.registration.change-address.confirm', [
                'id' => $params['trademark_id'],
                'trademark_info_id' => $params['trademark_info_id'],
                's' => $key,
            ]);
        } elseif (isset($params['change_info_register_id']) && $params['change_info_register_id']) {
            $route = route('admin.registration.change-address.confirm', [
                'id' => $params['trademark_id'],
                'change_info_register_id' => $params['change_info_register_id'],
                's' => $key,
            ]);
        } elseif (isset($params['register_trademark_id']) && $params['register_trademark_id']) {
            $route = route('admin.registration.change-address.confirm', [
                'id' => $params['trademark_id'],
                'register_trademark_id' => $params['register_trademark_id'],
                's' => $key,
            ]);
        } else {
            return redirect()->back();
        }

            return redirect($route);
    }

    /**
     * Show Confirm Registration
     *
     * @param  Request $request
     * @param  int $id
     * @return void
     */
    public function showConfirmRegistration(Request $request, $id)
    {
        $trademark = $this->trademarkService->find($id);
        $nations = $this->mNationService->listNationOptions();
        if (!$trademark) {
            abort(404);
        }
        if (!$request->has('s') && !$request->s) {
            abort(404);
        }
        $dataSession = Session::get($request->s);
        $data = $this->getDataChangeAddress($request->all(), $trademark);

        $newAddessInfo = CommonHelper::getInfoAddress(
            $dataSession['m_nation_id'] ?? 0,
            $dataSession['m_prefecture_id'] ?? 0,
            $dataSession['address_second'] ?? '',
            $dataSession['address_three'] ?? '',
        );
        if (isset($newAddessInfo['nation'])) {
            unset($newAddessInfo['nation']);
        }
        $newAddessInfo = implode('', $newAddessInfo);
        $data['new_address_info'] = $newAddessInfo;

        return view('admin.modules.change_address.confirm_registration_change_address', compact(
            'data',
            'dataSession',
            'nations',
        ));
    }

    /**
     * Update Info
     *
     * @param  Request $request
     * @return void
     */
    public function updateInfo(Request $request)
    {
        $key = Str::random(11);
        $trademarkInfo = $this->trademarkInfoService->changeAddress($request->all(), A700_SHUTSUGANNIN02);
        $params = $trademarkInfo['params'];
        Session::put($key, $params);
        switch ($trademarkInfo['redirect_to']) {
            case A301:
                return redirect()->route('admin.registration.notify', [
                    'id' => $params['trademark_id'],
                    'maching_result_id' => $request->matching_result_id,
            ])->with('message', __('messages.general.update_success'));
            case A000ANKEN_TOP:
                return redirect()->route('admin.application-detail.index', ['id' => $params['trademark_id']])->with('message', __('messages.general.update_success'));
            default:
                $queries = [
                    'id' => $params['trademark_id'],
                    's' => $key,
                ];
                if (isset($params['change_info_register_id'])) {
                    $queries['change_info_register_id'] = $params['change_info_register_id'];
                } elseif (isset($params['register_trademark_id'])) {
                    $queries['register_trademark_id'] = $params['register_trademark_id'];
                }

                return redirect()->route('admin.registration.change-address.document', $queries);
        }
    }

    /**
     * Show Document Registration
     *
     * @param  Request $request
     * @param  int $id
     * @return view
     */
    public function showDocumentRegistration(Request $request, $id)
    {
        $trademark = $this->trademarkService->find($id);
        $nations = $this->mNationService->listNationOptions();
        if (!$trademark) {
            abort(404);
        }
        if (!$request->has('s') && !$request->s) {
            abort(404);
        }
        $dataSession = Session::get($request->s);

        $data = $this->getDataChangeAddress($request->all(), $trademark);

        $newAddessInfo = CommonHelper::getInfoAddress(
            $dataSession['m_nation_id'] ?? 0,
            $dataSession['m_prefecture_id'] ?? 0,
            $dataSession['address_second'] ?? '',
            $dataSession['address_three'] ?? '',
        );
        if (isset($newAddessInfo['nation'])) {
            unset($newAddessInfo['nation']);
        }
        $newAddessInfo = implode('', $newAddessInfo);
        $data['new_address_info'] = $newAddessInfo;

        return view('admin.modules.change_address.document_registration_change_address', compact(
            'dataSession',
            'data',
            'nations'
        ));
    }

    /**
     * Save Data Document
     *
     * @param  Request $request
     * @return void
     */
    public function saveDataDocument(Request $request)
    {
        $this->trademarkInfoService->changeAddress($request->all(), A700_SHUTSUGANNIN03);
        Session::forget($request->s);

        return redirect()->route('admin.application-detail.index', ['id' => $request['trademark_id']])->with('message', __('messages.general.update_success'));
    }

    /**
     * Show Skip Registration
     *
     * @param  Request $request
     * @param  int $id - matching_result_id?register_trademark_id
     * @return void
     */
    public function showSkipRegistration(Request $request, $id)
    {
        $matchingResult = $this->matchingResultService->find($id);
        if (!$matchingResult) {
            abort(CODE_ERROR_404);
        }
        $registerTrademark = $this->registerTrademarkService->find($request->register_trademark_id);
        if (!$registerTrademark) {
            abort(CODE_ERROR_404);
        }
        return view('admin.modules.change_address.skip_registration', compact([
            'matchingResult',
            'registerTrademark',
        ]));
    }

    /**
     * Update change address.
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function updateChangeAddress(Request $request, int $id): View
    {
        if (isset($request->change_info_register_id) && isset($request->register_trademark_id)) {
            abort(404);
        }

        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(404);
        }
        $relation = $trademark->load('appTrademark.trademarkInfo');

        $appTrademark = $relation->appTrademark;
        if (!$appTrademark) {
            abort(404);
        }
        if (isset($request->register_trademark_id)) {
            $checkUrl = route('admin.update.change_address.index', [
                'id' => $trademark->id,
                'register_trademark_id' => $request->register_trademark_id,
                'type' => $request->type
            ]);
        } else {
            $changeInfoRegister = $this->changeInfoRegisterService->find($request->change_info_register_id);
            $checkUrl = route('admin.update.change_address.index', ['id' => $trademark->id, 'change_info_register_id' => $changeInfoRegister->id]);
        }

        $data = $this->getDataChangeAddressOfKenrisha($request->all(), $trademark);

        $nations = $this->mNationService->findByCondition([''])->get();
        $prefectures = $this->mPrefectureService->findByCondition([''])->get();

        // Url Back
        $urlBackDefault = route('admin.home');
        $backUrl = CommonHelper::setBackUrl($urlBackDefault, $checkUrl);
            return view('admin.modules.change_address.update_change_address', compact(
                'nations',
                'backUrl',
                'trademark',
                'prefectures',
                'data'
            ));
    }

    /**
     * Get Data Change Address
     *
     * @param  array $request
     * @param  model $trademark
     * @return array
     */
    public function getDataChangeAddressOfKenrisha($request, $trademark)
    {
        $trademark->load('appTrademark');
        $appTrademark = $trademark->appTrademark;
        $changeInfoRegister = null;
        if (isset($request['change_info_register_id']) && $request['change_info_register_id']) {
            $changeInfoRegister = $this->changeInfoRegisterService->findByCondition([
                'id' => $request['change_info_register_id'],
                'trademark_id' => $trademark->id,
            ])->first();
            if ($changeInfoRegister) {
                $changeInfoRegister->load('nation', 'prefecture', 'registerTrademark');
            } else {
                abort(404);
            }
        }
        $registerTrademark = null;
        if (isset($request['register_trademark_id']) && $request['register_trademark_id']) {
            $registerTrademark = $this->registerTrademarkService->findByCondition([
                'id' => $request['register_trademark_id'],
                'trademark_id' => $trademark->id,
            ])->first();
            if ($registerTrademark) {
                $registerTrademark->load('trademarkInfo');
            } else {
                abort(404);
            }
        }
        if (isset($changeInfoRegister) && $changeInfoRegister) {
            $registerTrademark = $changeInfoRegister->registerTrademark;
            $data = [
                'change_info_register_id' => $changeInfoRegister->id,
                'trademark_info_name' => $registerTrademark->trademark_info_name,
                'trademark_info_nation_name' => $registerTrademark->mNationTrademarkInfo->name,
                'trademark_info_nation_id' => $registerTrademark->trademark_info_nation_id,
                'trademark_info_address_first' => $registerTrademark->trademark_info_address_first,
                'trademark_info_address_first_name' => $registerTrademark->prefecture->name,
                'trademark_info_address_second' => $registerTrademark->trademark_info_address_second,
                'trademark_info_address_three' => $registerTrademark->trademark_info_address_three,
                'type_acc' => $changeInfoRegister->type_acc,
                'name' => $changeInfoRegister->name,
                'm_nation_id' => $changeInfoRegister->m_nation_id,
                'm_nation_name' => $changeInfoRegister->nation->name,
                'm_prefectures_id' => $changeInfoRegister->m_prefectures_id,
                'm_prefectures_name' => $changeInfoRegister->prefecture->name,
                'address_second' => $changeInfoRegister->address_second,
                'address_three' => $changeInfoRegister->address_three,
                'changeInfoRegister' => $changeInfoRegister
            ];
        } elseif (isset($registerTrademark) && $registerTrademark) {
            $registerTrademarkChoice = $this->registerTrademarkService->find($registerTrademark->id_register_trademark_choice);
            $data = [
                'register_trademark_id' => $registerTrademark->id,
                'type' => $request['type'],
                'trademark_info_name' => $registerTrademarkChoice->trademark_info_name,
                'trademark_info_nation_name' => $registerTrademarkChoice->mNationTrademarkInfo->name,
                'trademark_info_nation_id' => $registerTrademarkChoice->trademark_info_nation_id,
                'trademark_info_address_first_name' => $registerTrademarkChoice->prefecture->name,
                'trademark_info_address_first' => $registerTrademarkChoice->trademark_info_address_first,
                'trademark_info_address_second' => $registerTrademarkChoice->trademark_info_address_second,
                'trademark_info_address_three' => $registerTrademarkChoice->trademark_info_address_three,
                'type_acc' => $registerTrademark->info_type_acc,
                'name' => $registerTrademark->trademark_info_name,
                'm_nation_id' => $registerTrademark->trademark_info_nation_id,
                'm_nation_name' => $registerTrademark->mNationTrademarkInfo->name,
                'm_prefectures_id' => $registerTrademark->trademark_info_address_first,
                'm_prefectures_name' => $registerTrademark->trademark_info_nation_id == NATION_JAPAN_ID ? $registerTrademark->prefecture->name : '',
                'address_second' => $registerTrademark->trademark_info_address_second,
                'address_three' => $registerTrademark->trademark_info_address_three,
                'registerTrademark' => $registerTrademark
            ];
        }
        $data['user_id'] = $trademark->user_id;
        $data['trademark_id'] = $trademark->id;
        if (isset($request['s'])) {
            $dataSession = Session::get($request['s']);

                $data['name'] = $dataSession['name'];
                $data['m_nation_id'] = $dataSession['m_nation_id'];
                $data['m_prefectures_id'] = $dataSession['m_prefecture_id'];
                $data['address_second'] = $dataSession['address_second'];
                $data['address_three'] = $dataSession['address_three'];
        }
        return $data;
    }

    /**
     * Post update change address.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function postUpdateChangeAddress(Request $request, int $id): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $trademark = $this->trademarkService->find($id);
            if (!$trademark) {
                abort(404);
            }

            $relation = $trademark->load('appTrademark.trademarkInfo');

            $appTrademark = $relation->appTrademark;
            if (!$appTrademark) {
                abort(404);
            }
            $changeInfoRegister = null;
            $registerTrademark = null;
            if (!empty($request->change_info_register_id)) {
                $changeInfoRegister = $this->changeInfoRegisterService->findByCondition([
                   'id' => $request->change_info_register_id,
                   'trademark_id' => $id,
                ])->first();
            } else {
                $registerTrademark = $this->registerTrademarkService->findByCondition([
                    'id' => $request->register_trademark_id,
                    'trademark_id' => $id,
                ])->first();
            }

            $data = [
                'type_acc' => $request->type_acc ?? (isset($changeInfoRegister)
                        ? $changeInfoRegister->type_acc : $registerTrademark->info_type_acc),
                'name' => $request->name ?? (isset($changeInfoRegister)
                        ? $changeInfoRegister->name : $registerTrademark->trademark_info_name),
                'm_nation_id' => $request->m_nation_id ?? (isset($changeInfoRegister)
                        ? $changeInfoRegister->m_nation_id : $registerTrademark->trademark_info_nation_id),
                'm_prefecture_id' => $request->m_prefecture_id ?? (isset($changeInfoRegister)
                        ? $changeInfoRegister->m_prefectures_id : $registerTrademark->trademark_info_address_first),
                'address_second' => $request->address_second ?? (isset($changeInfoRegister)
                        ? $changeInfoRegister->address_second : $registerTrademark->trademark_info_address_second),
                'address_three' => $request->address_three ?? (isset($changeInfoRegister)
                        ? $changeInfoRegister->address_three : $registerTrademark->trademark_info_address_three),
            ];

            $key = Str::random(11);
            $dataSession = $data;
            $params = [
                'id' => $trademark->id,
                's' => $key
            ];
            if (!empty($request->change_info_register_id)) {
                $params['change_info_register_id'] = $changeInfoRegister->id;
            } else {
                $params['register_trademark_id'] = $registerTrademark->id;
                $params['type'] = $request->type;
            }
            Session::put($key, $dataSession);
            DB::commit();

            return redirect()->route('admin.update.change_address.confirm', $params);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
            return redirect()->back();
        }
    }

    /**
     * Update change address confirm.
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function updateChangeAddressConfirm(Request $request, int $id): View
    {
        if (isset($request->change_info_register_id) && isset($request->register_trademark_id)) {
            abort(404);
        }
        $key = $request->s;
        if (empty($request->s)) {
            abort(404);
        }
        $dataSession = Session::get($key);
        if (!$dataSession) {
            abort(404);
        }

        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(404);
        }

        $nationSession = null;
        $prefectureSession = null;
        $params = [
            'id' => $trademark->id,
            's' => $key
        ];
        if ($request->change_info_register_id) {
            $params['change_info_register_id'] = $request->change_info_register_id;
        } else {
            $params['register_trademark_id'] = $request->register_trademark_id;
            $params['type'] = $request->type;
        }

        if (isset($dataSession['m_nation_id'])) {
            $nationSession = $this->mNationService->find($dataSession['m_nation_id'])->name;
        }
        if (isset($dataSession['m_prefecture_id'])) {
            $prefectureSession = $this->mPrefectureService->find($dataSession['m_prefecture_id'])->name;
        }
        // Url Back
        $urlBackDefault = route('admin.home');
        $checkUrl = route('admin.update.change_address.confirm', $params);
        $backUrl = route('admin.update.change_address.index', $params);
        $redirectUrl = route('admin.update.change_address.document', $params);

        $data = $this->getDataChangeAddressOfKenrisha($request->all(), $trademark);

        return view('admin.modules.change_address.update_change_address_confirm', compact(
            'key',
            'backUrl',
            'trademark',
            'dataSession',
            'nationSession',
            'prefectureSession',
            'data',
            'redirectUrl',
        ));
    }

    /**
     * Update change address document.
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function updateChangeAddressDocument(Request $request, int $id): View
    {
        if (isset($request->change_info_register_id) && isset($request->register_trademark_id)) {
            abort(404);
        }
        $key = $request->s;
        if (empty($request->s)) {
            abort(404);
        }

        $dataSession = Session::get($key);
        if (!$dataSession) {
            abort(404);
        }

        $trademark = $this->trademarkService->find($id);
        if (!$trademark) {
            abort(404);
        }

        $relation = $trademark->load('appTrademark.trademarkInfo');

        $nationSession = null;
        $prefectureSession = null;
        $params = [
            'id' => $trademark->id,
            's' => $key
        ];
        if ($request->change_info_register_id) {
            $params['change_info_register_id'] = $request->change_info_register_id;
        } else {
            $params['register_trademark_id'] = $request->register_trademark_id;
            $params['type'] = $request->type;
        }

        if (isset($dataSession['m_nation_id'])) {
            $nationSession = $this->mNationService->find($dataSession['m_nation_id'])->name;
        }
        if (isset($dataSession['m_prefecture_id'])) {
            $prefectureSession = $this->mPrefectureService->find($dataSession['m_prefecture_id'])->name;
        }

        // Url Back
        $urlBackDefault = route('admin.home');
        $checkUrl = route('admin.update.change_address.confirm', $params);
        $backUrl = CommonHelper::setBackUrl($urlBackDefault, $checkUrl);
        $urlPost = route('admin.update.change_address.document.post', $params);
        $data = $this->getDataChangeAddressOfKenrisha($request->all(), $trademark);

        return view('admin.modules.change_address.update_change_address_document', compact(
            'key',
            'trademark',
            'dataSession',
            'nationSession',
            'prefectureSession',
            'urlPost',
            'data'
        ));
    }

    /**
     * Post update change address.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function postUpdateChangeAddressDocument(Request $request, int $id): RedirectResponse
    {
        try {
            DB::beginTransaction();
            if (isset($request->change_info_register_id) && isset($request->register_trademark_id)) {
                abort(404);
            }

            $key = $request->s;
            if (empty($request->s)) {
                abort(404);
            }

            $dataSession = Session::get($key);
            if (!$dataSession) {
                abort(404);
            }

            $trademark = $this->trademarkService->find($id);
            if (!$trademark) {
                abort(404);
            }

            $relation = $trademark->load('appTrademark.trademarkInfo');

            $appTrademark = $relation->appTrademark;
            if (!$appTrademark) {
                abort(404);
            }


            $adminId = auth()->user()->id;
            if (isset($request->register_trademark_id)) {
                if (empty($request->type)) {
                    redirect()->back();
                }
                $registerTrademark = $this->registerTrademarkService->findByCondition([
                   'id' => $request->register_trademark_id,
                   'trademark_id' => $id,
                ]);
                if ($request->type == TYPE_1) {
                    $contentAtop = '事務担当　後期納付手続き：登録名義人DB変更';
                    $contentAnkentop1 = '事務担当　後期納付手続き：登録名義人DB変更';
                } elseif ($request->type == TYPE_2) {
                    $contentAtop = '事務担当　更新手続き：登録名義人DB変更';
                    $contentAnkentop1 = '事務担当　更新手続き：登録名義人DB変更';
                }
                $targetPage = route('admin.update.change_address.document', [
                    'id' => $trademark->id,
                    'register_trademark_id' => $request->register_trademark_id,
                    'type' => $request->type
                ]);
                $dataUpdate = [
                    'trademark_info_name' => $dataSession['name'],
                    'trademark_info_nation_id' => $dataSession['m_nation_id'],
                    'trademark_info_address_first' => $dataSession['m_nation_id'] == 1 ? $dataSession['m_prefecture_id'] : null,
                    'trademark_info_address_second' => $dataSession['m_nation_id'] == 1 ? $dataSession['address_second'] : null,
                    'trademark_info_address_three' => $dataSession['address_three'],
                    'is_updated' => ChangeInfoRegister::IS_UPDATED_TRUE
                ];
                $registerTrademark->update($dataUpdate);

                $stepBeforeNotice = $this->noticeDetailService->findByCondition([
                    'raw' => 'type_acc <> ' . NoticeDetail::TYPE_USER,
                    'completion_date' => null,
                ])->whereHas('notice', function ($query) use ($trademark) {
                    $query->where('trademark_id', $trademark->id)
                          ->where('user_id', $trademark->user_id)
                        ->whereIn('flow', [Notice::FLOW_RENEWAL, Notice::FLOW_REGISTRATION_5_YEARS]);
                })->get();

                $stepBeforeNotice->map(function ($item) {
                    $item->update([
                        'completion_date' => Carbon::now(),
                    ]);
                });
            } elseif (isset($request->change_info_register_id)) {
                $changeRegisterInfo = $this->changeInfoRegisterService->findByCondition([
                    'id' => $request->change_info_register_id,
                    'trademark_id' => $id,
                ])->first();
                $changeRegisterInfo->update(['is_updated' => ChangeInfoRegister::IS_UPDATED_TRUE]);
                $registerTrademark = $changeRegisterInfo->registerTrademark;
                $dataUpdate = [
                    'trademark_info_name' => $dataSession['name'],
                    'trademark_info_nation_id' => $dataSession['m_nation_id'],
                    'trademark_info_address_first' => $dataSession['m_prefecture_id'],
                    'trademark_info_address_second' => $dataSession['address_second'],
                    'trademark_info_address_three' => $dataSession['address_three'],
                    'is_updated' => ChangeInfoRegister::IS_UPDATED_TRUE
                ];
                $registerTrademark->update($dataUpdate);
                $contentAtop = '事務担当　登録査定：登録名義人DB変更';
                $contentAnkentop1 = '事務担当　登録査定：登録名義人DB変更';
                $targetPage = route('admin.update.change_address.document', [
                    'id' => $trademark->id,
                    'change_info_register_id' => $request->change_info_register_id,
                ]);

                $stepBeforeNotice = $this->noticeDetailService->findByCondition([
                    'raw' => 'type_acc <> ' . NoticeDetail::TYPE_USER,
                    'completion_date' => null,
                ])->whereHas('notice', function ($query) use ($trademark) {
                    $query->whereIn('flow', [Notice::FLOW_RENEWAL, Notice::FLOW_CHANGE_INFO, Notice::FLOW_REGISTRATION_5_YEARS])
                        ->where('trademark_id', $trademark->id)
                        ->where('user_id', $trademark->user_id);
                })->get();
                $stepBeforeNotice->map(function ($item) {
                    $item->update([
                        'completion_date' => Carbon::now(),
                    ]);
                });
            }

            $redirectPage = route('admin.application-detail.index', ['id' => $trademark->id]);

            $notice = [
                'trademark_id' => $trademark->id,
                'trademark_info_id' => null,
                'user_id' => $trademark->user_id,
                'flow' => Notice::FLOW_RENEWAL,
            ];

            $noticeDetails = [
                [
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => $contentAtop,
                ],
                [
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => $contentAnkentop1,
                    'attribute' => '所内処理',
                    'buttons' => [
                        [
                            'btn_type' => NoticeDetailBtn::BTN_PDF_UPLOAD,
                            'from_page' => A700KENRISHA03,
                        ],
                    ],
                ],
            ];

            $this->commonNoticeService->sendNotice([
                'notices' => $notice,
                'notice_details' => $noticeDetails,
            ]);

            DB::commit();

            CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.update_success'));
            return redirect($redirectPage);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            CommonHelper::setMessage($request, MESSAGE_ERROR, __('messages.error'));
            return redirect()->back();
        }
    }

    /**
     * Update change address skip.
     *
     * @param Request $request
     * @param int $id
     * @return View
     */
    public function updateChangeAddressSkip(Request $request, int $id): View
    {
        $registerTrademark = $this->registerTrademarkService->find($id);
        if (!$registerTrademark) {
            abort(404);
        }
        $trademark = $registerTrademark->trademark;
        if (!$trademark) {
            abort(404);
        }
        $redirectPage = route('admin.update.change_address.skip', ['id' => $registerTrademark->id]);
        if (str_contains($registerTrademark->type, U402)) {
            $redirectPage = route('admin.update.document.modification.product.detail', ['id' => $registerTrademark->id]);
        } elseif (str_contains($registerTrademark->type, U302_402)) {
            $redirectPage = route('admin.registration.procedure-latter-period.document', ['id' => $registerTrademark->id]);
        }
        return view('admin.modules.change_address.update_change_address_skip', compact(
            'redirectPage'
        ));
    }
}
