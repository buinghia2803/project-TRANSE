<?php

namespace App\Jobs;

use App\Mail\GeneralMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendGeneralMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $view;
    protected array  $params;
    protected $mailTo;

    /**
        $params = [
            'to' => 'test@relipasoft.com',
            'subject' => 'Subject String',
            'cc' => [],
            'bcc' => [],
            'attachment' => '',
            'attachments' => [],
        ];
     */

    /**
     * Create a new job instance.
     *
     * @param   string $view
     * @param   array  $params
     * @return  void
     */
    public function __construct(string $view, array $params = [])
    {
        $this->view = $view;
        $this->params = $params;
        $this->mailTo = $params['to'] ?? null;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            Mail::to($this->mailTo)->send(new GeneralMail($this->view, $this->params));
        } catch (\Exception $e) {
            Log::error('Send Email Error. View: ' . $this->view);
            Log::error($e);
        }
    }
}
