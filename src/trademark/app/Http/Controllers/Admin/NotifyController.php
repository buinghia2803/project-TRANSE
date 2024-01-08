<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\CommonHelper;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NotifyController extends BaseController
{
    protected SettingService $settingService;

    public function __construct(SettingService $settingService)
    {
        parent::__construct();
        $this->settingService = $settingService;
    }

    /**
     * Display a000news_edit.
     *
     * @param   Request $request
     * @return  View
     */
    public function index(Request $request)
    {
        $urlBackDefault = route('admin.home');
        $checkUrl = route('admin.notify.index');
        $backUrl = CommonHelper::setBackUrl($urlBackDefault, $checkUrl);

        $setting = $this->settingService->findByCondition([
            'key' => A000NEWS_EDIT,
        ])->first();
        $settingValue = $setting->value ?? null;

        if (isset($request->s)) {
            $sessionData = Session::get($request->s);
            $settingValue = $sessionData['value'] ?? null;
        }

        return view('admin.modules.notify.index', compact(
            'backUrl',
            'settingValue'
        ));
    }

    /**
     * Send data to a000news_edit_confirm
     *
     * @param   Request $request
     * @return  RedirectResponse
     */
    public function sendComment(Request $request)
    {
        $params = $request->all();
        $data = [
          'key' => $params['key'],
          'value' => $params['value'],
        ];
        $secretKey = Str::random(11);
        Session::put($secretKey, $data);
        return redirect()->route('admin.notify.confirm', ['s' => $secretKey]);
    }

    /**
     * Display a000news_edit_confirm
     *
     * @param   Request $request
     * @return  View
     */
    public function confirm(Request $request)
    {
        if (!isset($request->s)) {
            abort(404);
        }
        $data = Session::get($request->s);
        $data['s'] = $request->s;
        $urlBackDefault = route('admin.home');
        $checkUrl = route('admin.notify.index');
        $backUrl = CommonHelper::setBackUrl($urlBackDefault, $checkUrl);
        return view('admin.modules.notify.confirm', compact('backUrl', 'data'));
    }

    /**
     * Save data a000news_edit_confirm
     *
     * @param   Request $request
     * @return  RedirectResponse
     */
    public function postConfirm(Request $request)
    {
        $params = $request->all();
        $this->settingService->updateOrCreate(
            [
            'key' => $params['key'],
            ],
            [
            'key' => $params['key'],
            'value' => $params['value'],
            ]
        );
        return redirect()->route('admin.home')->with('message', __('messages.general.Common_S008'));
    }
}
