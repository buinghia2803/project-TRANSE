<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Models\Admin;
use App\Models\Notice;
use App\Models\NoticeDetail;
use App\Models\PrecheckComment;
use App\Models\PrecheckKeepData;
use App\Models\PrecheckKeepDataProdResult;
use App\Models\PrecheckProduct;
use App\Repositories\PrecheckProductRepository;
use App\Services\Common\NoticeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use App\Models\Precheck;
use App\Models\PrecheckResult;
use App\Repositories\NoticeDetailRepository;
use Google\Service\Bigquery\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class PrecheckProductService extends BaseService
{
    protected $precheckResultService;
    protected $precheckService;
    protected $precheckCommentService;
    private $noticeService;
    private $noticeDetailRepository;

    /**
     * Initializing the instances and variables
     *
     * @param PrecheckProductRepository $precheckProductRepository
     * @param PrecheckResultService $precheckResultService
     * @param PrecheckCommentService $precheckResultCommentService
     * @param PrecheckService $precheckService
     */
    public function __construct(
        PrecheckProductRepository         $precheckProductRepository,
        PrecheckResultService             $precheckResultService,
        PrecheckCommentService            $precheckCommentService,
        NoticeService                     $noticeService,
        PrecheckKeepDataService           $precheckKeepDataService,
        PrecheckKeepDataProdService       $precheckKeepDataProdService,
        PrecheckKeepDataProdResultService $precheckKeepDataProdResultService,
        NoticeDetailRepository            $noticeDetailRepository,
        PrecheckService $precheckService,
        NoticeDetailService $noticeDetailService
    )
    {
        $this->repository = $precheckProductRepository;
        $this->precheckResultService = $precheckResultService;
        $this->precheckCommentService = $precheckCommentService;
        $this->noticeService = $noticeService;
        $this->precheckKeepDataService = $precheckKeepDataService;
        $this->precheckKeepDataProdService = $precheckKeepDataProdService;
        $this->precheckKeepDataProdResultService = $precheckKeepDataProdResultService;
        $this->precheckService = $precheckService;
        $this->noticeDetailRepository = $noticeDetailRepository;
        $this->noticeDetailService = $noticeDetailService;
    }

    /**
     * Get data precheck products
     *
     * @param array $id
     * @return array
     */
    public function getPrecheckProduct(array $id): array
    {
        $data = $this->repository->getPrecheckProduct($id);
        return $data;
    }

    /**
     * Get data precheck products
     *
     * @param array $id
     * @return array
     */
    public function getPrecheckProduct2(array $id): array
    {
        $data = $this->repository->getPrecheckProduct2($id);
        return $data;
    }

    /**
     * Convert data precheck products (precheck simple and select check similar)
     *
     * @param object $collects
     * @return array
     */
    public function convert($collects, $id, $type = null)
    {
        $currentPrecheck = $this->precheckService->find($id);

        $precheckSelects = $this->precheckService->findByCondition(['trademark_id' => $currentPrecheck->trademark_id])
            ->where('type_precheck', Precheck::TYPE_PRECHECK_DETAILED_REPORT)
            ->where('id', '<=', $currentPrecheck->id)
            ->orderBy('id', 'DESC')->get();
        $precheckSimples = $this->precheckService->findByCondition(['trademark_id' => $currentPrecheck->trademark_id])
            ->where('type_precheck', Precheck::TYPE_PRECHECK_SIMPLE_REPORT)
            ->where('id', '<=', $currentPrecheck->id)
            ->orderBy('id', 'DESC')->get();

        $closestPrecheckSelect = $precheckSelects->where('id', '<>', $currentPrecheck->id)->first();
        $closestPrecheckSimple = $precheckSimples->where('id', '<>', $currentPrecheck->id)->first();

        foreach ($collects as $keyCollect => $collect) {
            $collect->map(function ($rowProduct, $key) use ($id, $keyCollect, $type, $closestPrecheckSelect, $closestPrecheckSimple) {
                $mCode = $rowProduct->m_code_id;
                $precheckProducts = $rowProduct->precheckProduct;
                $simple = [];
                $simplePresent = [];
                $detailPresent = [];
                $detail = [];

                $precheckProducts->map(function ($row) use ($rowProduct, &$simplePresent, &$simple, &$detail, &$detailPresent, $id, $mCode, $type, $keyCollect) {
                    $precheckResults = $row->precheckResult;

                    if ($precheckResults->count() == 0) {
                        $simple[] = null;
                        $detail[] = null;
                    } else {
                        foreach ($precheckResults as $precheckResult) {
                            $precheck = $precheckResult->precheck;
                            if ($precheck) {
                                if ($type != null) {
                                    if (isset($precheckResult->precheck) && ($mCode == $precheckResult->m_code_id)) {
                                        $precheckResult->updated_at_convert = CommonHelper::formatTime($precheckResult->updated_at, 'Y/m/d');
                                        if ($precheckResult->result_identification_detail < $precheckResult->result_similar_detail) {
                                            $precheckResult->result_final = $precheckResult->result_similar_detail;
                                        } else {
                                            $precheckResult->result_final = $precheckResult->result_identification_detail;
                                        }

                                        if ($precheckResult->precheck_id == $id) {
                                            if ($precheck->type_precheck == Precheck::TYPE_PRECHECK_SIMPLE_REPORT) {
                                                $simplePresent[] = $precheckResult;
                                            } else {
                                                $detailPresent[] = $precheckResult;
                                            }
                                        } else {
                                            if ($precheck->type_precheck == Precheck::TYPE_PRECHECK_SIMPLE_REPORT) {
                                                $simple[] = $precheckResult;
                                                $detail[] = null;
                                            } else {
                                                $detail[] = $precheckResult;
                                                $simple[] = null;
                                            }
                                        }
                                    }
                                } else {
                                    if ($keyCollect == $rowProduct->mDistinction->name) {
                                        $precheckResult->updated_at_convert = CommonHelper::formatTime($precheckResult->updated_at, 'Y/m/d');
                                        if ($precheckResult->result_identification_detail < $precheckResult->result_similar_detail) {
                                            $precheckResult->result_final = $precheckResult->result_similar_detail;
                                        } else {
                                            $precheckResult->result_final = $precheckResult->result_identification_detail;
                                        }

                                        if ($precheckResult->precheck_id == $id) {
                                            if ($precheck->type_precheck == Precheck::TYPE_PRECHECK_SIMPLE_REPORT) {
                                                $simplePresent[] = $precheckResult;
                                            } else {
                                                $detailPresent[] = $precheckResult;
                                            }
                                        } else {
                                            if ($precheck->type_precheck == Precheck::TYPE_PRECHECK_SIMPLE_REPORT) {
                                                $simple[] = $precheckResult;
                                                $detail[] = null;
                                            } else {
                                                $detail[] = $precheckResult;
                                                $simple[] = null;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                });

                $simpleDataConvert = [];
                if (count($simple) > 0) {
                    $simple = collect($simple)->filter();
                    if ($simple->count() > 0) {
                        foreach ($simple as $result) {
                            if (!empty($closestPrecheckSimple) && $closestPrecheckSimple->id == $result->precheck_id) {
                                $simpleDataConvert[0] = $result;
                            } else {
                                $simpleDataConvert[1] = $result;
                            }
                        }
                    }
                }
                if (empty($simpleDataConvert[0])) {
                    $simpleDataConvert[0] = null;
                }
                if (empty($simpleDataConvert[1])) {
                    $simpleDataConvert[1] = null;
                }

                $detailDataConvert = [];
                if (count($detail) > 0) {
                    $detail = collect($detail)->filter();
                    if ($detail->count() > 0) {
                        foreach ($detail as $result) {
                            if (!empty($closestPrecheckSelect) && $closestPrecheckSelect->id == $result->precheck_id) {
                                $detailDataConvert[0] = $result;
                            } else {
                                $detailDataConvert[1] = $result;
                            }
                        }
                    }
                }
                if (empty($detailDataConvert[0])) {
                    $detailDataConvert[0] = null;
                }
                if (empty($detailDataConvert[1])) {
                    $detailDataConvert[1] = null;
                }

                $simple = collect($simpleDataConvert);
                $detail = collect($detailDataConvert);

                $rowProduct->simple = $simple;
                $rowProduct->detail = $detail;

                $rowProduct->simplePresent = $simplePresent;
                $rowProduct->detailPresent = $detailPresent;

                return $rowProduct;
            });
        }

        return $collects;
    }

    /**
     * Convert data precheck products (precheck simple and select check similar)
     *
     * @param object $collects
     * @param $id
     * @param $type
     * @return array
     */
    public function getDataCheckSimple($collects, $id, $type)
    {
        $dataResult = [];

        $datas = $this->convert($collects, $id, $type);
        foreach ($datas as $key => $precheckProduct) {
            $dataResult[] = [
                'codeName' => $key,
                'product' => $precheckProduct,
            ];
        }
        return $dataResult;
    }

    /**
     * Insert data check simple (admin tantou)
     *
     * @param $request
     */
    public function insertDataPrechcheckSimple($request)
    {
        $countPrecheckProductIds = count($request->precheck_product_id);
        for ($i = 0; $i < $countPrecheckProductIds; $i++) {
            $result = $this->precheckResultService->updateOrCreate(
                [
                    'precheck_product_id' => $request->precheck_product_id[$i],
                    'precheck_id' => (int) $request->precheck_id,
                    'm_code_id' => (int) $request->m_code_id[$i],
                ],
                [
                    'precheck_product_id' => $request->precheck_product_id[$i],
                    'result_similar_simple' => $request->result_similar_simple[$i],
                    'precheck_id' => (int) $request->precheck_id,
                    'm_code_id' => (int) $request->m_code_id[$i],
                    'admin_id' => Auth::user()->id,
                    'updated_at' => \Carbon\Carbon::now(),
                ]
            );
        }

        $this->precheckCommentService->updateOrCreate(
            [
                'precheck_id' => (int) $request->precheck_id,
                'type' => PrecheckComment::TYPE_COMMENT_INTERNAL,
                'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
                'admin_id' => Auth::user()->id,
            ],
            [
                'content' => $request->content1 ?? '',
                'precheck_id' => $request->precheck_id,
                'admin_id' => Auth::user()->id,
                'type' => PrecheckComment::TYPE_COMMENT_INTERNAL,
                'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
            ]
        );

        $this->precheckCommentService->updateOrCreate(
            [
                'precheck_id' => (int) $request->precheck_id,
                'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
                'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
                'admin_id' => Auth::user()->id,
            ],
            [
                'content' => $request->content2 ?? '',
                'precheck_id' => $request->precheck_id,
                'admin_id' => Auth::user()->id,
                'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
                'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
            ]
        );
    }

    /**
     * Send notice of check simple (admin tantou)
     *
     * @param $trademark
     */
    public function sendNoticeOfCheckSimple($trademark, $precheck)
    {
        $redirectPageOfNoticeDetailPeriou = route('admin.precheck.view-precheck-simple', $trademark->id);
        $redirectPageOfNoticeDetailPeriou = str_replace(request()->root(), '', $redirectPageOfNoticeDetailPeriou);
        //update notice detail of rows 66 and 68 columns G,H
        $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_MANAGER,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'completion_date' => null,
        ], ['notice'])->get()
            ->where('notice.flow', Notice::FLOW_PRECHECK)
            ->where('notice.trademark_id', $trademark->id)
            ->map(function ($item) {
                $item->update([
                    'completion_date' => now(),
                ]);
            });
        $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_MANAGER,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'completion_date' => null,
            'content' => 'プレチェックサービス：お申し込み受領'
        ], ['notice'])->get()
            ->where('notice.flow', Notice::FLOW_PRECHECK)
            ->where('notice.trademark_id', $trademark->id)
            ->map(function ($item) {
                $item->update([
                    'completion_date' => now(),
                ]);
            });
        $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_MANAGER,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'completion_date' => null,
            'content' => 'プレチェックサービス：決済確認済'
        ], ['notice'])->get()
            ->where('notice.flow', Notice::FLOW_PRECHECK)
            ->where('notice.trademark_id', $trademark->id)
            ->map(function ($item) {
                $item->update([
                    'completion_date' => now(),
                ]);
            });
        // Send Notice Seki
        $this->noticeService->sendNotice([
            'notices' => [
                'flow' => Notice::FLOW_PRECHECK,
                'user_id' => $trademark->user_id,
                'trademark_id' => $trademark->id,
            ],
            'notice_details' => [
                // Send Notice User
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => route('admin.precheck.view-precheck-simple', [
                        'id' => $trademark->id,
                        'precheck_id' => $precheck->id,
                    ]),
                    'redirect_page' => route('user.precheck.application-trademark', [
                        'id' => $trademark->id,
                        'precheck_id' => $precheck->id,
                    ]),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'content' => 'プレチェックサービス：AMSからのレポート',
                ],
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'target_page' => route('admin.precheck.view-precheck-simple', [
                        'id' => $trademark->id,
                        'precheck_id' => $precheck->id,
                    ]),
                    'redirect_page' => route('user.precheck.application-trademark', [
                        'id' => $trademark->id,
                        'precheck_id' => $precheck->id,
                    ]),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'content' => 'プレチェックサービス：AMSからのレポート',
                ],
            ],
        ]);
    }

    /**
     * Convert data precheck check similar
     *
     * @param object $collects
     * @return array
     */
    public function getDataCheckSimilar($collects, $id, $type)
    {
        $dataResult = [];

        $datas = $this->convert($collects, $id, $type);
        foreach ($datas as $key => $precheckProduct) {
            $precheckProduct = $precheckProduct->map(function ($item) {
                $isRegisterProduct = $item->precheckProduct[count($item->precheckProduct) - 1]->is_register_product;

                $item->is_register_product = $isRegisterProduct;

                return $item;
            })->sortByDesc('is_register_product')->values();

            $dataResult[] = [
                'codeName' => $key,
                'product' => $precheckProduct,
            ];
        }
        return $dataResult;
    }


    /**
     * Insert data check similar (admin tantou)
     *
     * @param $request
     */
    public function insertDataPrecheckSimilar($request)
    {
        $results = $request->result;
        $resultSimilarDetails = $request->result_similar_detail;
        foreach ($results as $item) {
            $codeID = $item['code_id'] ?? null;
            $codeName = $item['code_name'] ?? null;
            $precheckProductID = $item['precheck_product_id'] ?? null;

            foreach ($resultSimilarDetails as $detailCodeName => $resultSimilarDetail) {
                if ($codeName == $detailCodeName) {
                    $this->precheckResultService->updateOrCreate([
                        'precheck_id' => (int) $request->precheck_id,
                        'precheck_product_id' => $precheckProductID,
                        'm_code_id' => $codeID,
                    ], [
                        'precheck_product_id' => $precheckProductID,
                        'result_similar_detail' => $resultSimilarDetail,
                        'precheck_id' => $request->precheck_id,
                        'm_code_id' => $codeID,
                        'admin_id' => Auth::user()->id
                    ]);
                }
            }
        }
        $this->precheckCommentService->updateOrCreate(
            [
                'precheck_id' => (int) $request->precheck_id,
                'type' => PrecheckComment::TYPE_COMMENT_INTERNAL,
                'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
                'admin_id' => Auth::user()->id,
            ],
            [
                'content' => $request->content2 ?? '',
                'precheck_id' => (int) $request->precheck_id,
                'admin_id' => Auth::user()->id,
                'type' => PrecheckComment::TYPE_COMMENT_INTERNAL,
                'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
            ]
        );

        $this->precheckCommentService->updateOrCreate(
            [
                'precheck_id' => (int) $request->precheck_id,
                'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
                'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
                'admin_id' => Auth::user()->id,
            ],
            [
                'content' => $request->content1 ?? '',
                'precheck_id' => (int) $request->precheck_id,
                'admin_id' => Auth::user()->id,
                'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
                'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
            ]
        );
    }

    /**
     * Send notice of check similar (admin tantou)
     *
     * @param $trademark
     */
    public function sendNoticeOfCheckSimilar($trademark, $precheck)
    {
        //update notice detail of rows 69 and 71 columns G,H
        $redirectPageOfNoticeDetailPeriou = route('admin.precheck.check-precheck-result', $trademark->id);
        $redirectPageOfNoticeDetailPeriou = str_replace(request()->root(), '', $redirectPageOfNoticeDetailPeriou);
        $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_MANAGER,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'completion_date' => null,
        ], ['notice'])->get()
            ->where('notice.flow', Notice::FLOW_PRECHECK)
            ->where('notice.trademark_id', $trademark->id)
            ->map(function ($item) {
                $item->update([
                    'completion_date' => now(),
                ]);
            });
        $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_MANAGER,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'completion_date' => null,
            'content' => 'プレチェックサービス：お申し込み受領'
        ], ['notice'])->get()
            ->where('notice.flow', Notice::FLOW_PRECHECK)
            ->where('notice.trademark_id', $trademark->id)
            ->map(function ($item) {
                $item->update([
                    'completion_date' => now(),
                ]);
            });
        $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_MANAGER,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'completion_date' => null,
            'content' => 'プレチェックサービス：決済確認済'
        ], ['notice'])->get()
            ->where('notice.flow', Notice::FLOW_PRECHECK)
            ->where('notice.trademark_id', $trademark->id)
            ->map(function ($item) {
                $item->update([
                    'completion_date' => now(),
                ]);
            });
        // Send Notice Seki
        $this->noticeService->sendNotice([
            'notices' => [
                'flow' => Notice::FLOW_PRECHECK,
                'user_id' => $trademark->user_id,
                'trademark_id' => $trademark->id,
            ],
            'notice_details' => [
                // Send Notice Seki
                [
                    'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                    'content' => 'プレチェックサービス：レポート　確認・承認',
                    'target_page' => route('admin.precheck.check-similar', [
                        'id' => $trademark->id,
                        'precheck_id' => $precheck->id,
                    ]),
                    'redirect_page' => route('admin.precheck.view-precheck-confirm', [
                        'id' => $trademark->id,
                        'precheck_id' => $precheck->id,
                    ]),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE
                ],
                [
                    'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
                    'content' => '責任者　プレチェックサービス：レポート　確認・承認',
                    'target_page' => route('admin.precheck.check-similar', [
                        'id' => $trademark->id,
                        'precheck_id' => $precheck->id,
                    ]),
                    'redirect_page' => route('admin.precheck.view-precheck-confirm', [
                        'id' => $trademark->id,
                        'precheck_id' => $precheck->id,
                    ]),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                    'attribute' => '所内処理'
                ],
            ],
        ]);
    }

    /**
     * Convert data precheck products (precheck select check unique)
     *
     * @param object $collects
     * @return array
     */
    public function getDataCheckUnique(object $collects, $id): array
    {
        $dataResult = [];
        $datas = $this->convert($collects, $id);
        foreach ($datas as $key => $precheckProduct) {
            $dataResult[] = [
                'codeDistriction' => $key,
                'product' => $precheckProduct,
            ];
        }
        return $dataResult;
    }

    /**
     * Insert data check unique (admin tantou)
     *
     * @param $request
     */
    public function insertDataPrecheckUnique($request)
    {
        $countPrecheckProductIds = count($request->precheck_product_id);
        for ($i = 0; $i < $countPrecheckProductIds; $i++) {
            foreach ($request->m_code_id[$request->precheck_product_id[$i]] as $valueCode) {
                $this->precheckResultService->updateOrCreate(
                    [
                        'precheck_product_id' => $request->precheck_product_id[$i],
                        'precheck_id' => (int) $request->precheck_id,
                        'm_code_id' => (int) $valueCode,
                    ],
                    [
                        'precheck_product_id' => $request->precheck_product_id[$i],
                        'result_identification_detail' => $request->result_identification_detail[$i],
                        'precheck_id' => (int) $request->precheck_id,
                        'm_code_id' => $valueCode,
                        'admin_id' => Auth::user()->id
                    ]
                );
            }
        }
        $this->precheckCommentService->updateOrCreate(
            [
                'precheck_id' => (int) $request->precheck_id,
                'type' => PrecheckComment::TYPE_COMMENT_INTERNAL,
                'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
                'admin_id' => Auth::user()->id,
            ],
            [
                'content' => $request->content2 ? $request->content2 : '',
                'precheck_id' => $request->precheck_id,
                'admin_id' => Auth::user()->id,
                'type' => PrecheckComment::TYPE_COMMENT_INTERNAL,
                'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
            ]
        );
        $this->precheckCommentService->updateOrCreate(
            [
                'precheck_id' => (int) $request->precheck_id,
                'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
                'input_of_page' => PrecheckComment::INPUT_OF_PAGE_SHIKI,
                'admin_id' => Auth::user()->id,
            ],
            [
                'content' => $request->content1 ? $request->content1 : '',
                'precheck_id' => $request->precheck_id,
                'admin_id' => Auth::user()->id,
                'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
                'input_of_page' => PrecheckComment::INPUT_OF_PAGE_SHIKI,
            ]
        );
    }

    /**
     * Get data precheck products
     *
     * @param array $id
     * @return array
     */
    public function getPrecheckProductUnique(array $id): array
    {
        $data = $this->repository->getPrecheckProductUnique($id);

        return $data;
    }

    /**
     * Get data precheck products
     *
     * @param array $id
     * @return array
     */
    public function getPrecheckProductSimilar(array $id): array
    {
        $data = $this->repository->getPrecheckProductSimilar($id);

        return $data;
    }

    /**
     * Insert data edit precheck unique (admin seki)
     *
     * @param $request
     */
    public function insertDataEditPrecheckUnique($request)
    {
        $resultPrecheckKeepData = $this->precheckKeepDataService->updateOrCreate(
            ['precheck_id' => $request->precheck_id],
            [
                'precheck_id' => $request->precheck_id,
                'comment_from_ams_identification' => $request->content1,
                'comment_internal' => $request->content2,
                'step' => 1
            ]
        );
        $countProductIds = count($request->m_product_id);
        $arrResultPrecheckKeepDataProdIds = [];

        $mProductIds = $request->m_product_id;
        $checkLock = $request->check_lock;
        foreach ($mProductIds as $productId) {
            if (!empty($checkLock[(int) $productId])) {
                $checkLock[(int) $productId] = 1;
            } else {
                $checkLock[(int) $productId] = 0;
            }
        }
        for ($i = 0; $i < $countProductIds; $i++) {
            $resultPrecheckKeepDataProd = $this->precheckKeepDataProdService->updateOrCreate(
                [
                    'precheck_keep_data_id' => $resultPrecheckKeepData->id,
                    'm_product_id' => $request->m_product_id[$i]
                ],
                [
                    'precheck_keep_data_id' => $resultPrecheckKeepData->id,
                    'm_product_id' => $request->m_product_id[$i]
                ]
            );
            $arrResultPrecheckKeepDataProdIds[] = $resultPrecheckKeepDataProd->id;
            foreach ($request->m_code_id[$request->m_product_id[$i]] as $mCodeId) {
                $this->precheckKeepDataProdResultService->updateOrCreate([
                    'precheck_keep_data_id' => $resultPrecheckKeepData->id,
                    'precheck_keep_data_prod_id' => $resultPrecheckKeepDataProd->id,
                    'm_code_id' => (int) $mCodeId,
                    'm_product_id' => $request->m_product_id[$i],
                ], [
                    'precheck_keep_data_id' => $resultPrecheckKeepData->id,
                    'precheck_keep_data_prod_id' => $resultPrecheckKeepDataProd->id,
                    'is_block_identification' => $checkLock[(int) $request->m_product_id[$i]],
                    'is_decision_draft' => (int) $request->is_decision_draft[$i],
                    'is_decision_edit' => (int) $request->is_decision_edit[$i],
                    'm_product_id' => $request->m_product_id[$i],
                    'm_code_id' => (int) $mCodeId,
                    'admin_id' => Auth::user()->id
                ]);
                if ($request->is_decision_draft[$i] == 0 && $request->is_decision_edit[$i] == 1) {
                    $this->precheckKeepDataProdResultService->updateOrCreate([
                    'precheck_keep_data_id' => $resultPrecheckKeepData->id,
                        'precheck_keep_data_prod_id' => $resultPrecheckKeepDataProd->id,
                        'm_code_id' => (int) $mCodeId,
                        'm_product_id' => $request->m_product_id[$i],
                    ], [
                        'result_identification_detail_edit' => $request->result_identification_detail_edit[$i],
                        'result_identification_detail_final' => $request->result_edit[$i],
                    ]);
                } elseif ($request->is_decision_draft[$i] == 0 && $request->is_decision_edit[$i] == 0) {
                    $this->precheckKeepDataProdResultService->updateOrCreate([
                        'precheck_keep_data_id' => $resultPrecheckKeepData->id,
                        'precheck_keep_data_prod_id' => $resultPrecheckKeepDataProd->id,
                        'm_code_id' => (int) $mCodeId,
                        'm_product_id' => $request->m_product_id[$i],
                    ], [
                        'result_identification_detail_edit' => $request->result_identification_detail_edit[$i],
                    ]);
                } elseif ($request->is_decision_draft[$i] == 1 && $request->is_decision_edit[$i] == 0) {
                    $this->precheckKeepDataProdResultService->updateOrCreate([
                        'precheck_keep_data_id' => $resultPrecheckKeepData->id,
                        'precheck_keep_data_prod_id' => $resultPrecheckKeepDataProd->id,
                        'm_code_id' => (int) $mCodeId,
                        'm_product_id' => $request->m_product_id[$i],
                    ], [
                        'result_identification_detail_final' => $request->result_edit[$i],
                        'result_identification_detail_edit' => $request->result_identification_detail_edit[$i],
                    ]);
                }
            }
        }
        Session::put(SESSION_GO_TO_A021RUI_SHU, [
            'precheck_keep_data_prod_ids' => $arrResultPrecheckKeepDataProdIds,
            'precheck_keep_data' => $resultPrecheckKeepData
        ]);
    }

    /**
     * GetPrecheckProductConfirm
     *
     * @param array $ids
     * @return mixed
     */
    public function getPrecheckProductConfirm(array $ids)
    {
        return $this->repository->getPrecheckProductConfirm($ids);
    }

    /**
     * Send notice of check precheck confirm (admin seki)
     *
     * @param $trademark
     */
    public function sendNoticeOfPrecheckConfirm($trademark, $precheck)
    {

        //update notice detail of row 74, columns G,H
        $redirectPageOfNoticeDetailPeriou = route('admin.precheck.view-precheck-confirm', $trademark->id);
        $redirectPageOfNoticeDetailPeriou = str_replace(request()->root(), '', $redirectPageOfNoticeDetailPeriou);
        $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'completion_date' => null,
        ], ['notice'])->get()
            ->where('notice.flow', Notice::FLOW_PRECHECK)
            ->where('notice.trademark_id', $trademark->id)
            ->map(function ($item) {
                $item->update([
                    'completion_date' => now(),
                ]);
            });

        // Send Notice Seki
        $this->noticeService->sendNotice([
            'notices' => [
                'flow' => Notice::FLOW_PRECHECK,
                'user_id' => $trademark->user_id,
                'trademark_id' => $trademark->id,
            ],
            'notice_details' => [
                // Send Notice user
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'content' => 'プレチェックサービス：AMSからのレポート',
                    'target_page' => route('admin.precheck.view-precheck-confirm', [
                        'id' => $trademark->id,
                        'precheck_id' => $precheck->id,
                    ]),
                    'redirect_page' => route('user.precheck.application-trademark', [
                        'id' => $trademark->id,
                        'precheck_id' => $precheck->id,
                    ]),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'is_answer' => NoticeDetail::IS_NOT_ANSWER,
                ],
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'content' => 'プレチェックサービス：AMSからのレポート',
                    'target_page' => route('admin.precheck.view-precheck-confirm', [
                        'id' => $trademark->id,
                        'precheck_id' => $precheck->id,
                    ]),
                    'redirect_page' => route('user.precheck.application-trademark', [
                        'id' => $trademark->id,
                        'precheck_id' => $precheck->id,
                    ]),
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                ],
            ],
        ]);
    }

    /**
     * Insert data to precheck precheck keep data (a021shiki_shu)
     *
     * @param $request
     * @param $checkLock
     */
    public function inserDataPrecheckKeepData($request)
    {
        // Save comment
        $resultPrecheckKeepData = $this->precheckKeepDataService->updateOrCreate(
            ['precheck_id' => (int) $request->precheck_id],
            [
                'precheck_id' => $request->precheck_id,
                'comment_from_ams_similar' => $request->content1,
                'comment_internal' => $request->content2,
                'step' => 2
            ]
        );

        // Save Product
        $results = $request->result ?? [];
        $resultData = $request->result_data ?? [];

        foreach ($results as $result) {
            $codeID = $result['code_id'] ?? null;
            $codeName = $result['code_name'] ?? null;
            $precheckProductID = $result['precheck_product_id'] ?? null;
            $mProductID = $result['m_product_id'] ?? null;
            $isRegisterProduct = $result['is_register_product'] ?? null;

            foreach ($resultData as $detailCodeName => $editData) {
                if ($codeName == $detailCodeName) {
                    $resultSimilarDetailEdit = $editData['result_similar_detail_edit'] ?? null;
                    $resultEdit = $editData['result_edit'] ?? null;
                    $isDecisionDraft = $editData['is_decision_draft'] ?? null;
                    $isDecisionEdit = $editData['is_decision_edit'] ?? null;
                    $precheckKeepDataProdResultID = $editData['precheck_keep_data_prod_result_id'] ?? null;
                    $resultSimilarDetailPresent = $editData['result_similar_detail_present'] ?? null;
                    $precheckKeepDataProdID = $editData['precheck_keep_data_prod_id'] ?? null;
                    $checkLock = !empty($editData['check_lock']) ? true : false;

                    $resultPrecheckKeepDataProd = $this->precheckKeepDataProdService->updateOrCreate(
                        [
                            'precheck_keep_data_id' => $resultPrecheckKeepData->id,
                            'm_product_id' => $mProductID,
                        ],
                        [
                            'precheck_keep_data_id' => $resultPrecheckKeepData->id,
                            'm_product_id' => $mProductID,
                        ]
                    );
                    $precheckKeepDataProdResult = $this->precheckKeepDataProdResultService->updateOrCreate(
                        [
                            'precheck_keep_data_id' => $resultPrecheckKeepData->id,
                            'precheck_keep_data_prod_id' => $resultPrecheckKeepDataProd->id,
                            'm_code_id' => $codeID,
                            'm_product_id' => $mProductID,
                        ],
                        [
                            'is_block_similar' => $checkLock,
                            'is_decision_similar_draft' => $isDecisionDraft,
                            'is_decision_similar_edit' => $isDecisionEdit,
                            'precheck_keep_data_prod_id' => $resultPrecheckKeepDataProd->id,
                            'precheck_keep_data_id' => $resultPrecheckKeepData->id,
                            'admin_id' => Auth::user()->id,
                            'm_code_id' => $codeID,
                            'm_product_id' => $mProductID,
                        ]
                    );

                    if ($isDecisionDraft == 0 && $isDecisionEdit == 1) {
                        $precheckKeepDataProdResult->update([
                            'result_similar_detail_edit' => $resultSimilarDetailEdit,
                            'result_similar_detail_final' => $resultEdit,
                        ]);
                    } elseif ($isDecisionDraft == 0 && $isDecisionEdit == 0) {
                        $precheckKeepDataProdResult->update([
                            'result_similar_detail_edit' => $resultSimilarDetailEdit,
                        ]);
                    } elseif ($isDecisionDraft == 1 && $isDecisionEdit == 0) {
                        $precheckKeepDataProdResult->update([
                            'result_similar_detail_edit' => $resultSimilarDetailEdit,
                        ]);
                    }
                }
            }
        }
    }

    /**
     * Insert data to precheck result (confirm of a021rui_shu)
     *
     * @param $request
     * @param $precheck
     * @return RedirectResponse
     */
    public function insertDataToEditPrecheckResult($request, $precheck)
    {
        $precheckKeepData = $this->precheckKeepDataService->findByCondition(['precheck_id' => $precheck->id])->first();
        $precheckkeepDataProdResult = PrecheckKeepDataProdResult::where('precheck_keep_data_id', $precheckKeepData->id)->get();
        $precheckProducts = PrecheckProduct::where('precheck_id', $precheck->id)->get();

        foreach ($precheckkeepDataProdResult as $value) {
            $precheckProduct = $precheckProducts->where('m_product_id', $value->m_product_id)->first();

            $precheckResult = $this->precheckResultService->updateOrCreate([
                'precheck_id' => $precheck->id,
                'precheck_product_id' => $precheckProduct->id,
                'm_code_id' => (int) $value->m_code_id,
            ], [
                'precheck_id' => (int) $request->precheck_id,
                'precheck_product_id' => $precheckProduct->id,
                'm_code_id' => (int) $value->m_code_id,
                'admin_id' => Auth::user()->id,
                'is_block_similar' => $value->is_block_similar,
            ]);

            if ($value->is_decision_draft == 0) {
                $precheckResult->update([
                    'result_identification_detail' => $value->result_identification_detail_edit,
                ]);
            }

            if ($value->is_decision_similar_draft == 0) {
                $precheckResult->update([
                    'result_similar_detail' => $value->result_similar_detail_edit,
                ]);
            }
        }

        if ($precheckKeepData->precheck_id == $request->precheck_id) {
            $this->precheckCommentService->updateOrCreate(
                [
                    'precheck_id' => (int) $request->precheck_id,
                    'type' => PrecheckComment::TYPE_COMMENT_INTERNAL,
                    'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
                ],
                [
                    'content' => $precheckKeepData->comment_internal ? $precheckKeepData->comment_internal : '',
                    'precheck_id' => $precheck->id,
                    'admin_id' => Auth::user()->id,
                    'type' => PrecheckComment::TYPE_COMMENT_INTERNAL,
                    'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
                ],
            );
            $this->precheckCommentService->updateOrCreate(
                [
                    'precheck_id' => (int) $request->precheck_id,
                    'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
                    'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
                ],
                [
                    'content' => $precheckKeepData->comment_from_ams_similar ? $precheckKeepData->comment_from_ams_similar : '',
                    'precheck_id' => $precheck->id,
                    'admin_id' => Auth::user()->id,
                    'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
                    'input_of_page' => PrecheckComment::INPUT_OF_PAGE_RUI_AND_KAN,
                ],
            );
            $this->precheckCommentService->updateOrCreate(
                [
                    'precheck_id' => (int) $request->precheck_id,
                    'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
                    'input_of_page' => PrecheckComment::INPUT_OF_PAGE_SHIKI,
                ],
                [
                    'content' => $precheckKeepData->comment_from_ams_identification ? $precheckKeepData->comment_from_ams_identification : '',
                    'precheck_id' => $precheck->id,
                    'admin_id' => Auth::user()->id,
                    'type' => PrecheckComment::TYPE_COMMENT_SEND_CUSTOMERS,
                    'input_of_page' => PrecheckComment::INPUT_OF_PAGE_SHIKI,
                ],
            );
        }
        Session::forget(SESSION_GO_TO_A021RUI_SHU);

        $precheckKeepDataProd = $this->precheckKeepDataProdService
            ->findByCondition(['precheck_keep_data_id' => $precheckKeepData->id])->pluck('id')->toArray();
        if ($precheckKeepDataProd) {
            foreach ($precheckKeepDataProd as $value) {
                $this->precheckKeepDataProdResultService->findByCondition(['precheck_keep_data_prod_id' => $value])->delete();
            }
            $this->precheckKeepDataProdService->findByCondition(['precheck_keep_data_id' => $precheckKeepData->id])->delete();
            $this->precheckKeepDataService->findByCondition(['precheck_id' => $request->precheck_id])->delete();
        }
    }

    /**
     * Send notice of edit check similar (admin seki)
     *
     * @param $trademark
     */
    public function sendNoticeOfEditPrecheckSimilar($trademark, $precheck)
    {

        // Update Notice at a201a (No 104: I J)
        $targetPage = route('admin.precheck.check-similar', [
            'id' => $trademark->id,
        ]);
        $targetPage = str_replace(request()->root(), '', $targetPage);

        $this->noticeDetailService->findByCondition([
            'type_acc' => NoticeDetail::TYPE_SUPERVISOR,
            'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
            'completion_date' => null,
        ], ['notice'])->get()
            ->where('notice.flow', Notice::FLOW_PRECHECK)
            ->where('notice.trademark_id', $trademark->id)
            ->map(function ($item) {
                $item->update([
                    'completion_date' => now(),
                ]);
            });
        $redirectPage = route('user.precheck.application-trademark', [
            'id' => $trademark->id,
            'precheck_id' => $precheck->id,
        ]);
        // Send Notice Seki
        $this->noticeService->sendNotice([
            'notices' => [
                'flow' => Notice::FLOW_PRECHECK,
                'user_id' => $trademark->user_id,
                'trademark_id' => $trademark->id,
            ],
            'notice_details' => [
                // U-000top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'content' => 'プレチェックサービス：AMSからのレポート',
                    'target_page' => route('admin.precheck_select.view-edit-precheck-similar', [
                        'id' => $trademark->id,
                        'precheck_id' => $precheck->id,
                    ]),
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_TOP,
                    'is_action' => NoticeDetail::IS_ACTION_TRUE,
                    'is_answer' => NoticeDetail::IS_NOT_ANSWER,

                ],
                // U-000anken_top
                [
                    'target_id' => $trademark->user_id,
                    'type_acc' => NoticeDetail::TYPE_USER,
                    'content' => 'プレチェックサービス：AMSからのレポート',
                    'target_page' => route('admin.precheck_select.view-edit-precheck-similar', [
                        'id' => $trademark->id,
                        'precheck_id' => $precheck->id,
                    ]),
                    'redirect_page' => $redirectPage,
                    'type_notify' => NoticeDetail::TYPE_NOTIFY_TODO,
                    'type_page' => NoticeDetail::TYPE_PAGE_ANKEN_TOP,
                ],
            ],
        ]);
    }


    /**
     * Get modal data precheck products
     *
     * @param object $precheckSimple
     * @param object $precheckSelect
     * @return array
     */
    public function getPrecheckProductModal(object $precheckSimple, object $precheckSelect): array
    {
        $precheckNewSecond = $this->repository->getPrecheckProductModal($precheckSimple, $precheckSelect);

        return $precheckNewSecond;
    }

    /**
     * Get data product register
     *
     * @param int $trademarkid
     * @return array
     */
    public function getPrecheckProductRegister(int $trademarkid)
    {

        return $this->repository->getPrecheckProductRegister($trademarkid);
    }

    /**
     * Get data product register
     *
     * @param int $trademarkid
     * @return array
     */
    public function getPrecheckProductIsRegister(int $trademarkId)
    {
        return $this->repository->getPrecheckProductIsRegister($trademarkId);
    }

    /**
     * Get data product register
     *
     * @param object $productNotRegister
     * @param object $productRegister
     * @return object
     */
    public function getDataNotRegister(object $productNotRegister, object $productRegister)
    {
        foreach ($productNotRegister as $key => $value2) {
            $productRegister->search(function ($value) use ($value2, $productNotRegister, $key) {
                if ($value2->m_product_id == $value->m_product_id) {
                    unset($productNotRegister[$key]);
                }
            });
        }

        return $productNotRegister;
    }
}
