<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use App\Repositories\NoticeDetailRepository;
use App\Services\SettingService;
use Illuminate\Http\Request;
use App\Models\MyFolder;
use App\Models\Trademark;
use App\Models\AppTrademark;
use App\Models\AppTrademarkProd;
use App\Models\AppTrademarkProdCmt;
use App\Models\MyFolderProduct;
use App\Models\NoticeDetail;
use App\Models\PayerInfo;
use App\Models\Precheck;
use App\Models\PrecheckComment;
use App\Models\PrecheckKeepData;
use App\Models\PrecheckKeepDataProd;
use App\Models\PrecheckKeepDataProdResult;
use App\Models\SFTComment;
use App\Models\SFTContentProduct;
use App\Models\SFTSuitableProduct;
use App\Models\TrademarkInfo;
use Illuminate\Contracts\View\View;
use App\Services\NoticeService;
use App\Services\MyFolderService;
use App\Services\NoticeDetailService;
use App\Services\PrecheckService;
use App\Services\TrademarkService;
use App\Services\AppTrademarkService;
use App\Services\SupportFirstTimeService;
use App\Services\SFTContentProductService;
use App\Services\SFTSuitableProductService;
use App\Services\PrecheckProductService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class DashboardController extends Controller
{
    protected NoticeService $noticeService;
    protected MyFolderService $myFolderService;
    protected TrademarkService $trademarkService;
    protected AppTrademarkService $appTrademarkService;
    protected PrecheckService $precheckService;
    protected SupportFirstTimeService $supportFirstTimeService;
    protected SFTContentProductService $sftContentProductService;
    protected SFTSuitableProductService $sftSuitableProductService;
    protected PrecheckProductService $precheckProductService;
    protected NoticeDetailService $noticeDetailService;
    protected SettingService $settingService;
    protected NoticeDetailRepository $noticeDetailRepository;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        NoticeService $noticeService,
        NoticeDetailService $noticeDetailService,
        MyFolderService $myFolderService,
        TrademarkService $trademarkService,
        PrecheckService $precheckService,
        SupportFirstTimeService $supportFirstTimeService,
        SFTContentProductService $sftContentProductService,
        SFTSuitableProductService $sftSuitableProductService,
        PrecheckProductService $precheckProductService,
        AppTrademarkService $appTrademarkService,
        SettingService $settingService,
        NoticeDetailRepository $noticeDetailRepository
    )
    {
        $this->noticeService = $noticeService;
        $this->noticeDetailService = $noticeDetailService;
        $this->myFolderService = $myFolderService;
        $this->precheckService = $precheckService;
        $this->trademarkService = $trademarkService;
        $this->appTrademarkService = $appTrademarkService;
        $this->supportFirstTimeService = $supportFirstTimeService;
        $this->sftContentProductService = $sftContentProductService;
        $this->sftSuitableProductService = $sftSuitableProductService;
        $this->precheckProductService = $precheckProductService;
        $this->settingService = $settingService;
        $this->noticeDetailRepository = $noticeDetailRepository;
    }
    /**
     * Show top - redirect after login
     *
     * @return View
     */
    public function showTop(Request $request): View
    {
        $user = Auth::guard('web')->user();
        // Get to do list.
        $toDoList = $this->noticeDetailService->getNoticeDetails([
            'type_acc' => NoticeDetail::TYPE_USER,
            'target_id' => $user->id,
            'is_answer' => NoticeDetail::IS_NOT_ANSWER,
        ], [
            'notice.trademark.comparisonTrademarkResult',
            'notice.trademark.registerTrademark.registerTrademarkRenewals',
        ])
        ->paginate(PAGE_LIMIT_10);

        $top10NoticeDetails = $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_USER,
            'target_id' => $user->id,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
            'type_page' => NoticeDetail::TYPE_PAGE_TOP,
        ])->with('notice.trademark')->orderBy('created_at', SORT_BY_DESC)->orderBy('id', SORT_BY_DESC)->paginate(PAGE_LIMIT_10);
        $top10NoticeDetails->map(function ($item) {
            if ($item->notice) {
                $notice = $item->notice;
                if ($notice->trademark) {
                    $trademark = $notice->trademark;
                    $item->trademark_number = $trademark->trademark_number;
                }
            }
        });
        $appTrademarksApply = $this->appTrademarkService->getAppTrademarkWaitingApply([
            'status' => AppTrademark::NOT_APPLY,
            'type_pages' => [
                AppTrademark::PAGE_TYPE_U011B,
                AppTrademark::PAGE_TYPE_U011B_31,
                AppTrademark::PAGE_TYPE_U021B,
                AppTrademark::PAGE_TYPE_U021B_31,
                AppTrademark::PAGE_TYPE_U031EDIT,
                AppTrademark::PAGE_TYPE_U031EDIT_WITH_NUMBER,
            ],
        ])->paginate(PAGE_LIMIT_10);

        $trademarksNotApply = $this->appTrademarkService->getAppTrademarkNotApply([
            'status' => AppTrademark::NOT_APPLY,
            'type_pages' => [
                AppTrademark::PAGE_TYPE_U031,
                AppTrademark::PAGE_TYPE_U031EDIT,
                AppTrademark::PAGE_TYPE_U031B,
                AppTrademark::PAGE_TYPE_U031C,
                AppTrademark::PAGE_TYPE_U031D,
            ],
            'sort_type' => $request->sort_type ?? SORT_BY_DESC
        ]);

        $trademarksNotApply = new LengthAwarePaginator($trademarksNotApply->take(PAGE_LIMIT_10), $trademarksNotApply->count(), PAGE_LIMIT_10, 1);

        $myFolders = $this->myFolderService->findByCondition([
            'user_id' => $user->id,
        ], ['myFolderProduct.mProduct'])->paginate(PAGE_LIMIT_3);
        $setting = $this->settingService->findByCondition(['key' => A000NEWS_EDIT])->first();
        return view(
            'user.modules.top.u000top',
            compact(
                'toDoList',
                'myFolders',
                'top10NoticeDetails',
                'appTrademarksApply',
                'trademarksNotApply',
                'setting'
            )
        );
    }

    /**
     * Show all notice
     */
    public function showAllNotice(Request $request)
    {
        try {
            $user = Auth::guard('web')->user();
            $top10NoticeDetails = $this->noticeDetailService->findByCondition([
                'type_acc' => NoticeDetail::TYPE_USER,
                'target_id' => $user->id,
                'type_notify' => NoticeDetail::TYPE_NOTIFY_DEFAULT,
                'type_page' => NoticeDetail::TYPE_PAGE_TOP,
            ])->with('notice.trademark')->orderBy('created_at', SORT_BY_DESC)->get();
            $top10NoticeDetails->map(function ($item) {
                if ($item->notice) {
                    $notice = $item->notice;
                    if ($notice->trademark) {
                        $trademark = $notice->trademark;
                        $item->trademark_number = $trademark->trademark_number;
                    }
                }
            });
            $html = '<div id="noticeListAll" style="height: 500px;overflow-y: auto;">';
            foreach ($top10NoticeDetails as $noticeDetail) {
                $html = $html . '<div style="margin-bottom: 10px">
                <span style="margin-right: 40px">' . (isset($noticeDetail->created_at) ? Carbon::parse($noticeDetail->created_at)->format('Y/m/d') : '') . '</span>
                <span style="margin-right: 40px">' .  ($noticeDetail->trademark_number ?? '') . '</span>
                <span style="margin-right: 40px">' . ($noticeDetail->content ?? '') . '</span>
                </div>';
            }

            $html = $html. '</div>';

            return response()->json(['html' => $html], 200);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json(['html' => ''], 400);
        }
    }

    /**
     * Delete my folder and relation table.
     *
     * @param Request $request
     * @param int $id
     */
    public function deleteMyFolder(Request $request, int $id)
    {
        $user = Auth::guard('web')->user();
        $myFolder = $this->myFolderService->findByCondition(['id' => $id, 'user_id' => $user->id]);
        if (!$myFolder) {
            throw new \Exception('Not Found My Folder.');
        }
        try {
            DB::beginTransaction();
            $myFolderProds = MyFolderProduct::where('my_folder_id', $id)->get();
            foreach ($myFolderProds as $myFolderProd) {
                $myFolderProd->delete();
            }

            $myFolder->delete();
            DB::commit();

            return response()->json(['data' => ['success' => true]], 200);
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();

            return response()->json(['data' => ['error' => true]], 400);
        }
    }

    /**
     * Delete anken
     *
     * @param int $id
     */
    public function deleteAnken(Request $request, int $id)
    {
        $user = Auth::guard('web')->user();
        try {
            DB::beginTransaction();
            $trademark = $this->trademarkService->findByCondition(['user_id' => $user->id, 'id' => $id])->first();
            $sft = $this->supportFirstTimeService->findByCondition(['trademark_id' => $id])->first();
            if ($sft) {
                $sftComments = SFTComment::where('support_first_time_id', $sft->id)->get();
                foreach ($sftComments as $item) {
                    $item->delete();
                }
                $sftContentProduct = SFTContentProduct::where('support_first_time_id', $sft->id)->get();
                foreach ($sftContentProduct as $item) {
                    $item->delete();
                }

                $sftSuitableProduct = SFTSuitableProduct::where('support_first_time_id', $sft->id)->get();
                foreach ($sftSuitableProduct as $item) {
                    $item->delete();
                }
            }

            $prechecks = Precheck::where('trademark_id', $id)->get();
            if ($prechecks->count()) {
                foreach ($prechecks as $precheck) {
                    $precheckKeepDatas = PrecheckKeepData::where('precheck_id', $precheck->id)->get();
                    foreach ($precheckKeepDatas as $key => $precheckKeepData) {
                        $precheckComments = PrecheckComment::where('precheck_id', $precheck->id)->get();
                        foreach ($precheckComments as $item) {
                            $item->delete();
                        }
                        $precheckKeepDataProds = PrecheckKeepDataProd::where('precheck_keep_data_id', $precheckKeepData->id)->get();
                        foreach ($precheckKeepDataProds as $item) {
                            $precheckKeepDataProdResults = PrecheckKeepDataProdResult::where('precheck_keep_data_prod_id', $item->id)->get();
                            foreach ($precheckKeepDataProdResults as $item) {
                                $item->delete();
                            }
                            $item->delete();
                        }
                        $precheckKeepData->delete();
                    }
                    $payerInfos = PayerInfo::where('target_id', $precheck->id)->where('type', 4)->get();
                    foreach ($payerInfos as $key => $item) {
                        $item->delete();
                    }
                    $precheck->delete();
                }
            }
            if ($sft) {
                $payerInfos = PayerInfo::where('target_id', $sft->id)->whereIn('type', [1, 2])->get();
                foreach ($payerInfos as $key => $item) {
                    $item->delete();
                }
            }
            $appTrademark = $this->appTrademarkService->findByCondition(['trademark_id' => $id])->first();
            if ($appTrademark) {
                $appTrademarkProds = AppTrademarkProd::where('app_trademark_id', $appTrademark->id)->get();
                foreach ($appTrademarkProds as $item) {
                    $item->delete();
                }
                $appTrademarkProdCmts = AppTrademarkProdCmt::where('app_trademark_id', $appTrademark->id)->get();
                foreach ($appTrademarkProdCmts as $item) {
                    $item->delete();
                }
                $appTrademark->delete();
            }

            $trademarkInfos = TrademarkInfo::where(['target_id' => $id, 'type' => TrademarkInfo::TYPE_TRADEMARK])->get();
            foreach ($trademarkInfos as $key => $item) {
                $item->delete();
            }

            if ($sft) {
                $sft->delete();
            }
            if ($trademark) {
                $trademark->delete();
            }
            DB::commit();

            return response()->json(['data' => ['success' => true]], 200);
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollBack();

            return response()->json(['data' => ['error' => true]], 400);
        }
    }

    /**
     * Return route with type page.
     *
     * @param Request $request
     */
    public function getRedirectRouteWithType(Request $request)
    {
        try {
            $user = Auth::user();
            $trademark = Trademark::find($request->trademark_id);

            if (!$trademark || $user->id != $trademark->user_id) {
                throw new \Exception('');
            }

            $routeRedirect = '';
            if ($request->has('type_page')
                && $request->has('trademark_id')
                && $request->trademark_id
            ) {
                switch ((int) $request->type_page) {
                    case TYPE_PAGE_SFT:
                        $sft = $this->supportFirstTimeService->findByCondition(['trademark_id' => $request->trademark_id])->first();
                        if (!$sft) {
                            abort(404);
                        }
                        $routeRedirect = route('user.support.first.time.u011b', ['id' => $sft->id]);
                        break;
                    case TYPE_PAGE_SFT_AMS:
                        $sft = $this->supportFirstTimeService->findByCondition(['trademark_id' => $request->trademark_id])->first();
                        if (!$sft) {
                            abort(404);
                        }
                        $routeRedirect = route('user.sft.proposal-ams', ['id' => $sft->id]);
                        break;
                    case TYPE_PAGE_PRECHECK:
                        $precheck = $this->precheckService->findByCondition(['trademark_id' => $request->trademark_id])->first();
                        if (!$precheck) {
                            abort(404);
                        }
                        $routeRedirect = route('user.precheck.application-trademark', ['id' => $request->trademark_id]);
                        break;
                    case TYPE_PAGE_PRECHECK_AMS:
                        $precheck = $this->precheckService->findByCondition(['trademark_id' => $request->trademark_id])->first();
                        if (!$precheck) {
                            abort(404);
                        }
                        $routeRedirect = route('user.precheck.application-trademark-v2', ['id' => $request->trademark_id]);
                        break;
                    case TYPE_PAGE_APP_TRADEMARK_U031:
                        $routeRedirect = route('user.apply-trademark-register', ['id' => $request->trademark_id, 'from_page' => U000TOP]);
                        break;
                    case TYPE_PAGE_APP_TRADEMARK_U031EDIT:
                        $routeRedirect = route('user.apply-trademark-free-input', ['id' => $request->trademark_id]);
                        break;
                    case TYPE_PAGE_APP_TRADEMARK_U031B:
                        $routeRedirect = route('user.register-apply-trademark-after-search', ['id' => $request->trademark_id]);
                        break;
                    case TYPE_PAGE_APP_TRADEMARK_U031C:
                        $routeRedirect = route('user.application-detail.index', ['id' => $request->trademark_id]);
                        break;
                    case TYPE_PAGE_APP_TRADEMARK_NUMBER:
                        $routeRedirect = route('user.precheck.apply-trademark-with-number', ['id' => $request->trademark_id]);
                        break;
                    case TYPE_PAGE_APP_TRADEMARK_U031D:
                        $routeRedirect = route('user.register-trademark-without-number');
                        break;
                    default:
                        $secret = Str::random(11);
                        $trademark = $this->trademarkService->find($request->trademark_id);
                        $precheck = $this->precheckService->findByCondition(['trademark_id' => $request->trademark_id])->get()->last();
                        if ($precheck) {
                            $productIds = $this->precheckProductService->findByCondition(['precheck_id' => $precheck->id])->get()->pluck('m_product_id')->toArray();
                            Session::put($secret, [
                                'from_page' => U000TOP,
                                'data' => [
                                    'm_product_ids' => $productIds,
                                ]
                            ]);

                            $routeRedirect = route('user.precheck.register-precheck', ['id' => $trademark->id, 's' => $secret]);
                        } else {
                            $sft = $this->supportFirstTimeService->findByCondition(['trademark_id' => $request->trademark_id])->first();
                            $sftContentProds = $this->sftContentProductService->findByCondition(['support_first_time_id' => $sft->id])
                                ->pluck('name')
                                ->toArray();

                            Session::put($secret, [
                                'sft' => $sft,
                                'trademark' => $trademark,
                                'product_names' => $sftContentProds,
                            ]);
                            $routeRedirect = route('user.sft.index', ['s' => $secret]);
                        }

                        break;
                }

                return response()->json(['redirect_to' => $routeRedirect], 200);
            }

            return response()->json(['redirect_to' => null], 400);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json(['redirect_to' => null], 400);
        }
    }

    /**
     * Show more my folder
     *
     * @param Request $request.
     * @return Response
     */
    public function showAllMyFolderAjax(Request $request)
    {
        try {
            $user = Auth::guard('web')->user();

            $myFolders = $this->myFolderService->findByCondition([
                'user_id' => $user->id,
            ], ['myFolderProduct.mProduct']);

            $myFolders = $myFolders->skip(PAGE_LIMIT_3)->limit(PAGE_LIMIT_5)->get();

            $html = $this->serializeMyFolderHTML($myFolders);

            return response()->json(['html' => $html], 200);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json(['html' => ''], 400);
        }
    }

    /**
     * Show more todo list;
     *
     * @param Request $request.
     * @return Response
     */
    public function showAllToDoAjax(Request $request)
    {
        try {
            $user = Auth::guard('web')->user();

            $toDoList = $this->noticeDetailService->getNoticeDetails(
                array_merge($request->all(), [
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_id' => $user->id,
                    'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                ]),
                [
                    'notice.trademark.comparisonTrademarkResult',
                    'notice.trademark.registerTrademark.registerTrademarkRenewals',
                ]
            );

            $countMax = $toDoList->count();
            $data = $toDoList->skip(PAGE_LIMIT_10)->limit($countMax)->get();

            $html = $this->serializeTodoListHTML($data);

            return response()->json(['html' => $html], 200);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json(['html' => ''], 400);
        }
    }

    /**
     * Show more App trademark not apply;
     *
     * @param Request $request.
     * @return Response
     */
    public function showALlAppTrademarkNotApply(Request $request)
    {
        try {
            $trademarksNotApply = $this->appTrademarkService->getAppTrademarkNotApply([
                'status' => AppTrademark::NOT_APPLY,
                'type_pages' => [
                    AppTrademark::PAGE_TYPE_U031,
                    AppTrademark::PAGE_TYPE_U031B,
                    AppTrademark::PAGE_TYPE_U031C,
                    AppTrademark::PAGE_TYPE_U031D,
                ],
                'sort_type' => $request->sort_type ?? SORT_BY_DESC
            ]);

            $data = null;
            if ($request->has('show_all')) {
                if (!(int) $request->show_all) {
                    $data = $trademarksNotApply->take(PAGE_LIMIT_10);
                } else {
                    $data = $trademarksNotApply;
                }
            } else {
                $data = $trademarksNotApply->skip(PAGE_LIMIT_10);
            }

            $html = $this->serializeATMNotApplyHTML($data);

            return response()->json(['html' => $html], 200);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json(['html' => ''], 400);
        }
    }

    /**
     * Render product name to html
     *
     * @param int $id
     */
    public function showAllProdNameTrademark(int $id)
    {
        try {
            $trademark = $this->trademarkService->find($id);
            $products = $trademark->getProductsWithRelation();
            $html = '';
            foreach ($products as $key => $text) {
                $html = $html . '<div>' . $text . '</div>';
            }

            return response()->json(['html' => $html], 200);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json(['html' => ''], 400);
        }
    }

    /**
     * Render product name to html
     *
     * @param int $id
     */
    public function closeProdNameTrademark(int $id)
    {
        try {
            $trademark = $this->trademarkService->find($id);
            $products = $trademark->getProductsWithRelation();
            $html = '';
            if (count($products) > 3) {
                $takeProducts = array_slice($products, 0, 3);
            } else {
                $takeProducts = $products;
            }

            foreach ($takeProducts as $key => $text) {
                $html = $html . '<div>' . $text;
                if (count($products) > 3 && $key == count($takeProducts) - 1) {
                    $html = $html . '<span>
                        <a href="javascript:void(0)" data-trademark-id="' . $trademark->id . '" class="showAllProductTrademark">[+]</a>
                    </span>';
                }
                $html = $html . '</div>';
            }

            return response()->json(['html' => $html], 200);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json(['html' => ''], 400);
        }
    }

    /**
     * Render product name to html
     *
     * @param int $id
     */
    public function showAllProdNameAppTrademark(int $id)
    {
        try {
            $appTrademark = $this->appTrademarkService->find($id);
            $products = $appTrademark->getProducts();
            $html = '';
            foreach ($products as $key => $text) {
                $html = $html . '<div>' . $text . '</div>';
            }

            return response()->json(['html' => $html], 200);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json(['html' => ''], 400);
        }
    }

    /**
     * Render product name to html
     *
     * @param int $id
     */
    public function closeProdNameAppTrademark(int $id)
    {
        try {
            $appTrademark = $this->appTrademarkService->find($id);
            $products = $appTrademark->getProducts();
            $html = '';
            if (count($products) > 3) {
                $takeProducts = array_slice($products, 0, 3);
            } else {
                $takeProducts = $products;
            }
            foreach ($takeProducts as $key => $text) {
                $html = $html . '<div>' . $text;
                if (count($products) > 3 && $key == count($takeProducts) - 1) {
                    $html = $html . '<span>
                        <a href="javascript:void(0)" data-app-trademark-id="' . $appTrademark->id . '" class="showAllProductAppTrademark">[+]</a>
                        </span>';
                }
                $html = $html . '</div>';
            }

            return response()->json(['html' => $html], 200);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json(['html' => ''], 400);
        }
    }

    /**
     * Show all record of app trademark apply.
     *
     * @param Request $request.
     * @return Response
     */
    public function showAllApplyTrademarkApply(Request $request)
    {
        try {
            $appTrademarksApply = $this->appTrademarkService->getAppTrademarkWaitingApply([
                'status' => AppTrademark::NOT_APPLY,
                'type_pages' => [
                    AppTrademark::PAGE_TYPE_U011B,
                    AppTrademark::PAGE_TYPE_U011B_31,
                    AppTrademark::PAGE_TYPE_U021B,
                    AppTrademark::PAGE_TYPE_U021B_31,
                    AppTrademark::PAGE_TYPE_U031EDIT,
                    AppTrademark::PAGE_TYPE_U031EDIT_WITH_NUMBER,
                ],
            ]);

            $countMax = $appTrademarksApply->count();
            $data = $appTrademarksApply->skip(PAGE_LIMIT_10)->limit($countMax)->get();

            $html = $this->serializeATMApplyHTML($data);

            return response()->json(['html' => $html], 200);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json(['html' => ''], 400);
        }
    }

    /**
     * Serialize data my folder to html.
     *
     * @param Collection
     * @return string
     */
    protected function serializeMyFolderHTML(Collection $myFolders): string
    {
        try {
            $html = '';
            foreach ($myFolders as $key => $folder) {
                $trademark = null;
                if ($folder->type != MyFolder::TYPE_OTHER) {
                    if ($folder->relationTrademark) {
                        $trademark = $folder->relationTrademark->trademark;
                    }
                }

                $html = $html . '<tr>
                    <td class="center">' . (Carbon::parse($folder->created_at)->format('Y/m/d') ?? '') . '</td>
                    <td>' . ($folder->folder_number ?? '') . '</td>
                    <td>' . ($trademark->reference_number ?? '') . '</td>
                    <td>';
                if ($trademark && $trademark->type_trademark == Trademark::TRADEMARK_TYPE_LETTER) {
                    $html = $html . '<span class="trademark_name">' . ($trademark->name_trademark ?? ''). '<span>';
                } elseif ($trademark && $trademark->type_trademark == Trademark::TRADEMARK_TYPE_OTHER) {
                    $html = $html . '<img width="100px" src="' . ($trademark->image_trademark ?? '').'" alt="">';
                } else {
                    if ($folder->type_trademark == Trademark::TRADEMARK_TYPE_LETTER) {
                        $html = $html . '<span class="trademark_name">' . ($folder->name_trademark ?? '') . '<span>';
                    } else {
                        $html = $html . '<img width="100px" src="' . ($folder->image_trademark ?? '') . '" alt="">';
                    }
                }
                $html = $html . '</td> <td class="td-product-name">';
                if (isset($folder->myFolderProduct) && $folder->myFolderProduct) {
                    $totalProduct = count($folder->myFolderProduct);
                    foreach ($folder->myFolderProduct as $key => $mfProd) {
                        $html = $html .  (string) $mfProd->getNameProd() . ($key < $totalProduct - 1 ? '、' : '');
                    }
                }

                $html = $html . '
                    </td>
                    <td>
                        <a href="' . route('user.search-ai.goto-result', ['folder_id' => $folder->id]) . '">
                            <button type="button" class="btn_e redirect-to-u020b">マイリストへ</button>
                        </a>
                    </td>
                    <td>
                        <button type="button" class="btn_d delete-my-folder" data-folder-id="' . $folder->id . '" >削除</button>
                    </td>
                    </tr>
                ';
            }

            return $html;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Serialize data application trademark to html.
     *
     * @param Collection
     * @return string
     */
    protected function serializeTodoListHTML(Collection $toDoList): string
    {
        try {
            $html = '';
            foreach ($toDoList as $todo) {
                if (isset($todo->notice) && isset($todo->notice->trademark)) {
                    $trademark = null;
                    if (isset($todo->notice)) {
                        $trademark = $todo->notice->trademark;
                    }
                    $backgroundTd = $todo->getClassColorTop();
                    $html = $html . '<tr>
                    <td class="' . $backgroundTd . '">
                    ' . ($todo->response_deadline_ams ? Carbon::parse($todo->response_deadline_ams)->format('Y/m/d') : '') . '
                    </td>
                    <td class="' . $backgroundTd . '">
                        <a href="' . ($trademark ? route('user.application-detail.index', ['id' => $trademark->id, 'from' => FROM_U000_TOP]) : '#!') . '">
                        ' . $trademark->trademark_number . '
                        </a>
                    </td>
                    <td class="' . $backgroundTd . '">' . ($trademark->reference_number ?? '') . '</td>
                    <td class="' . $backgroundTd . '">
                    ';
                    if ($trademark && $trademark->type_trademark == Trademark::TRADEMARK_TYPE_LETTER) {
                        $html = $html . '<span class="trademark_name">' . ($trademark->name_trademark ?? '') . '</span>';
                    } else {
                        $html = $html . '<img width="100px" src="' . ($trademark->image_trademark ?? '') . '" alt="" srcset="">';
                    }
                    $html = $html . '</td><td class="' . $backgroundTd . '">';

                    if (isset($todo->redirect_page) && $todo->redirect_page) {
                        $html = $html . '<a href="' . $todo->redirect_page . '">
                                ' . $todo->content . '
                            </a>';
                    } else {
                        $html = $html . '<span>' . $todo->content . '</span>';
                    }
                    $html = $html . '</td>
                        <td class="' . $backgroundTd . '">' . ($todo->created_at ? Carbon::parse($todo->created_at)->format('Y/m/d') : '') . '</td>
                    </tr>';
                }
            }

            return $html;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Update notice.
     */
    public function updateNotice(Request $request)
    {
        try {
            $noticeDetail = $this->noticeDetailService->find($request->id);
            $noticeDetail->update([
                'is_open' => NoticeDetail::TYPE_OPEN,
            ]);
            $notice = $noticeDetail->notice;
            if ($notice->flow == Notice::FLOW_QA) {
                $noticeDetail->update([
                    'is_answer' => NoticeDetail::IS_ANSWER,
                ]);
            }

            // Update is_answer at phase 2 row 101
            if ($notice->flow == Notice::FLOW_RESPONSE_REASON && $notice->step == Notice::STEP_5
                || $notice->flow == Notice::FLOW_RENEWAL_BEFORE_DEADLINE && $notice->step == null) {
                $trademark = $notice->trademark;

                $this->noticeDetailRepository->findByCondition([
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                ])->with('notice')->get()
                    ->where('notice.trademark_id', $trademark->id)
                    ->where('notice.user_id', $trademark->user_id)
                    ->whereIn('notice.flow', [Notice::FLOW_RESPONSE_REASON, Notice::FLOW_RENEWAL_BEFORE_DEADLINE])
                    ->filter(function ($item) {
                        if ($item->notice->flow == Notice::FLOW_RESPONSE_REASON && $item->notice->step == Notice::STEP_5) {
                            return true;
                        } elseif ($item->notice->flow == Notice::FLOW_RENEWAL_BEFORE_DEADLINE && $item->notice->step == null) {
                            return true;
                        }
                        return false;
                    })
                    ->map(function ($item) {
                        $item->update([
                            'is_answer' => NoticeDetail::IS_ANSWER,
                        ]);
                    });
            }

            return response()->json([], 200);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json([], 400);
        }
    }

    /**
     * Serialize data application trademark to html.
     *
     * @param Collection
     * @return string
     */
    protected function serializeATMNotApplyHTML($notApplies): string
    {
        try {
            $html = '';
            foreach ($notApplies as $key => $trademark) {
                $html = $html . '<tr>
                    <td>' . $trademark->getCreateAtNotApply() . '</td>
                    <td>' . (isset($trademark) ? $trademark->trademark_number : '') . '</td><td>';

                if (isset($trademark) && isset($trademark->payment) && !$trademark->payment->payment_status) {
                    $secret = Str::random(11);
                    Session::put($secret, [
                        'payment_id' => $trademark->payment->id,
                        'payment_type' => $trademark->payment->payerInfo->payment_type ?? 0,
                        'from_page' => U000TOP
                    ]);
                    $html = $html . '
                    <span class="quote_number">' . $trademark->payment->quote_number . '</span>
                    <a class="btn_a" href="' . route('user.payment.index', ['s' => $secret]) . '">お申込み</a>
                    ';
                }

                $html = $html . '</td><td class="111">' . ($trademark->reference_number ?? '') . '</td><td>';

                if ($trademark->type_trademark == Trademark::TRADEMARK_TYPE_LETTER) {
                    $html = $html . '<span class="trademark_name">' .  $trademark->name_trademark . '</span>';
                } else {
                    $html = $html . '<img width="100px" src="' . $trademark->image_trademark . '" alt="" srcset="">';
                }

                $products = $trademark->getProductsWithRelation();
                if (count($products) > 3) {
                    $takeProducts = array_slice($products, 0, 3);
                } else {
                    $takeProducts = $products;
                }

                $html = $html . '</td>
                <td class="td_distinction-product-cls">';
                foreach ($takeProducts as $key => $text) {
                    $html = $html . '<div>' . $text;
                    if (count($products) > 3 && $key == count($takeProducts) - 1) {
                        $html = $html . '<span>
                            <a href="javascript:void(0)" data-trademark-id="' . $trademark->id . '" class="showAllProductTrademark">[+]</a>
                        </span>';
                    }
                    $html = $html . '</div>';
                }

                $html = $html . '</td>
                <td class="td-btn-redirect-u021b center">
                    <button
                        class="btn_b btn-type-redirect"
                        data-trademark-id="' . $trademark->id . '"
                        data-status="' . (isset($trademark->appTrademark) ? $trademark->appTrademark->status : '') . '"
                        data-type-page="' . (isset($trademark->appTrademark) ? $trademark->appTrademark->type_page : '') . '"
                        type="button"
                    >
                        表示
                    </button>
                </td>
                <td class="td-btn-delete center">
                    <button class="btn_d delete-anken" data-trademark-id="' . $trademark->id . '" type="button">
                        削除
                    </button>
                </td>
                </tr>
                ';
            }

            return $html;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Serialize data application trademark to html.
     *
     * @param Collection
     * @return string
     */
    protected function serializeATMApplyHTML(Collection $applies): string
    {
        $html = '';
        try {
            foreach ($applies as $key => $apply) {
                $products = $apply->getProducts();
                if (count($products) > 3) {
                    $takeProducts = array_slice($products, 0, 3);
                } else {
                    $takeProducts = $products;
                }
                $trademark = $apply->trademark;

                $html = $html . '<tr>
                <td>' . (isset($apply->created_at) ? Carbon::parse($apply->created_at)->format('Y/m/d') : '') . '</td>
                <td>
                    <a href="' . route('user.application-detail.index', ['id' => $trademark->id, 'from' => FROM_U000_TOP]) . '">' .
                    (isset($trademark) ? $trademark->trademark_number : '') . '
                    </a>
                </td>
                <td>';
                if (isset($trademark) && isset($trademark->payment) && !$trademark->payment->payment_status) {
                    $secret = Str::random(11);
                    Session::put($secret, [
                        'payment_id' => $trademark->payment->id,
                        'payment_type' => $trademark->payment->payerInfo->payment_type ?? 0,
                        'from_page' => U000TOP
                    ]);
                    $html = $html . '<span class="quote_number">' . $trademark->payment->quote_number . '</span>
                    <a class="btn_a" href="' . route('user.payment.index', ['s' => $secret]) . '">お申込み</a>';
                }
                $html = $html . '</td>
                    <td>' . ($trademark->reference_number ?? '') . '</td>
                    <td>';
                if ($trademark->type_trademark == Trademark::TRADEMARK_TYPE_LETTER) {
                    $html = $html . '<span class="trademark_name">' .  $trademark->name_trademark . '</span>';
                } else {
                    $html = $html . '<img width="100px" src="' . $trademark->image_trademark . '" alt="" srcset="">';
                }

                $html = $html . '</td>
                <td class="td_distinction-product-cls">';
                foreach ($takeProducts as $key => $text) {
                    $html = $html . '<div>' . $text;
                    if (count($products) > 3 && $key == count($takeProducts) - 1) {
                        $html = $html . '<span>
                            <a href="javascript:void(0)" data-app-trademark-id="' . $apply->id . '" class="showAllProductAppTrademark">[+]</a>
                            </span>';
                    }
                    $html = $html . '</div>';
                }
                $html = $html . '</td>
                <td class="td-btn-redirect-u021b center">
                    <button
                        class="btn_b btn-type-redirect"
                        data-trademark-id="' . $trademark->id . '"
                        data-status="' . $apply->status . '"
                        data-type-page="' . $apply->type_page . '"
                        type="button"
                    >
                        表示
                    </button>
                </td>
                <td class="td-btn-delete center">
                    <button class="btn_d delete-anken" data-trademark-id="' . $trademark->id . '" type="button">
                        削除
                    </button>
                </td>
                </tr>';
            }

            return $html;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
