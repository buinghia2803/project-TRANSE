<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MProduct;
use App\Services\MDistinctionService;
use App\Services\MProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MProductController extends Controller
{
    protected $mProductService;
    protected $mDistinctionService;

    public function __construct(
        MProductService $mProductService,
        MDistinctionService $mDistinctionService
    )
    {
        $this->mProductService = $mProductService;
        $this->mDistinctionService = $mDistinctionService;
    }

    /**
     * Update type ajax
     *
     * @param Request $request
     * @return  mixed
     */
    public function updateTypeAjax(Request $request)
    {
        if ($request->ajax()) {
            $inputs = $request->all();
            if (isset($inputs['id'])) {
                $mProduct = $this->mProductService->find($inputs['id']);
                if ($mProduct->type != MProduct::TYPE_SEMI_CLEAN) {
                    $mProduct->type = MProduct::TYPE_SEMI_CLEAN;
                    $flug = $mProduct->save();
                    return response()->json(['data' => $flug], 200);
                }
            }
        }
    }

    /**
     * Get code and distinction by product
     *
     * @param Request $request
     * @return void
     */
    public function getCodeAndDistinction(Request $request)
    {
        if ($request->ajax()) {
            $inputs = $request->all();
            if (isset($inputs['product_id'])) {
                $id = $inputs['product_id'];
                $product = $this->mProductService->find($id);

                return response()->json([
                    'm_distinction' => $product->mDistinction,
                    'm_codes' => $product->code,
                ]);
            }
        }
    }

    /**
     * @param Request $request
     * @return View
     */
    public function getGoodMasterSearch(Request $request): View
    {
        $inputs = $request->all();
        $dataSession = null;
        $routeResultSearchNoCode = route('admin.goods-master-result', ['no_code' => true]);
        if (!empty($inputs['s'])) {
            $key = $inputs['s'];
            if (session()->has($key)) {
                $routeResultSearchNoCode = route('admin.goods-master-result', ['no_code' => true, 's' => $key]);
                $dataSession = session()->get($key);
                unset($dataSession['_token']);
            }
        }
        $conditionFilter = MProduct::getConditionFilters();
        $conditionCompare = MProduct::getConditionCompare();
        $optionField = MProduct::getOptionField();
        $typeOriginal = MProduct::TYPE_ORIGINAL_CLEAN;
        $typeRegis = MProduct::TYPE_REGISTERED_CLEAN;

        $data = [
            'conditionFilter' => $conditionFilter,
            'conditionCompare' => $conditionCompare,
            'optionField' => $optionField,
            'typeOriginal' => $typeOriginal,
            'typeRegis' => $typeRegis,
            'dataSession' => $dataSession,
            'routeResultSearchNoCode' => $routeResultSearchNoCode,
        ];

        return view('admin.modules.products.goods-master-search', $data);
    }

    /**
     * Post Good Master Search
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postGoodMasterSearch(Request $request)
    {
        $inputs = $request->all();
        $key = Str::random(11);
        session()->put($key, $inputs);

        return redirect()->route('admin.goods-master-result', ['s' => $key]);
    }

    /**
     * GetGoodMasterResult
     *
     * @param Request $request
     * @return View
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getGoodMasterResult(Request $request)
    {
        $inputs = $request->all();
        $key = null;
        $dataSession = null;
        $routeBack = route('admin.goods-master-search');
        $routeGoodsMasterDetail = route('admin.goods-master-search');
        if (!empty($inputs['s'])) {
            $key = $inputs['s'];
            if (session()->has($key)) {
                $routeBack = route('admin.goods-master-search', ['s' => $key]);
                $routeGoodsMasterDetail = route('admin.goods-master-detail', ['s' => $key]);
                $dataSession = session()->get($key);
            }
        }
        $dataProduct = $this->mProductService->getDataSearchProduct($inputs, $dataSession);

        $data = [
            'key' => $key,
            'routeBack' => $routeBack,
            'routeGoodsMasterDetail' => $routeGoodsMasterDetail,
            'dataProduct' => $dataProduct,
        ];

        return view('admin.modules.products.goods-master-result', $data);
    }

    /**
     * GetGoodMasterDetail
     *
     * @param Request $request
     * @return View
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getGoodMasterDetail(Request $request): View
    {
        $inputs = $request->all();
        $key = null;
        $dataSession = null;
        if (!empty($inputs['s']) && session()->has($inputs['s'])) {
            $key = $inputs['s'];
            $dataSession = session()->get($key);
        }

        $dataProduct = $this->mProductService->getDataSearchProduct($inputs, $dataSession);
        $distinctions = $this->mDistinctionService->listDistinctionOptions();
        //const
        $isParentStatus = MProduct::IS_PARENT;
        $notParentStatus = MProduct::IS_PARENT;

        $data = [
            'keySession' => $key,
            'dataProduct' => $dataProduct,
            'distinctions' => $distinctions,
            'isParentStatus' => $isParentStatus,
            'notParentStatus' => $notParentStatus,
        ];
        return view('admin.modules.products.goods-master-detail', $data);
    }

    /**
     * PostGoodMasterDetail
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postGoodMasterDetail(Request $request)
    {
        $inputs = $request->all();
        $result = $this->mProductService->editDataGoodMasterProduct($inputs);
        if ($result) {
            return back()->with('message', __('messages.general.Common_S008'));
        }
        return back();
    }

    /**
     * CheckProductNumberAjax
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function checkProductNumberAjax(Request $request): JsonResponse
    {
        if ($request->ajax()) {
            $inputs = $request->all();
            if (!empty($inputs['value'])) {
                $query = $this->mProductService->findByCondition(['products_number' => $inputs['value']])
                    ->whereIn('type', [MProduct::TYPE_ORIGINAL_CLEAN, MProduct::TYPE_REGISTERED_CLEAN]);
                if (!empty($inputs['m_product_id'])) {
                    $query = $query->where('id', '!=', $inputs['m_product_id']);
                }
                $result = $query->first();
                if ($result) {
                    return response()->json(['status' => true]);
                }
            }
            return response()->json(['status' => false]);
        }

        return response()->json([]);
    }
}
