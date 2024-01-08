<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Services\MNationService;
use App\Services\MPrefectureService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayerInfoController extends Controller
{
    protected $mNationService;
    protected $mPrefectureService;

    public function __construct(MNationService $mNationService, MPrefectureService $mPrefectureService)
    {
        $this->mNationService = $mNationService;
        $this->mPrefectureService = $mPrefectureService;
    }

    /**
     * Payer info
     *
     * @return View
     */
    public function getPayerInfo(): View
    {
        $nations = $this->mNationService->listNationOptions();
        $prefectures = $this->mPrefectureService->listPrefectureOptions();

        $data = [
            'nations' => $nations,
            'prefectures' => $prefectures,
        ];
        return view('user.modules.profiles.payer_info', $data);
    }
}
