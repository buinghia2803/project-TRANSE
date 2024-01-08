<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\SFTSuitableProductService;
use Illuminate\Http\Request;

class SftSuitableProductController extends Controller
{
    protected $sftSuitableProductService;

    public function __construct(SFTSuitableProductService $sftSuitableProductService)
    {
        $this->sftSuitableProductService = $sftSuitableProductService;
    }

    /**
     * Update is block column ajax
     *
     * @param Request $request
     * @return void
     */
    public function updateIsBlockAjax(Request $request)
    {
        if ($request->ajax()) {
            $inputs = $request->all();
            $res = $this->sftSuitableProductService->updateIsBlockAjax($inputs);
            if ($res) {
                return response()->json([
                    'status' => true,
                ]);
            }
        }
        return response()->json([
            'status' => false,
        ]);
    }
}
