<?php

namespace App\Services;

use App\Models\AppTrademark;
use App\Models\ChangeInfoRegister;
use App\Models\MPrefecture;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\NoticeDetailBtn;
use App\Repositories\ChangeInfoRegisterRepository;
use App\Repositories\RegisterTrademarkRepository;
use App\Services\TrademarkService;
use App\Services\BaseService;
use App\Repositories\TrademarkInfoRepository;
use App\Repositories\TrademarkRepository;
use App\Services\Common\NoticeService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TrademarkInfoService extends BaseService
{
    protected TrademarkInfoRepository $trademarkInfoRepository;
    protected TrademarkService $trademarkService;
    protected ChangeInfoRegisterRepository $changeInfoRegisterRepository;
    protected NoticeService $noticeService;
    protected TrademarkRepository $trademarkRepository;
    protected RegisterTrademarkRepository $registerTrademarkRepository;
    protected NoticeDetailService $noticeDetailService;

    /**
     * Initializing the instances and variables
     *
     * @param TrademarkInfoRepository $trademarkInfoRepository
     * @param TrademarkService $trademarkService
     */
    public function __construct(
        TrademarkInfoRepository $trademarkInfoRepository,
        TrademarkService $trademarkService,
        ChangeInfoRegisterRepository $changeInfoRegisterRepository,
        NoticeService $noticeService,
        TrademarkRepository $trademarkRepository,
        RegisterTrademarkRepository $registerTrademarkRepository,
        NoticeDetailService $noticeDetailService
    )
    {
        $this->repository = $trademarkInfoRepository;
        $this->trademarkService = $trademarkService;
        $this->changeInfoRegisterRepository = $changeInfoRegisterRepository;
        $this->noticeService = $noticeService;
        $this->noticeDetailService = $noticeDetailService;
        $this->trademarkRepository = $trademarkRepository;
        $this->registerTrademarkRepository = $registerTrademarkRepository;
    }

    /**
     * Insert Trademark Info
     *
     * @param Request $request
     * @param integer $trademark_id
     *
     * @return boolean
     */
    public function updateOrCreateTrademarkInfo($data, $trademark_id, $requestFromPage = null, $fromPage = null): bool
    {
        try {
            $authId = auth()->user()->id;
            if (!$authId) {
                return redirect()->back();
            }

            $condition = [
                'id' => $trademark_id,
                'user_id' => $authId,
            ];
            $arrayCreate = [];
            $newTrademarkInfoID = [];

            $arr = $this->trademarkService->findByCondition($condition, ['appTrademark'])->first();
            $targetId = $arr->appTrademark->id;

            $trademarkInfos = $this->findByCondition(['target_id' => $targetId])->get();
            $oldTrademarkInfoID = $trademarkInfos->pluck('id')->toArray();

            foreach ($data as $value) {
                if ($value['name'] && $targetId && $value['m_nation_id'] && $value['type_acc']) {
                    $value['target_id'] = $targetId;
                    $value['created_at'] = now();
                    $value['updated_at'] = now();
                    if ($value['name'] == null) {
                        $value['name'] = '';
                    }
                    $arrayCreate = $value;
                    if ($arrayCreate['id']) {
                        unset($arrayCreate['id']);
                    }

                    if ($arrayCreate['m_nation_id'] != NATION_JAPAN_ID) {
                        $arrayCreate['m_prefecture_id'] = MPrefecture::where('m_nation_id', $arrayCreate['m_nation_id'])->first()->id;
                    }

                    $conditionTrademarkInfo = ['id' => $value['id']];
                    if (isset($requestFromPage)) {
                        if ($requestFromPage == U021B) {
                            $conditionTrademarkInfo['from_page'] = $fromPage;
                        }
                    }

                    $newTrademarkInfo = $this->repository->updateOrCreate($conditionTrademarkInfo, $arrayCreate);
                    $newTrademarkInfoID[] = $newTrademarkInfo->id;
                }
            }

            // Delete TrademarkInfo
            $deleteTrademarkInfoID = [];
            foreach ($oldTrademarkInfoID as $oldID) {
                if (!in_array($oldID, $newTrademarkInfoID)) {
                    $deleteTrademarkInfoID[] = $oldID;
                }
            }
            $this->findByCondition([])->whereIn('id', $deleteTrademarkInfoID)->delete();

            return true;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Find trademark infor.
     *
     * @param AppTrademark $sft
     * @param $fromPageTrademarkInfo
     */
    public function findTrademarkInfo(AppTrademark $appTrademark, $fromPageTrademarkInfo = null)
    {
        $condition = [
            'type' => TYPE_APP_TRADEMARK,
            'target_id' => $appTrademark->id,
        ];

        if ($fromPageTrademarkInfo) {
            $condition['from_page'] = $fromPageTrademarkInfo;
        }

        return $this->repository->findByCondition($condition)->get();
    }

    /**
     * Change Address
     *
     * @param  array $params
     * @param  mixed $fromPage
     * @return array
     */
    public function changeAddress(array $params, $fromPage = null): array
    {
        $redirect = null;
        if ($params['trademark_info_id']) {
            $trademarkInfo = $this->find($params['trademark_info_id']);
            $model = $trademarkInfo;
            if ($params['m_nation_id'] != NATION_JAPAN_ID) {
                $params['m_prefecture_id'] = MPrefecture::where('m_nation_id', $params['m_nation_id'])->first()->id;
            }
        } elseif ($params['change_info_register_id']) {
            $changeInfoRegister = $this->changeInfoRegisterRepository->find($params['change_info_register_id']);
            $model = $changeInfoRegister->trademarkInfo;
            unset($params['trademark_info_id']);
        } elseif ($params['register_trademark_id']) {
            $registerTrademark = $this->registerTrademarkRepository->find($params['register_trademark_id']);
            unset($params['trademark_info_id']);
            $model = $registerTrademark;
            $params['trademark_info_name'] = $params['name'];
            $params['trademark_info_nation_id'] = $params['m_nation_id'];
            $params['trademark_info_address_first'] = $params['m_prefecture_id'];
            $params['trademark_info_address_second'] = $params['address_second'];
            $params['trademark_info_address_three'] = $params['address_three'];
        }

        $params['is_updated'] = IS_UPDATED_TRUE;
        if (isset($fromPage) && $fromPage == A301) {
            $this->updateTrademarkInfoChangeAddress($model, $params);
            $redirect = A301;
        } elseif (isset($fromPage) && $fromPage == A700_SHUTSUGANNIN02) {
            $params['m_prefectures_id'] = $params['m_prefecture_id'];
            $this->updateTrademarkInfoChangeAddress($model, $params);
            if (isset($params['trademark_info_id']) && !empty($params['trademark_info_id'])) {
                $this->sendNoticeChangeAddressOfShutsugannin02($params);
                $redirect = A000ANKEN_TOP;
            } else {
                $redirect = A700_SHUTSUGANNIN03;
            }
        } elseif (isset($fromPage) && $fromPage == A700_SHUTSUGANNIN03) {
            $this->updateTrademarkInfoChangeAddress($model, $params);
            $this->sendNoticeChangeAddress($params);
            $redirect = A700_SHUTSUGANNIN03;
        }

        $condtion = [
            'params' => $params,
            'redirect_to' => $redirect,
        ];

        return $condtion;
    }

    /**
     * Update Trademark Info Change Address
     *
     * @param  $model
     * @param  array $params
     * @return void
     */
    public function updateTrademarkInfoChangeAddress($model, $params)
    {
        $model->update($params);
    }

    /**
     * Send Notice Change Address
     *
     * @param  array $params
     * @return void
     */
    public function sendNoticeChangeAddress($params)
    {
        $trademark = $this->trademarkRepository->find($params['trademark_id']);
        $queries = [
            'id' => $params['trademark_id'],
        ];
        if (isset($params['change_info_register_id'])) {
            $queries['change_info_register_id'] = $params['change_info_register_id'];
        } elseif (isset($params['trademark_info_id'])) {
            $queries['trademark_info_id'] = $params['trademark_info_id'];
        } elseif (isset($params['register_trademark_id'])) {
            $queries['register_trademark_id'] = $params['register_trademark_id'];
        }
        $targetPage = route('admin.registration.change-address.index', $queries);
        $redirectPage = route('admin.application-detail.index', ['id' => $params['trademark_id']]);

        $stepBeforeNotice = $this->noticeDetailService->findByCondition([
            'raw' => 'type_acc <> ' . NoticeDetail::TYPE_USER,
            'completion_date' => null,
        ])->with('notice')->get()
            ->where('notice.trademark_id', $trademark->id)
            ->where('notice.user_id', $trademark->user_id)
            ->whereIn('notice.flow', [Notice::FLOW_REGISTER_TRADEMARK, Notice::FLOW_CHANGE_INFO]);
        $stepBeforeNotice->map(function ($item) {
            $item->update([
                'completion_date' => Carbon::now(),
            ]);
        });

        $this->noticeService->sendNotice([
            'notices' => [
                'trademark_id' => $trademark->id,
                'user_id' => $trademark->user_id,
                'flow' => Notice::FLOW_REGISTER_TRADEMARK,
            ],
            'notice_details' => [
                // A-000top
                [
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '事務担当　登録査定：出願人DB変更',
                    'is_action' => true,
                ],
                // A-000anken_top
                [
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '事務担当　登録査定：出願人DB変更',
                    'attribute' => '所内処理',
                    'buttons' => [
                        [
                            'btn_type' => NoticeDetailBtn::BTN_PDF_UPLOAD,
                            'from_page' => A700_SHUTSUGANNIN03,
                        ],
                    ]
                ],
            ],
        ]);
    }

    public function sendNoticeChangeAddressOfShutsugannin02($params)
    {
        $trademark = $this->trademarkRepository->find($params['trademark_id']);
        $queries = [
            'id' => $params['trademark_id'],
            'trademark_info_id' => $params['trademark_info_id'],
        ];
        $adminId = Auth::user()->id;
        $targetPage = route('admin.registration.change-address.index', $queries);
        $redirectPage = route('admin.application-detail.index', ['id' => $params['trademark_id']]);
        $this->noticeService->sendNotice([
            'notices' => [
                'trademark_id' => $trademark->id,
                'trademark_info_id' => null,
                'user_id' => $trademark->user_id,
                'flow' => Notice::FLOW_REGISTER_TRADEMARK,
            ],
            'notice_details' => [
                // A-000top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'content' => '事務担当　登録査定：出願人DB変更',
                ],
                // A-000anken_top
                [
                    'target_id' => null,
                    'type_acc' => NoticeDetail::TYPE_OFFICE_MANAGER,
                    'target_page' => $targetPage,
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => '事務担当　登録査定：出願人DB変更',
                    'attribute' => '所内処理',
                    'buttons' => [
                        [
                            'btn_type' => NoticeDetailBtn::BTN_PDF_UPLOAD,
                            'from_page' => A700_SHUTSUGANNIN02,
                        ],
                    ]
                ],
            ],
        ]);
    }
}
