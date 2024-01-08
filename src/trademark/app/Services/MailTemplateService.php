<?php

namespace App\Services;

use App\Helpers\CommonHelper;
use App\Helpers\FileHelper;
use App\Jobs\SendMailTemplateJob;
use App\Models\MailTemplate;
use App\Models\AppTrademark;
use App\Models\FreeHistory;
use App\Models\Setting;
use App\Repositories\MailTemplateRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MailTemplateService extends BaseService
{
    /**
     * Initializing the instances and variables
     *
     * @param   MailTemplateRepository $mailTemplateRepository
     */
    public function __construct(MailTemplateRepository $mailTemplateRepository)
    {
        $this->repository = $mailTemplateRepository;
    }

    /**
     * Get type
     *
     * @return  array
     */
    public function types(): array
    {
        return $this->repository->types();
    }

    /**
     * Send mail request.
     *
     * @param array $argc.
     */
    public function sendMailRequest($argc, int $type = MailTemplate::CREDIT_CARD, int $guardType = MailTemplate::GUARD_TYPE_USER)
    {
        try {
            $params = [];
            $settings = Setting::whereIn('key', ['transfer_destination', 'payment_due_date', 'register_due_date'])->get();

            $dateRange = $settings->where('key', 'payment_due_date')->first()->value;

            $fromPage = isset($argc['payment']) && $argc['payment']->from_page ? $argc['payment']->from_page : null;

            if (str_contains($fromPage, U302_402_5YR_KOUKI)) {
                $params['from_page'] = U302_402_5YR_KOUKI;
                $argc['from_page'] = U302_402_5YR_KOUKI;
            }

            if (str_contains($fromPage, U402)) {
                $params['from_page'] = U402;
                $argc['from_page'] = U402;
            }

            if (str_contains($fromPage, U021N)) {
                $params['from_page'] = U021N;
                $argc['from_page'] = U021N;
            }

            if (str_contains($fromPage, U201_SELECT_01_N)) {
                $params['from_page'] = U201_SELECT_01;
                $argc['from_page'] = U201_SELECT_01;
            }

            $mailTemplate = $this->repository->findByCondition([
                'from_page' => $argc['from_page'] ?? '',
                'type' => $type,
                'guard_type' => $guardType,
            ])->first();

            if ($mailTemplate) {
                if (isset($argc['user']) && $argc['user']) {
                    $user = $argc['user'];
                } else if (isset($argc['payment']) && $argc['payment']) {
                    $user = $argc['payment']->trademark->user ?? null;
                } else {
                    throw new \Exception(__FILE__ . ':' . __LINE__ . ' Don\'t found user to send mail.');
                }

                $params['subject'] = $mailTemplate->subject;
                $params['to'] = $user->getListMail();
                if (isset($argc['payment']) && $argc['payment']) {
                    $params['payment_amount'] = CommonHelper::formatPrice($argc['payment']->payment_amount);
                    $params['payment_due_date'] = now()->addDays($dateRange)->format('Y/m/d');
                }
                switch ($argc['from_page']) {
                    case U201_SIMPLE:
                    case U201_SELECT_01:
                    case U000FREE:
                    case U302:
                    case U402:
                    case U302_402_5YR_KOUKI:
                        $dateRange = $settings->where('key', 'register_due_date')->first()->value;
                        $params['register_due_date'] = now()->addDays($dateRange)->format('Y/m/d');
                        break;
                    default:
                        break;
                }

                $params['transfer_destination'] = $settings->where('key', 'transfer_destination')->first()->value;

                SendMailTemplateJob::dispatch('', $params['to'], $params, null, $mailTemplate);
            } else {
                throw new \Exception('Do not found mail template.');
            }
        } catch (\Exception $e) {
            Log::error($e);
        }
    }

    /**
     * Check condition send mail of U201 cluster.
     *
     * @param array $params
     * @return bool
     */
    public function checkSendMailUser(array $params)
    {
        try {
            $result = false;
            $payment = $params['payment'] ?? null;
            $fromPage = isset($payment) && $payment->from_page ? $payment->from_page : null;

            if (str_contains($fromPage, U302_402_5YR_KOUKI)) {
                $params['from_page'] = U302_402_5YR_KOUKI;
            }
            if (str_contains($fromPage, U201_SELECT_01_N)) {
                $params['from_page'] = U201_SELECT_01_N;
            }
            if (str_contains($fromPage, U402)) {
                $params['from_page'] = U402;
            }

            if (str_contains($fromPage, U021N)) {
                $params['from_page'] = U021N;
            }

            switch ($params['from_page']) {
                case U011:
                case U011B:
                case U011B_31:
                case U021:
                case U021N:
                case U021B:
                case U021B_31:
                case U031:
                case U031B:
                case U031C:
                case U031D:
                case U031EDIT:
                case U031_EDIT_WITH_NUMBER:
                case U302:
                case U402:
                case U302_402_5YR_KOUKI:
                    $result = true;
                    break;
                case U000FREE:
                    if (isset($params['freeHistory']) && $params['freeHistory'] && $params['freeHistory']->type == FreeHistory::TYPE_4) {
                        $result = true;
                    }
                    break;
                case U201_SIMPLE:
                    $appTrademark = $payment->trademark->appTrademark;
                    if ($appTrademark->pack != AppTrademark::PACK_C) {
                        $result = true;
                    }
                    break;
                case U201_SELECT_01_N:
                case U201_SELECT_01:
                    $appTrademark = $payment->trademark->appTrademark;
                    if ($appTrademark->pack != AppTrademark::PACK_C) {
                        $result = true;
                    }
                    break;
            }
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Parse content with params
     *
     * @param string $content
     * @param array $params
     * @return string
     */
    protected function parseContent(string $content, array $params)
    {
        $pattern = '/\{\{(.*?)\}\}/';
        $data = [];
        preg_match_all($pattern, $content, $matches, PREG_SET_ORDER, 0);
        if (isset($params['payment']) && $params['payment']) {
            $data = [
                '$date' => isset($params['payment_date']) ? Carbon::parse($params['payment_date'])->format('Y/m/d') : '',
                '$amount' => CommonHelper::formatPrice($params['payment']->payment_amount),
            ];
        }
        foreach ($matches as $key => $match) {
            $content = str_replace($match[0], $data[$match[1]], $content);
        }

        return $content;
    }

    /**
     * Update record
     *
     * @param   integer $typeID
     * @param   array   $params
     * @return  boolean
     */
    public function updateRecord(int $typeID, array $params): bool
    {
        try {
            DB::beginTransaction();

            $mailTemplate = $this->repository->findByCondition([
                'type' => $typeID,
                'lang' => $params['lang'] ?? \App::getLocale(),
            ])->first();

            // Set lang default if not set
            if (empty($params['lang'])) {
                $params['lang'] = \App::getLocale();
            }

            $params['cc'] = implode(',', $params['cc'] ?? []);
            $params['bcc'] = implode(',', $params['bcc'] ?? []);

            // Upload Image
            if (!empty($params['attachment'])) {
                // Store new image
                $file = $params['attachment'];
                $image = FileHelper::uploads($file, [], LOCAL_PUBLIC_FOLDER . '/attachment');
                $params['attachment'] = $image[0]['filepath'] ?? null;

                // Unlink old image
                if (!empty($mailTemplate->image) && !empty($article->image)) {
                    FileHelper::unlink($article->image, $this->resizeImage());
                }
            }

            if ($mailTemplate != null) {
                // Unlink unused image
                if (empty($params['attachment']) && empty($params['attachment_input']) && !empty($mailTemplate->attachment)) {
                    FileHelper::unlink($mailTemplate->attachment);
                    $params['attachment'] = null;
                }

                // Update
                $mailTemplate->update($params);
            } else {
                // Create
                $this->create($params);
            }

            // Commit and return
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return false;
        }
    }
}
