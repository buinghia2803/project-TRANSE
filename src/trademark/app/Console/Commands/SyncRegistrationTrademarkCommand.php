<?php

namespace App\Console\Commands;

use App\Jobs\SendGeneralMailJob;
use App\Models\AppTrademark;
use App\Models\RegisterTrademark;
use App\Services\RegisterTrademarkService;
use App\Services\Common\NoticeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SyncRegistrationTrademarkCommand extends Command
{
    protected NoticeService $noticeService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:send-mail-register-trademark';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    protected $registerTrademarkService;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(RegisterTrademarkService $registerTrademarkService, NoticeService $noticeService)
    {
        parent::__construct();
        $this->registerTrademarkService = $registerTrademarkService;
        $this->noticeService = $noticeService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            $trademarks = DB::select('
            SELECT
                trademarks.*,
                register_trademarks.id AS register_trademark_id,
                app_trademarks.pack,
                maching_results.pi_dd_date
            FROM trademarks
                JOIN app_trademarks ON trademarks.id = app_trademarks.trademark_id
                JOIN register_trademarks ON register_trademarks.trademark_id = trademarks.id
                JOIN maching_results ON maching_results.trademark_id = trademarks.id
                WHERE register_trademarks.is_register = '. RegisterTrademark::IS_NOT_REGISTER .'
                AND NOW() > register_trademarks.user_response_deadline
                AND app_trademarks.pack = '. AppTrademark::PACK_A .'
                AND register_trademarks.is_send_mail = 0
            ');

            if (count($trademarks)) {
                foreach ($trademarks as $trademark) {
                    RegisterTrademark::find($trademark->register_trademark_id)->update([
                        'is_send_mail' => true,
                    ]);
                    $user = User::find($trademark->user_id);
                    $route = route('user.apply-trademark-register', ['id' => $trademark->id]);

                    SendGeneralMailJob::dispatch('emails.response_deadline_passed', [
                        'to' => $user->getListMail(),
                        'subject' => __('labels.u302.title_mail'),
                        'link' => $route,
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }
}
