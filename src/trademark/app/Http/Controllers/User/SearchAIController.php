<?php

namespace App\Http\Controllers\User;

use App\Helpers\CommonHelper;
use App\Helpers\FileHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchAI\SearchAIRequest;
use App\Models\AppTrademark;
use App\Models\MProduct;
use App\Models\MyFolder;
use App\Models\SupportFirstTime;
use App\Models\Trademark;
use App\Services\MProductService;
use App\Services\MyFolderService;
use App\Services\PrecheckService;
use App\Services\SupportFirstTimeService;
use App\Services\TrademarkService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class SearchAIController extends Controller
{
    private MyFolderService $myFolderService;
    private MProductService $mProductService;
    private TrademarkService $trademarkService;
    private SupportFirstTimeService $supportFirstTimeService;
    private PrecheckService $precheckService;

    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(
        MyFolderService $myFolderService,
        MProductService $MProductService,
        TrademarkService $trademarkService,
        SupportFirstTimeService $supportFirstTimeService,
        PrecheckService $precheckService
    )
    {
        $this->myFolderService = $myFolderService;
        $this->mProductService = $MProductService;
        $this->trademarkService = $trademarkService;
        $this->supportFirstTimeService = $supportFirstTimeService;
        $this->precheckService = $precheckService;
    }

    /**
     * SearchAI
     *
     * @return  View
     */
    public function searchAI(Request $request): View
    {
        $dataSearch = $request->keyword ?? [];
        $searchAIData = Session::get(SESSION_SEARCH_AI);
        Session::forget(SESSION_SEARCH_AI);
        Session::forget(SESSION_ADDITION_PRODUCT);

        $myFolder = $this->myFolderService->findByCondition([
            'user_id' => Auth::guard('web')->user()->id ?? 1,
        ])->get()->last();

        $keywords = json_decode($myFolder->keyword ?? '[]');

        if (empty($searchAIData) && !empty($request->trademark_id)) {
            $trademark = $this->trademarkService->find($request->trademark_id);
            if (!empty($trademark)) {
                $searchAIData = [
                    'type_trademark' => $trademark->type_trademark ?? null,
                    'name_trademark' => $trademark->name_trademark ?? null,
                    'image_trademark' => $trademark->image_trademark ?? null,
                ];
            }
        }

        return view('user.modules.search-ai.index', compact(
            'searchAIData',
            'keywords',
            'dataSearch'
        ));
    }

    /**
     * Handle SearchAI
     *
     * @param   SearchAIRequest $request
     * @return  RedirectResponse
     */
    public function postSearchAI(SearchAIRequest $request): RedirectResponse
    {
        if ($request->hasFile('image_trademark')) {
            $imageTrademark = FileHelper::uploads($request->image_trademark, [], 'uploads/trademark');
            $imageTrademark = $imageTrademark[0]['filepath'] ?? null;
        }

        $dataSearchAI = [
            'type_trademark' => $request->type_trademark ?? null,
            'name_trademark' => $request->name_trademark ?? null,
            'image_trademark' => $imageTrademark ?? null,
            'keyword' => $request->keyword ?? [],
        ];

        // Set session search_ai
        Session::put(SESSION_SEARCH_AI, $dataSearchAI);

        // Set session referer
        Session::put(SESSION_REFERER_SEARCH_AI, [
            'referer' => FROM_SEARCH_AI,
        ]);

        // // Set session referer for u011b
        // Session::put(SESSION_REFERER_SEARCH_AI, [
        //     'referer' => FROM_SUPPORT_FIRST_TIME,
        //     'support_first_time_id' => 1,
        //     'trademark_id' => 1,
        // ]);
        // Session::put(SESSION_SUGGEST_PRODUCT, [ 1, 2, 3 ]);

        // Set session referer for u021b
        // Session::put(SESSION_REFERER_SEARCH_AI, [
        //     'referer' => FROM_PRECHECK,
        //     'precheck_id' => 1,
        //     'trademark_id' => 1,
        // ]);
        // Session::put(SESSION_SUGGEST_PRODUCT, [ 1, 2, 3 ]);

        return redirect()->route('user.search-ai.result');
    }

    /**
     * Reset Session
     *
     * @param int $folder_id
     * @return  void
     */
    public function resetSession(int $folder_id)
    {
        $folder = $this->myFolderService->find($folder_id)->load('myFolderProduct');

        $myFolderProduct = $folder->myFolderProduct;
        $productArrayIds = $myFolderProduct->pluck('m_product_id')->toArray();

        switch ($folder->type) {
            case MyFolder::TYPE_SFT:
                $sft = $this->supportFirstTimeService->find($folder->target_id);

                Session::put(SESSION_REFERER_SEARCH_AI, [
                    'referer' => FROM_SUPPORT_FIRST_TIME,
                    'support_first_time_id' => $sft->id,
                    'trademark_id' => $sft->trademark_id,
                ]);
                Session::put(SESSION_SUGGEST_PRODUCT, $productArrayIds);
                break;
            case MyFolder::TYPE_PRECHECK:
                $precheck = $this->precheckService->find($folder->target_id);

                Session::put(SESSION_REFERER_SEARCH_AI, [
                    'referer' => FROM_PRECHECK,
                    'precheck_id' => $precheck->id,
                    'trademark_id' => $precheck->trademark_id,
                ]);
                Session::put(SESSION_SUGGEST_PRODUCT, $productArrayIds);
                break;
            case MyFolder::TYPE_OTHER:
                $keywords = json_decode($folder->keyword ?? '[]', true);

                $dataSearchAI = [
                    'type_trademark' => $folder->type_trademark ?? null,
                    'name_trademark' => $folder->name_trademark ?? null,
                    'image_trademark' => $folder->image_trademark ?? null,
                    'keyword' => $keywords ?? [],
                ];

                // Set session search_ai
                Session::put(SESSION_SEARCH_AI, $dataSearchAI);
                Session::put(SESSION_ADDITION_PRODUCT, $productArrayIds);

                // Set session referer
                Session::put(SESSION_REFERER_SEARCH_AI, [
                    'referer' => FROM_SEARCH_AI,
                ]);
                break;
        }
    }

    /**
     * Go to Search Ai Result
     *
     * @param int $folder_id
     * @return  RedirectResponse
     */
    public function gotoSearchAiResult(int $folder_id): RedirectResponse
    {
        $this->resetSession($folder_id);
        return redirect()->route('user.search-ai.result', [ 'folder_id' => $folder_id ?? 0 ]);
    }

    /**
     * SuggestAI
     *
     * @param   Request $request
     * @return  mixed
     */
    public function suggestAI(Request $request)
    {
        // Load all folder of user
        $folders = $this->myFolderService->findByCondition([
            'user_id' => Auth::guard('web')->id(),
        ])->get();

        // Get folder if has referer
        $refererData = Session::get(SESSION_REFERER_SEARCH_AI);

        if (empty($refererData)) {
            abort(404);
        };

        $referer = $refererData['referer'] ?? '';
        $targetID = null;
        $trademark = null;
        $folder = null;
        if ($referer == FROM_SUPPORT_FIRST_TIME) {
            $trademark = $this->trademarkService->find($refererData['trademark_id'])->load('appTrademark');

            $targetID = $refererData['support_first_time_id'] ?? null;
            $folder = $folders->where('type', MyFolder::TYPE_SFT)->where('target_id', $targetID)->last();
        } elseif ($referer == FROM_PRECHECK) {
            $trademark = $this->trademarkService->find($refererData['trademark_id'])->load('appTrademark');

            $targetID = $refererData['precheck_id'] ?? null;
            $folder = $folders->where('type', MyFolder::TYPE_PRECHECK)->where('target_id', $targetID)->last();
        } elseif (!empty($request->folder_id)) {
            $folderID = $request->folder_id ?? 0;
            $folder = $this->myFolderService->find($folderID);
        }

        // Get data at SearchAI
        if ($referer == FROM_SUPPORT_FIRST_TIME || $referer == FROM_PRECHECK) {
            Session::forget(SESSION_SEARCH_AI);
        }
        $searchAiData = Session::get(SESSION_SEARCH_AI);
        $searchAiData['keyword'] = json_decode(json_encode($searchAiData['keyword'] ?? []), true);
        $keywords = $searchAiData['keyword'] ?? [];

        $keywordDataMatch = [];
        $keywordDataNotMatch = [];
        if (!empty($keywords)) {
            $mathData = $this->mProductService->getRelatedName($keywords);
            $keywordDataMatch = $mathData['keywordDataMatch'] ?? [];
            $keywordDataNotMatch = $mathData['keywordDataNotMatch'] ?? [];
        }

        // Trademark Info
        $typeTrademark = $trademark->type_trademark ?? $searchAiData['type_trademark'] ?? '';
        $nameTrademark = $trademark->name_trademark ?? $searchAiData['name_trademark'] ?? '';
        $imageTrademark = $trademark->image_trademark ?? $searchAiData['image_trademark'] ?? '';

        // Get product addition
        if (!in_array($referer, [FROM_SEARCH_AI, FROM_U031B])) {
            Session::forget(SESSION_ADDITION_PRODUCT);
        }
        $additionProductID = Session::get(SESSION_ADDITION_PRODUCT);
        $additionProducts = $this->mProductService->findMany($additionProductID ?? [], ['mDistinction']);

        // Get product suggest
        if ($referer == FROM_SEARCH_AI) {
            Session::forget(SESSION_SUGGEST_PRODUCT);
        }
        $suggestProductID = Session::get(SESSION_SUGGEST_PRODUCT);

        $suggestProduct = $this->mProductService->findMany($suggestProductID ?? [], ['mDistinction']);

        $pricePackage = $this->supportFirstTimeService->getPricePackOriginal();
        $setting = $this->supportFirstTimeService->getSetting();

        // Is show register btn
        $isShowRegister = true;
        if (!empty($trademark) && !empty($trademark->appTrademark) && $trademark->appTrademark->status != AppTrademark::STATUS_UNREGISTERED_SAVE) {
            $isShowRegister = false;
        }

        // Is Trademark Image
        $isTrademarkImage = false;
        if ($typeTrademark == Trademark::TRADEMARK_TYPE_OTHER) {
            $isTrademarkImage = true;
        }

        return view('user.modules.search-ai.suggest-ai', compact(
            'referer',
            'targetID',
            'folder',
            'folders',
            'searchAiData',
            'trademark',
            'typeTrademark',
            'nameTrademark',
            'imageTrademark',
            'keywordDataMatch',
            'keywordDataNotMatch',
            'additionProducts',
            'suggestProduct',
            'pricePackage',
            'setting',
            'isShowRegister',
            'isTrademarkImage',
            'keywords'
        ));
    }

    /**
     * Handle Suggest AI
     *
     * @param   Request $request
     * @return  RedirectResponse
     */
    public function postSuggestAI(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $referer = $request->referer ?? null;
            $submitType = $request->submit_type ?? null;
            $redirect = null;
            if (empty($referer) || empty($submitType)) {
                return redirect()->back();
            };

            // Format data of request
            $formatData = $this->myFolderService->formatData($request);
            $dataMyFolder = $formatData['dataMyFolder'];
            $allProduct = $formatData['allProduct'];

            // Handle save DB
            switch ($referer) {
                case FROM_SEARCH_AI:
                case FROM_U031B:
                    $dataMyFolder['target_id'] = 0;
                    $dataMyFolder['type'] = MyFolder::TYPE_OTHER;
                    break;
                case FROM_SUPPORT_FIRST_TIME:
                    $dataMyFolder['target_id'] = $request->target_id;
                    $dataMyFolder['type'] = MyFolder::TYPE_SFT;
                    break;
                case FROM_PRECHECK:
                    $dataMyFolder['target_id'] = $request->target_id;
                    $dataMyFolder['type'] = MyFolder::TYPE_PRECHECK;
                    break;
            }
            $dataMyFolder['type_trademark'] = $dataMyFolder['type_trademark'] ?? MyFolder::TRADEMARK_TYPE_LETTER;
            if ($submitType == SEARCH_AI_CREATE) {
                $folder = $this->myFolderService->createFolder($dataMyFolder, $allProduct);
            } elseif ($submitType == SEARCH_AI_EDIT) {
                $folderId = $request->folder_id ?? 0;
                $folder = $this->myFolderService->updateFolder($folderId, $dataMyFolder, $allProduct);
            } else {
                $folderId = $request->folder_id ?? 0;
                if (empty($folderId)) {
                    $folder = $this->myFolderService->createFolder($dataMyFolder, $allProduct);
                } else {
                    $folder = $this->myFolderService->updateFolder($folderId, $dataMyFolder, $allProduct);
                }
            }
            switch ($referer) {
                case FROM_SEARCH_AI:
                    $redirect = route('user.register-apply-trademark-after-search', [
                        'folder_id' => $folder->id,
                    ]);
                    break;
                case FROM_SUPPORT_FIRST_TIME:
                    $redirect = route('user.sft.proposal-ams', [
                        'id' => $request->target_id,
                        'folder_id' => $folder->id,
                    ]);
                    break;
                case FROM_PRECHECK:
                    $redirect = route('user.precheck.application-trademark-v2', [
                        'id' => $request->trademark_id,
                        'folder_id' => $folder->id,
                    ]);
                    break;
                case FROM_U031B:
                    $dataSessionU031b = Session::get(SESSION_U031B_REDIRECT_U020B);
                    $params = [];
                    if (!empty($dataSessionU031b['trademark_id'])) {
                        $params['id'] = $dataSessionU031b['trademark_id'];
                    }
                    $params['folder_id'] = $folder->id;
                    Session::forget(SESSION_U031B_REDIRECT_U020B);

                    $redirect = route('user.register-apply-trademark-after-search', $params);
            }

            // Commit
            DB::commit();

            // Forget session
            Session::forget(SESSION_REFERER_SEARCH_AI);
            Session::forget(SESSION_SEARCH_AI);
            Session::forget(SESSION_SUGGEST_PRODUCT);
            Session::forget(SESSION_ADDITION_PRODUCT);

            if (in_array($submitType, [ SEARCH_AI_CREATE, SEARCH_AI_EDIT ])) {
                $this->resetSession($folder->id ?? 0);

                CommonHelper::setMessage($request, MESSAGE_SUCCESS, __('messages.save_draft'));
                return redirect()->route('user.search-ai.result', [ 'folder_id' => $folder->id ?? 0 ]);
            }

            return redirect($redirect);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            return redirect()->back()->withInput();
        }
    }

    /**
     * Send Session To Search Ai Report
     *
     * @param Request $request
     */
    public function showSearchAiReport(Request $request)
    {
        $request->session()->put(SESSION_SUGGEST_PRODUCT, $request->all());

        return redirect()->route('user.search-ai.quote');
    }

    /**
     * Get View Data Search Ai Report
     *
     * @param   Request $request
     * @return  mixed
     */
    public function getViewSearchAiReport(Request $request)
    {
        $listProduct = $request->session()->get(SESSION_SUGGEST_PRODUCT);
        if (!$listProduct) {
            return redirect()->back();
        }
        $trademark = null;
        if ($listProduct['trademark_id']) {
            $trademark = $this->trademarkService->find($listProduct['trademark_id'])->load('appTrademark');
        }

        // Is show register btn
        $isShowRegister = true;
        if (!empty($trademark) && !empty($trademark->appTrademark) && $trademark->appTrademark->status != AppTrademark::STATUS_UNREGISTERED_SAVE) {
            $isShowRegister = false;
        }

        // Is Trademark Image
        $typeTrademark = $trademark->type_trademark ?? $listProduct['type_trademark'] ?? '';
        $isTrademarkImage = false;
        if (!empty($typeTrademark) && $typeTrademark == Trademark::TRADEMARK_TYPE_OTHER) {
            $isTrademarkImage = true;
        }

        // Load all folder of user
        $folders = $this->myFolderService->findByCondition([
            'user_id' => Auth::guard('web')->id(),
        ])->get();

        $pricePackage = $this->supportFirstTimeService->getPricePackOriginal();

        $refererData = Session::get(SESSION_REFERER_SEARCH_AI);

        $prodAdditionalIds = !empty($listProduct['prod_additional_ids']) ? explode(',', $listProduct['prod_additional_ids']) : [];
        $prodSuggestIds = !empty($listProduct['prod_suggest_ids']) ? explode(',', $listProduct['prod_suggest_ids']) : [];
        $prodIds = array_merge($prodAdditionalIds, $prodSuggestIds);

        $block = 3;

        $chunkListProduct = array_chunk($prodIds, $block);
        $countChunkListProduct = count($chunkListProduct);

        $totalProduct = count($prodIds);
        $productBlock = $totalProduct > $block ? $totalProduct - $block : 0;

        $packAPrice = [
            'base_price' => $pricePackage[0][0]['base_price'] ?? 0,
            'price_one_block' => $pricePackage[1][0]['base_price'] ?? 0,
            'prod_add' => $countChunkListProduct > 1 ? ($countChunkListProduct - 1) * ($pricePackage[1][0]['base_price'] ?? 0) : 0,
            'prod_add_total' => $productBlock
        ];
        $packAPrice['total'] = $packAPrice['base_price'] + $packAPrice['prod_add'];

        $packBPrice = [
            'base_price' => $pricePackage[0][1]['base_price'] ?? 0,
            'price_one_block' => $pricePackage[1][1]['base_price'] ?? 0,
            'prod_add' => $countChunkListProduct > 1 ? ($countChunkListProduct - 1) * ($pricePackage[1][1]['base_price'] ?? 0) : 0,
            'prod_add_total' => $productBlock
        ];
        $packBPrice['total'] = $packBPrice['base_price'] + $packBPrice['prod_add'];

        $packCPrice = [
            'base_price' => $pricePackage[0][2]['base_price'] ?? 0,
            'price_one_block' => $pricePackage[1][2]['base_price'] ?? 0,
            'prod_add' => $countChunkListProduct > 1 ? ($countChunkListProduct - 1) * ($pricePackage[1][2]['base_price'] ?? 0) : 0,
            'prod_add_total' => $productBlock
        ];
        $packCPrice['total'] = $packCPrice['base_price'] + $packCPrice['prod_add'];
        return view('user.modules.search-ai.search-ai-report', compact(
            'refererData',
            'listProduct',
            'folders',
            'totalProduct',
            'productBlock',
            'packAPrice',
            'packBPrice',
            'packCPrice',
            'trademark',
            'isShowRegister',
            'isTrademarkImage',
        ));
    }

    /**
     * Create Record My Folder and My Folder Product
     *
     * @param Request $request
     * @return  RedirectResponse
     */
    public function postSearchAiReport(Request $request): RedirectResponse
    {
        $sessionDetail = Session::get(SESSION_SUGGEST_PRODUCT);
        $refererData = Session::get(SESSION_REFERER_SEARCH_AI);
        $submitType = $request->submit_type ?? null;
        $sessionDetail['pack'] = $request->type;
        $request = new Request($sessionDetail);
        $formatData = $this->myFolderService->formatData($request);
        $dataMyFolder = $formatData['dataMyFolder'];
        $allProduct = $formatData['allProduct'];
        switch ($submitType) {
            case SEARCH_AI_REGISTER:
                $sessionDetail['from_page'] = U020C;
                Session::put(SESSION_SUGGEST_PRODUCT, $sessionDetail);

                return redirect()->route('user.register-apply-trademark-after-search');
            case SEARCH_AI_PRECHECK:
                if ($refererData['referer'] == FROM_SUPPORT_FIRST_TIME || $refererData['referer'] == FROM_PRECHECK) {
                    if ($sessionDetail['image_trademark']) {
                        return redirect()->back()->with('error', __('messages.support_first_time.support_U011_E008'))->withInput();
                    }
                }

                $prodAdditionalIds = explode(',', $sessionDetail['prod_additional_ids']);
                $prodSuggestIds = explode(',', $sessionDetail['prod_suggest_ids']);
                $mProductIds = array_filter(array_unique(array_merge($prodAdditionalIds, $prodSuggestIds)));
                if (count($mProductIds) == 0) {
                    return redirect()->back()->with('error', __('messages.general.Search_AI_select_product'))->withInput();
                }

                if (empty($sessionDetail['trademark_id'])) {
                    $trademark = $this->trademarkService->createTrademark([
                        'type_trademark' => $sessionDetail['type_trademark'] ?? 1,
                        'name_trademark' => $sessionDetail['name_trademark'] ?? '',
                        'image_trademark' => $sessionDetail['image_trademark'] ?? null,
                    ]);
                } else {
                    $trademark = $this->trademarkService->find($sessionDetail['trademark_id']);
                }

                $paramSession = [
                    'from_page' => FROM_PAGE_U021,
                    'data' => [
                        'm_product_ids' => $mProductIds,
                    ]
                ];
                $key = Str::random(11);
                Session::put($key, $paramSession);

                return redirect()->route('user.precheck.register-precheck', ['id' => $trademark->id, 's' => $key]);
            case SEARCH_AI_CREATE:
                // $this->myFolderService->createFolderSearchAiReport($sessionDetail, $refererData);
                $dataMyFolder['target_id'] = 0;
                $dataMyFolder['type'] = MyFolder::TYPE_OTHER;

                $this->myFolderService->createFolder($dataMyFolder, $allProduct);

                CommonHelper::setMessage(request(), MESSAGE_SUCCESS, __('messages.general.Common_E047'));
                return redirect()->back();
            case SEARCH_AI_EDIT:
                $folderId = $sessionDetail['folder_id'] ?? null;
                $this->myFolderService->updateFolder($folderId, $dataMyFolder, $allProduct);

                CommonHelper::setMessage(request(), MESSAGE_SUCCESS, __('messages.general.Common_E047'));
                return redirect()->back();
            default:
                return redirect()->back();
        }
    }

    /**
     * Ajax Suggest AI
     *
     * @param   Request $request
     * @return  JsonResponse
     */
    public function ajaxSuggestAI(Request $request): JsonResponse
    {
        $additionalProduct = $request->additional_product ?? [];
        $suggestProduct = $request->suggest_product ?? [];

        $result = [
            'additional_product' => [],
            'suggest_product' => [],
        ];

        // Load for Additional Product
        if (!empty($additionalProduct)) {
            $mathData = $this->mProductService->getRelatedName($additionalProduct);
            $mathData = $this->mProductService->filterDataSearchAI($mathData['keywordDataMatch'] ?? []);
            $result['additional_product'] = $mathData ?? [];
        }

        // Load for Suggest Product
        if (!empty($suggestProduct)) {
            $mathData = $this->mProductService->getRelatedName($suggestProduct);
            $mathData = $this->mProductService->filterDataSearchAI($mathData['keywordDataMatch'] ?? []);
            $result['suggest_product'] = $mathData ?? [];
        }

        return response()->json($result);
    }
}
