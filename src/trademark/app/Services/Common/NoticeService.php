<?php

namespace App\Services\Common;

use App\Models\Admin;
use App\Models\NoticeDetail;
use App\Models\Trademark;
use App\Repositories\AdminRepository;
use App\Repositories\NoticeDetailBtnRepository;
use App\Repositories\NoticeDetailRepository;
use App\Repositories\NoticeRepository;
use App\Repositories\UserRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class NoticeService extends BaseService
{
    private NoticeRepository $noticeRepository;
    private AdminRepository $adminRepository;
    private NoticeDetailRepository $noticeDetailRepository;
    private NoticeDetailBtnRepository $noticeDetailBtnRepository;
    private UserRepository $userRepository;

    /**
     * Construct
     */
    public function __construct(
        NoticeRepository $noticeRepository,
        AdminRepository $adminRepository,
        UserRepository $userRepository,
        NoticeDetailRepository $noticeDetailRepository,
        NoticeDetailBtnRepository $noticeDetailBtnRepository
    )
    {
        $this->noticeRepository = $noticeRepository;
        $this->adminRepository = $adminRepository;
        $this->userRepository = $userRepository;
        $this->noticeDetailRepository = $noticeDetailRepository;
        $this->noticeDetailBtnRepository = $noticeDetailBtnRepository;
    }

    /**
     * Create all data of notice
     *
     * @param array $data
     * @return void
     */
    public function createRecord(array $data)
    {
        try {
            DB::beginTransaction();

            // Create notice
            $notice = null;
            if (!empty($data['notices'])) {
                $notice = $this->noticeRepository->create($data['notices']);
            }

            // Create Notice Detail
            $noticeDetailIds = [];
            $noticeDetails = $data['notice_details'] ?? [];
            foreach ($noticeDetails as $item) {
                $item['notice_id'] = $item['notice_id'] ?? $notice->id ?? null;

                $item['target_page'] = !empty($item['target_page']) ? str_replace(request()->root(), '', $item['target_page']) : '';
                $item['redirect_page'] = !empty($item['redirect_page']) ? str_replace(request()->root(), '', $item['redirect_page']) : null;

                $noticeDetail = $this->noticeDetailRepository->create($item);

                $noticeDetailIds[] = $noticeDetail;

                if (!empty($item['buttons']) && count($item['buttons']) > 0) {
                    foreach ($item['buttons'] as $button) {
                        $button['notice_detail_id'] = $noticeDetail->id;
                        $button['url'] = !empty($button['url']) ? str_replace(request()->root(), '', $button['url']) : null;

                        $this->noticeDetailBtnRepository->create($button);
                    }
                }
            }

            Session::put(SESSION_NOTICE, [
                'notice_id' => $notice->id ?? null,
                'notice_detail_id' => $noticeDetailIds,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }

    /**
     * Create data of notice detail
     *
     * @param array $params
     * @return void
     */
    public function createNoticeDetail(array $params)
    {
        $adminOfficeManager = $this->adminRepository->findByCondition([
            'role' => ROLE_OFFICE_MANAGER,
        ])->get();

        $adminManager = $this->adminRepository->findByCondition([
            'role' => ROLE_MANAGER,
        ])->get();

        $adminSupervisor = $this->adminRepository->findByCondition([
            'role' => ROLE_SUPERVISOR,
        ])->get();

        try {
            DB::beginTransaction();

            $noticeDetails = [];
            switch ($params['type_acc']) {
                case NoticeDetail::TYPE_OFFICE_MANAGER:
                    foreach ($adminOfficeManager as $admin) {
                        $params['target_id'] = $admin->id;
                        $noticeDetails[] = $params;
                    }
                    break;
                case NoticeDetail::TYPE_MANAGER:
                    foreach ($adminManager as $admin) {
                        $params['target_id'] = $admin->id;
                        $noticeDetails[] = $params;
                    }
                    break;
                case NoticeDetail::TYPE_SUPERVISOR:
                    foreach ($adminSupervisor as $admin) {
                        $params['target_id'] = $admin->id;
                        $noticeDetails[] = $params;
                    }
                    break;
                default:
                    $noticeDetails[] = $params;
            }
            foreach ($noticeDetails as $noticeDetail) {
                $newRecord = $this->noticeDetailRepository->create($noticeDetail);

                if (!empty($noticeDetail['buttons']) && count($noticeDetail['buttons']) > 0) {
                    foreach ($noticeDetail['buttons'] as $button) {
                        $button['notice_detail_id'] = $newRecord->id;
                        $button['url'] = !empty($button['url']) ? str_replace(request()->root(), '', $button['url']) : null;

                        $this->noticeDetailBtnRepository->create($button);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }

    /**
     * Send notice for User
     *
     * @param int $userId
     * @param array $data
     * @return void
     */
    public function sendUser(int $userId, array $data)
    {
        $user = $this->userRepository->find($userId);

        $noticeDetails = [];
        foreach ($data['notice_details'] ?? [] as $item) {
            $item['target_id'] = $user->id;
            $item['type_acc'] = NoticeDetail::TYPE_USER;

            $noticeDetails[] = $item;
        }
        $data['notice_details'] = $noticeDetails;

        $this->createRecord($data);
    }

    /**
     * Send notice for Jimu
     *
     * @param array $data
     * @return void
     */
    public function sendOfficeManger(array $data)
    {
        $admins = $this->adminRepository->findByCondition([
            'role' => ROLE_OFFICE_MANAGER,
        ])->get();

        foreach ($admins as $admin) {
            $noticeDetails = [];
            foreach ($data['notice_details'] ?? [] as $item) {
                $item['target_id'] = $admin->id;
                $item['type_acc'] = NoticeDetail::TYPE_OFFICE_MANAGER;

                $noticeDetails[] = $item;
            }
            $data['notice_details'] = $noticeDetails;

            $this->createRecord($data);
        }
    }

    /**
     * Send notice for Tantou
     *
     * @param array $data
     * @return void
     */
    public function sendManager(array $data)
    {
        $admins = $this->adminRepository->findByCondition([
            'role' => ROLE_MANAGER,
        ])->get();

        foreach ($admins as $admin) {
            $noticeDetails = [];
            foreach ($data['notice_details'] ?? [] as $item) {
                $item['target_id'] = $admin->id;
                $item['type_acc'] = NoticeDetail::TYPE_MANAGER;

                $noticeDetails[] = $item;
            }
            $data['notice_details'] = $noticeDetails;

            $this->createRecord($data);
        }
    }

    /**
     * Send notice for Seki
     *
     * @param array $data
     * @return void
     */
    public function sendSupervisor(array $data)
    {
        $admins = $this->adminRepository->findByCondition([
            'role' => ROLE_SUPERVISOR,
        ])->get();

        foreach ($admins as $admin) {
            $noticeDetails = [];
            foreach ($data['notice_details'] ?? [] as $item) {
                $item['target_id'] = $admin->id;
                $item['type_acc'] = NoticeDetail::TYPE_SUPERVISOR;

                $noticeDetails[] = $item;
            }
            $data['notice_details'] = $noticeDetails;

            $this->createRecord($data);
        }
    }

    /**
     * Send notice with type acc
     *
     * @param array $data
     * @return void
     */
    public function sendNotice(array $data)
    {
        $adminOfficeManager = $this->adminRepository->findByCondition([
            'role' => ROLE_OFFICE_MANAGER,
        ])->get();

        $adminManager = $this->adminRepository->findByCondition([
            'role' => ROLE_MANAGER,
        ])->get();

        $adminSupervisor = $this->adminRepository->findByCondition([
            'role' => ROLE_SUPERVISOR,
        ])->get();

        $noticeDetails = [];
        foreach ($data['notice_details'] ?? [] as $item) {
            switch ($item['type_acc']) {
                case NoticeDetail::TYPE_OFFICE_MANAGER:
                    foreach ($adminOfficeManager as $admin) {
                        $item['target_id'] = $admin->id;
                        $noticeDetails[] = $item;
                    }
                    break;
                case NoticeDetail::TYPE_MANAGER:
                    foreach ($adminManager as $admin) {
                        $item['target_id'] = $admin->id;
                        $noticeDetails[] = $item;
                    }
                    break;
                case NoticeDetail::TYPE_SUPERVISOR:
                    foreach ($adminSupervisor as $admin) {
                        $item['target_id'] = $admin->id;
                        $noticeDetails[] = $item;
                    }
                    break;
                default:
                    $noticeDetails[] = $item;
            }
        }
        $data['notice_details'] = $noticeDetails;

        $this->createRecord($data);
    }

    /**
     * Send notice with type acc
     *
     * @param int $flow
     * @param mixed $comment
     * @param int $trademarkID
     * @param array $options
     * @return void
     */
    public function updateComment(int $flow, $comment, int $trademarkID, array $options = [])
    {
        $trademark = Trademark::where('id', $trademarkID)->first();

        $notice = $this->noticeRepository
            ->findByCondition([])
            ->where('trademark_id', $trademark->id)
            ->where('user_id', $trademark->user_id)
            ->where('flow', $flow)
            ->orderBy('id', SORT_TYPE_DESC)
            ->with('noticeDetails')
            ->first();

        if (!empty($notice)) {
            $noticeDetails = $notice->noticeDetails;
            $filterNoticeDetails = $noticeDetails->where('type_acc', '<>', NoticeDetail::TYPE_USER)
                ->where('type_page', NoticeDetail::TYPE_PAGE_ANKEN_TOP)
                ->whereNotNull('redirect_page')
                ->where('type_notify', NoticeDetail::TYPE_NOTIFY_TODO);

            $filterNoticeDetails->map(function ($item) use ($comment) {
                $item->update([
                    'comment' => $comment,
                ]);
            });
        }
    }
}
