<?php

namespace App\Http\Controllers\Admin;

use App\Models\AppTrademark;
use App\Services\TrademarkService;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\Services\ImportService;
use App\Services\MatchingResultService;
use Illuminate\Contracts\View\View;
use App\Services\XMLProcedures\Procedure;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;

class ImportController extends Controller
{
    protected ImportService $importService;
    protected MatchingResultService $matchingResultService;
    protected TrademarkService $trademarkService;

    /**
     * Constructor
     *
     * @param   ImportService $importServicee
     *
     * @return  void
     */
    public function __construct(
        ImportService $importService,
        MatchingResultService $matchingResultService,
        TrademarkService $trademarkService
    )
    {
        $this->middleware('permission:import_01.viewImport01')->only(['viewImport01']);
        $this->middleware('permission:import_02.viewImport02')->only(['viewImport02']);
        $this->middleware('permission:import_02.saveImportXML')->only(['saveImportXML']);
        $this->middleware('permission:import_02.showCompleted')->only(['showCompleted']);

        $this->importService = $importService;
        $this->matchingResultService = $matchingResultService;
        $this->trademarkService = $trademarkService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewImport01()
    {
        return view('admin.modules.import.import01');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendSession(Request $request)
    {
        $errors = [];
        $data = [];
        try {
            foreach ($request->file as $key => $value) {
                $fileName = $value->getClientOriginalName();
                Storage::putFileAs('xml', $value, $fileName);

                $content = $this->importService->getContentFileXML(public_path('xml/' . $fileName));
                $content = $this->importService->changeXMLContent($content);

                $content = mb_convert_encoding($content, "SJIS-win", "UTF-8");
                if (ctype_print($content)) {
                    $errors[] = [
                        'filename' => $fileName,
                        'message' => __('messages.general.Common_E028')
                    ];
                    break;
                }
                $fileXml = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA, 'jpopc', true);
                if (!$fileXml) {
                    $errors[] = [
                        'filename' => $fileName,
                        'message' => __('messages.general.Common_E028')
                    ];
                    break;
                }

                $array = json_decode(json_encode($fileXml), true);
                $array['fileName'] = $fileName;
                $error = $this->validateDataXML($array, $fileName);
                if ($error && count($error)) {
                    $errors[] = $error;
                }

                $data[$key] = new Procedure($array);

                Storage::delete($fileName);
            }
            if ($errors && count($errors)) {
                return redirect()->back()->withErrors($errors);
            }

            $key = Str::random(11);
            Session::put($key, collect($data));

            return redirect()->route('admin.import-doc-xml-show', ['s' => $key]);
        } catch (\Exception $e) {
            Log::error($e);

            return redirect()->back()->with([
                'error' => __('messages.error'),
            ]);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function viewImport02(Request $request)
    {
        try {
            if (!$request->has('s') && !$request->s) {
                return redirect()->route('admin.import-doc-xml');
            }
            // Check data must collection
            $sessionData = Session::get($request->s);

            if (!($sessionData instanceof Collection)) {
                abort(404);
            }
            $data = $this->importService->getDataImport($sessionData);
            $dataView = $data['dataView'];
            $dataNull = $data['dataNull'];
            $trademarkClose = $data['trademarkClose'];
            $trademarkDuplicate = $data['trademarkDuplicate'];
            $countSession = $sessionData->count();

            return view('admin.modules.import.import02', compact(
                'dataNull',
                'dataView',
                'countSession',
                'trademarkClose',
                'trademarkDuplicate'
            ));
        } catch (\Exception $e) {
            Log::error($e);

            return redirect()->back()->with('error', __('messages.general.Import_A000_E003'));
        }
    }

    /**
     * Completed screen.
     *
     * @return View
     */
    public function showCompleted(): View
    {
        return view('admin.modules.import.completed');
    }

    /**
     * Saving data
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function saveImportXML(Request $request): RedirectResponse
    {
        if (!$request->has('s') && !$request->s) {
            return redirect()->back();
        }
        $errors = [];
        $data = Session::get($request->s);
        $dataCheck = $data;
        $errors = $this->importService->validateDataXMLImport02($data);
        if ($errors && count($errors)) {
            return redirect()->back()->withErrors($errors);
        }
        $result = $this->importService->saveData($data);
        if ($result) {
            return redirect()->route('admin.save-xml-data-completed');
        } else {
            return redirect()->back()->withErrors([
                'errors' => [
                    'filename' => null,
                    'message' => __('messages.import_xml.system_error')
                ],
            ]);
        }
    }

    /**
     * Validation data in file XML
     *
     * @param array $array
     * @param string $fileName
     *
     * @return array
     */
    public function validateDataXML(array $array, string $fileName): array
    {
        $error = [];
        if (!isset($array['procedure-infomation'])
            && !isset($array['procedure-infomation']['application-reference'])
        ) {
            $error = [
                'filename' => $fileName,
                'message' => __('messages.general.Import_A000_E003')
            ];
        }

        $applicationNumber = $array['procedure-infomation']['application-reference']['application-number'] ?? null;
        if (!$applicationNumber) {
            $error = [
                'filename' => $fileName,
                'message' => __('messages.general.import_application_number_error'),
            ];
        }
        $trademark = $this->trademarkService->findByCondition(['application_number' => $applicationNumber])->first();

        if (empty($trademark)) {
            $error = [
                'filename' => $fileName,
                'message' => __('messages.general.import_trademark_not_exist'),
            ];
        }

        if (!empty($trademark) && $trademark->isCancel()) {
            $error = [
                'filename' => $fileName,
                'message' => __('messages.general.import_app_trademark_cancel'),
            ];
        }

        if (!empty($trademark)) {
            $appTrademark = $trademark->appTrademark ?? null;
            if (!empty($appTrademark) && $appTrademark->status != AppTrademark::STATUS_ADMIN_CONFIRM) {
                $error = [
                    'filename' => $fileName,
                    'message' => __('messages.general.import_app_trademark_error'),
                ];
            }
        }

        return $error;
    }
}
