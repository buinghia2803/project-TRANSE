<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommonHelper;
use App\Helpers\FileHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AjaxController extends BaseController
{
    /**
     * Constructor
     *
     * @return  void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Upload File in ajax
     *
     * @param   Request $request
     * @return  JsonResponse
     */
    public function editorUpload(Request $request): JsonResponse
    {
        try {
            if (!$request->hasFile('file')) {
                return response()->json([], CODE_ERROR_500);
            }

            $file = $request->file;

            $image = FileHelper::uploads($file);
            $filepath = $image[0]['filepath'] ?? null;

            if (empty($filepath)) {
                return response()->json([], CODE_ERROR_500);
            }

            return response()->json([ 'image' => $filepath ?? '' ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([], CODE_ERROR_500);
        }
    }

    /**
     * Upload File in ajax
     *
     * @param   Request $request
     * @return  JsonResponse
     */
    public function quickUpdate(Request $request): JsonResponse
    {
        try {
            $permission = $request->permission;

            $userPermission = Auth::guard(ADMIN_ROLE)->user()->getPermissionsViaRoles();
            $userPermission = $userPermission->pluck('name')->toArray();

            // Check permission
            if (in_array($permission, $userPermission)) {
                $table = $request->table ?? '';
                $field = $request->field ?? '';
                $ids = $request->ids ?? '';
                $value = $request->value ?? '';

                DB::table($table)->whereIn('id', $ids)->update([
                    $field => $value,
                ]);

                return response()->json([
                    'message' => __('messages.update_success'),
                ], CODE_SUCCESS_200);
            } else {
                return response()->json([
                    'message' => __('messages.permission_denied'),
                ], CODE_ERROR_500);
            }
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => __('messages.error'),
            ], CODE_ERROR_500);
        }
    }

    /**
     * Generate Address
     *
     * @param   Request $request
     * @return  JsonResponse
     */
    public function generateAddress(Request $request): JsonResponse
    {
        try {
            $postCode = $request->post_code ?? 0;
            $postCodeData = CommonHelper::generateAddressJP($postCode);

            if ($postCodeData == null) {
                return response()->json([
                    'data' => [],
                    'address' => '',
                ], CODE_SUCCESS_200);
            }

            return response()->json($postCodeData, CODE_SUCCESS_200);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json([
                'message' => __('messages.error'),
            ], CODE_ERROR_500);
        }
    }

    /**
     * Upload File
     *
     * @param   Request $request
     * @return  JsonResponse
     */
    public function uploadFile(Request $request): JsonResponse
    {
        $filepath = [];

        try {
            $files = $request->file('files');
            $path = $request->path ?? FOLDER_TEMP;

            foreach ($files as $file) {
                $fileInfo = FileHelper::uploads($file, [], $path);
                $pathInfo = $fileInfo[0]['filepath'] ?? null;

                $filepath[] = [
                    'path' => $pathInfo,
                    'name' => Str::replace($path . '/', '', $pathInfo),
                ];
            }

            return response()->json([
                'status' => CODE_SUCCESS_200,
                'filepath' => $filepath,
            ]);
        } catch (\Exception $e) {
            Log::error($e);

            foreach ($filepath as $path) {
                FileHelper::unlink($path['path'] ?? '');
            }

            return response()->json([
                'status' => CODE_ERROR_500,
                'message' => __('messages.error'),
            ]);
        }
    }

    /**
     * Remove File
     *
     * @param   Request $request
     * @return  JsonResponse
     */
    public function removeFile(Request $request): JsonResponse
    {
        try {
            $path = $request->path ?? null;

            if (!empty($path)) {
                FileHelper::unlink($path);
            }

            return response()->json([ 'status' => CODE_SUCCESS_200 ]);
        } catch (\Exception $e) {
            Log::error($e);

            return response()->json([
                'status' => CODE_ERROR_500,
                'message' => __('messages.error'),
            ]);
        }
    }
}
