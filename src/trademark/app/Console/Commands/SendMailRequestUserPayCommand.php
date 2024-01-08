<?php

namespace App\Console\Commands;

use App\Jobs\SendGeneralMailJob;
use App\Models\Payment;
use App\Services\PaymentService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendMailRequestUserPayCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:request-user-pay';

    /**
     * Ask users to pay money
     *
     * @var string
     */
    protected $description = 'Ask users to pay money';
    protected $paymentService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $nowDate = Carbon::now();
            $nowDateSubTreeDay = Carbon::now()->subDay(3);
            $payments = Payment::where('payment_status', Payment::STATUS_WAITING_PAYMENT)
                ->where('is_treatment', Payment::IS_TREATMENT_WAIT)
                ->where('created_at', '<=', date('Y-m-d H:i', strtotime($nowDateSubTreeDay)))
                ->with('trademark', function ($q) {
                    $q->with('user:id,info_name,email');
                })
                ->get();
            //1. send mail request payment user every day
            foreach ($payments as $payment) {
                if (empty($payment->date_of_sending_payment_email)
                    || !empty($payment->date_of_sending_payment_email)
                    && Carbon::parse($payment->date_of_sending_payment_email)->format('Y-m-d') != $nowDate->format('Y-m-d')
                ) {
                    if ($payment->trademark && $payment->trademark->user) {
                        //value due date payment
                        $paymentDueDate = $payment->getValuePaymentDueDate();
                        //if nowDate < due date payment
                        if ($nowDate <= $paymentDueDate) {
                            SendGeneralMailJob::dispatch('emails.request-payment-job', [
                                'to' => $payment->trademark->user->getListMail(),
                                'subject' => __('labels.emails.subject.request_user_pay'),
                            ]);
                            $payment->update([
                                'date_of_sending_payment_email' => now(),
                            ]);
                        }
                    }
                }
            }
            //get list payment all
            $paymentAll = Payment::all();
            foreach ($paymentAll as $item) {
                //has data in comparison_trademark_results table
                if ($item->trademark && $item->trademark->comparisonTrademarkResult) {
                    //2.payment has a term to CSC && if nowDate > comparison_trademark_results.response_deadline
                    if ($nowDate->greaterThan($item->trademark->comparisonTrademarkResult->response_deadline)) {
                        $this->updatePaymentByData($item);
                    }
                } else {
                    //3.payment not has a term to CSC
                    if ($nowDate->greaterThan(Carbon::parse($item->created_at)->addMonth(3))) {
                        $this->updatePaymentByData($item);
                    }
                }
            }

            DB::commit();
            $this->info('Cron job request payment to user success');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($e);
            $this->info('Cron job request payment to user error');
        }
    }

    /**
     * Update payment by data
     *
     * @param $model
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updatePaymentByData($model)
    {
        return $this->paymentService->update($model, [
            'is_treatment' => Payment::IS_TREATMENT_DONE,
            'payment_status' => Payment::STATUS_PAID,
            'payment_date' => Carbon::now(),
            'treatment_date' => Carbon::now(),
            'comment' => $model->comment
        ]);
    }
}
