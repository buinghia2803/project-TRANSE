<?php

namespace App\Jobs;

use App\Mail\MailTemplate;
use App\Models\MailTemplate as MailTemplateModel;
use App\Services\MailTemplateService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendMailTemplateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var     MailTemplateModel $mailType
     */
    protected MailTemplateModel $mailTemplate;

    /**
     * @var     string $mailType
     */
    protected string $mailType;

    /**
     * @var     string $mailTo
     */
    protected $mailTo;

    /**
     * @var     array $params
     */
    protected array $params;

    /**
     * @var     string|null $lang
     */
    protected ?string $lang;

    /**
     * Create a new job instance.
     *
     * @param   string      $mailType
     * @param   string|array $mailTo
     * @param   array       $params
     * @param   string|null $lang
     * @return  void
     */
    public function __construct(string $mailType, $mailTo, array $params = [], string $lang = null, MailTemplateModel $mailTemplate = null)
    {
        $this->mailTemplate = $mailTemplate;
        $this->mailType = $mailType;
        $this->mailTo = $mailTo;
        $this->params = $params;
        $this->lang = $lang;
    }

    /**
     * Execute the job.
     *
     * @param   MailTemplateService $mailTemplateService
     * @return  void
     */
    public function handle(MailTemplateService $mailTemplateService)
    {
        try {
            $mailToString = '';
            if (is_array($this->mailTo)) {
                $mailToString = implode(', ', $this->mailTo);
            } else {
                $mailToString = $this->mailTo;
            }

            if ($this->mailTemplate) {
                $mailTemplate = $this->mailTemplate;

                Mail::to($this->mailTo)->send(new MailTemplate($mailTemplate, $this->params));
            } else {
                $mailTemplate = $mailTemplateService->findByCondition([
                    'type' => $this->mailType,
                    'lang' => $this->lang ?? \App::getLocale(),
                ])->first();

                if ($mailTemplate != null) {
                    Mail::to($this->mailTo)->send(new MailTemplate($mailTemplate, $this->params));
                }
            }

            $fromPage = '';
            if (!empty($mailTemplate) && !empty($mailTemplate->from_page)) {
                $fromPage = '. From Page: ' . $mailTemplate->from_page;
            }

            Log::info('Send Mail Success'. $fromPage .'. Mail to: '. $mailToString);
        } catch (\Exception $e) {
            Log::info('Send Mail Error. Mail to: '. $mailToString);
            Log::error($e);
        }
    }
}
