<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\TrademarkService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class AjaxController extends Controller
{
    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct(TrademarkService $trademarkService)
    {
        $this->trademarkService = $trademarkService;
    }

    /**
     * Change Reference Number
     *
     * @param Request $request
     * @param $id
     * @return JsonResponse
     */
    public function changeReferenceNumber(Request $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $trademark = $this->trademarkService->find($id);

            if ($trademark == null || $trademark->user_id != Auth::guard('web')->id()) {
                return response()->json([
                    'message' => __('messages.update_fail'),
                ], CODE_ERROR_500);
            }

            $this->trademarkService->update($trademark, [
                'reference_number' => $request->reference_number ?? '',
            ]);

            DB::commit();

            return response()->json([
                'message' => __('messages.update_success'),
            ], CODE_SUCCESS_200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json([
                'message' => __('messages.update_fail'),
            ], CODE_ERROR_500);
        }
    }

    /**
     * Change Reference Number
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function setSession(Request $request)
    {
        try {
            Session::put($request->key ?? '', $request->value ?? []);
            return response()->json([], CODE_SUCCESS_200);
        } catch (\Exception $e) {
            return response()->json([], CODE_ERROR_500);
        }
    }

    /**
     * Common Handle Ajax
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function handle(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $response = [];
            $fromPage = $request->from_page ?? null;

            switch ($fromPage) {
                case U201:
                    break;
            }

            DB::commit();

            return response()->json($response);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }
}
