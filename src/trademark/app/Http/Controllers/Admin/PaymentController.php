<?php

namespace App\Http\Controllers\Admin;

use App\Exports\Admin\PaymentExport;
use App\Http\Controllers\Controller;
use App\Models\PayerInfo;
use App\Models\Payment;
use App\Models\Trademark;
use App\Notices\PaymentBankNotice;
use App\Services\ChangeInfoRegisterService;
use App\Services\PaymentService;
use App\Services\MailTemplateService;
use App\Services\TrademarkPlanService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\SendGeneralMailJob;
use App\Models\FreeHistory;
use App\Models\MailTemplate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $paymentService;
    private PaymentBankNotice $paymentBankNotice;
    protected MailTemplateService $mailTemplateService;
    private ChangeInfoRegisterService $changeInfoRegisterService;
    private TrademarkPlanService $trademarkPlanService;

    public function __construct(
        PaymentService $paymentService,
        ChangeInfoRegisterService $changeInfoRegisterService,
        PaymentBankNotice $paymentBankNotice,
        MailTemplateService $mailTemplateService,
        TrademarkPlanService $trademarkPlanService
    )
    {
        $this->paymentService = $paymentService;
        $this->paymentBankNotice = $paymentBankNotice;
        $this->mailTemplateService = $mailTemplateService;
        $this->changeInfoRegisterService = $changeInfoRegisterService;
        $this->trademarkPlanService = $trademarkPlanService;
        $this->middleware('permission:payment-all.viewPaymentAll')->only(['viewPaymentAll']);
        $this->middleware('permission:payment.viewBankTransfer')->only(['viewBankTransfer']);
        $this->middleware('permission:payment.sendMailRemindPayment')->only(['sendMailRemindPayment']);
        $this->middleware('permission:payment.updatePaymentBankTransfer')->only(['updatePaymentBankTransfer']);
    }

    /**
     * View payment all
     *
     * @return void
     */
    public function viewPaymentAll(Request $request)
    {
        $params = $request->all();
        $dataSession = [];
        //get data search condition
        if (!empty($params['s'])) {
            $keySession = $params['s'];
            if (Session::has($keySession)) {
                $dataSession = Session::get($keySession);
            }
        }
        $queryPayments = $this->paymentService->queryListPaymentAll($dataSession);
        if (!empty($dataSession['csv']) && $dataSession['csv'] == 'on') {
            $dataExport = $queryPayments->get();
            if ($dataExport->count($dataExport) > 0) {
                return Excel::download(new PaymentExport($dataExport), date('YmdHis') . '-' . 'payment-all.csv');
            }
        }
        unset($dataSession['csv']);
        $searchFields = Payment::getSearchFields();
        $conditionsAll = Payment::getListConditionAll();
        $conditions = Payment::getListCondition();
        $conditionDate = Payment::getListConditionDate();
        $payments = $queryPayments->paginate(PAGE_LIMIT_10);
        $typeCreditCard = PayerInfo::PAYMENT_CREATE_CARD;

        $data = [
            'payments' => $payments,
            'params' => $params,
            'searchFields' => $searchFields,
            'conditionsAll' => $conditionsAll,
            'conditions' => $conditions,
            'conditionDate' => $conditionDate,
            'dataSession' => $dataSession,
            'typeCreditCard' => $typeCreditCard,
        ];

        return view('admin.modules.payments.all', $data);
    }

    /**
     * Search condition payment all
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function searchConditionPaymentAll(Request $request)
    {
        $data = $request->all();
        $key = Str::random(11);
        $page = $data['page'] ?? 1;
        Session::put($key, $data);

        return redirect()->route('admin.payment-check.all', ['s' => $key]);
    }

    /**
     * Delete payment ajax
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updatePaymentAjax(Request $request)
    {
        if ($request->ajax()) {
            $params = $request->all();
            if (isset($params['payment_id'])) {
                $respon = $this->paymentService->updatePaymentAjax($params['payment_id']);
                if ($respon) {
                    return response()->json(['res' => $respon]);
                }
            }
        }

        return response()->json(['res' => false]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function viewBankTransfer()
    {
        $listPayment = $this->paymentService->getPaymentBankTransfer();
        $roleAdmin = Auth::user()->role;
        $applyForSftAndPrecheck = [
            Payment::TYPE_TRADEMARK,
            Payment::TYPE_SUPPORT_FIRST_TIME,
            Payment::TYPE_PRECHECK,
        ];
        $applyForPaymentTerm = [
            Payment::TYPE_REASON_REFUSAL,
            Payment::TYPE_SELECT_POLICY,
            Payment::TYPE_TRADEMARK_REGIS,
            Payment::TYPE_LATE_PAYMENT,
            Payment::RENEWAL_DEADLINE,
            Payment::CHANG_ADDRESS,
            Payment::CHANG_NAME,
            Payment::BEFORE_DUE_DATE,
        ];

        return view('admin.modules.payment-check.bank-transfer', compact('listPayment', 'applyForSftAndPrecheck', 'applyForPaymentTerm', 'roleAdmin'));
    }

    /**
     * Send Mail Remind Payment
     *
     * @param  mixed $email
     * @return void
     */
    public function sendMailRemindPayment($email)
    {
        SendGeneralMailJob::dispatch('emails.remind_payment', [
            'to' => $email,
            'subject' => __('labels.payment.mail.subject'),
        ]);

        return redirect()->back()->with('message', __('messages.payment.send_mail_success'))->withInput();
    }

    /**
     * Update Payment Bank Transfer
     *
     * @param  mixed $id
     * @return void
     */
    public function updatePaymentBankTransfer(Request $request)
    {
        try {
            DB::beginTransaction();
            $payment = $this->paymentService->find($request['id']);
            $params = [
                'is_treatment' => Payment::IS_TREATMENT_DONE,
                'payment_status' => Payment::STATUS_PAID,
                'payment_date' => $request['payment_date'] ?? null,
                'comment' => $request['comment'] ?? $payment->comment,
                'treatment_date' => now(),
                'is_send_notice' => Payment::IS_SEND_NOTICE_TRUE,
            ];

            if (isset($payment) && $payment->is_send_notice == Payment::IS_SEND_NOTICE_FALSE) {
                if ($payment->from_page == U000FREE) {
                    $this->paymentBankNotice->setData([
                        'free_history_id' => $payment->target_id,
                    ]);
                }
                $this->paymentBankNotice->notice($payment);
            }
            $payment->__unset('precheck');
            $payment->__unset('trademark');
            $this->paymentService->update($payment, $params);

            $payment->load('trademark.user', 'trademark.appTrademark');
            $params['payment'] = $payment ?? null;
            $params['from_page'] = $payment->from_page ?? null;
            if ($payment->from_page == U000FREE) {
                $params['freeHistory'] = FreeHistory::find($payment->target_id);
            }

            if (str_contains($payment->from_page, U203) && $payment->type == Payment::TYPE_SELECT_POLICY) {
                $trademarkPlanID = $payment->target_id ?? null;
                $this->trademarkPlanService->updateById($trademarkPlanID, [
                    'is_register' => IS_REGISTER_TRUE,
                    'sending_docs_deadline' => Carbon::now()->addDays(2),
                ]);
            }

            $isSendMail = $this->mailTemplateService->checkSendMailUser($params);
            if ($isSendMail && $request->type_submit == 'confirm') {
                // Send mail
                $this->mailTemplateService->sendMailRequest($params, MailTemplate::BANK_TRANSFER, MailTemplate::GUARD_TYPE_ADMIN);
            }


            DB::commit();

            return redirect()->back()->with('message', '処理済み')->withInput();
            // all good
        } catch (\Exception $e) {
            Log::error($e);
            DB::rollback();
            // something went wrong

            return redirect()->back()->with('error', __('messages.import_xml.system_error'))->withInput();
        }
    }
}
