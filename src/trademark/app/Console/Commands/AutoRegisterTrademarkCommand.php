<?php

namespace App\Console\Commands;

use App\Models\AppTrademark;
use App\Models\MPriceList;
use App\Models\Payment;
use App\Models\RegisterTrademark;
use App\Models\Trademark;
use App\Models\User;
use App\Notices\PaymentNotice;
use App\Services\PaymentService;
use App\Services\PayerInfoService;
use App\Services\AppTrademarkProdService;
use App\Services\RegisterTrademarkService;
use App\Services\RegisterTrademarkProdService;
use App\Services\TrademarkInfoService;
use App\Repositories\PaymentProductRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoRegisterTrademarkCommand extends Command
{
    protected PaymentService                    $paymentService;
    protected PayerInfoService                  $payerInfoService;
    protected TrademarkInfoService              $trademarkInfoService;
    protected RegisterTrademarkService          $registerTrademarkService;
    protected AppTrademarkProdService           $appTrademarkProdService;
    protected RegisterTrademarkProdService      $registerTrademarkProdService;
    protected PaymentProductRepository          $paymentProductRepository;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:auto-register-trademark';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        PaymentService                    $paymentService,
        PayerInfoService                  $payerInfoService,
        TrademarkInfoService              $trademarkInfoService,
        AppTrademarkProdService           $appTrademarkProdService,
        RegisterTrademarkService          $registerTrademarkService,
        RegisterTrademarkProdService      $registerTrademarkProdService,
        PaymentProductRepository          $paymentProductRepository
    )
    {
        parent::__construct();
        $this->trademarkInfoService = $trademarkInfoService;
        $this->paymentService = $paymentService;
        $this->payerInfoService = $payerInfoService;
        $this->appTrademarkProdService = $appTrademarkProdService;
        $this->registerTrademarkService = $registerTrademarkService;
        $this->paymentProductRepository = $paymentProductRepository;
        $this->registerTrademarkProdService = $registerTrademarkProdService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            $trademarks = DB::select('
            SELECT
                trademarks.*,
                register_trademarks.id AS register_trademark_id,
                app_trademarks.id as app_trademark_id,
                app_trademarks.pack,
                maching_results.pi_dd_date
            FROM trademarks
                JOIN app_trademarks ON trademarks.id = app_trademarks.trademark_id
                JOIN register_trademarks ON register_trademarks.trademark_id = trademarks.id
                JOIN maching_results ON maching_results.trademark_id = trademarks.id
                WHERE register_trademarks.is_register = '. RegisterTrademark::IS_NOT_REGISTER .'
                AND NOW() > DATE_ADD(maching_results.pi_dd_date , INTERVAL 21 DAY)
                AND app_trademarks.pack != '. AppTrademark::PACK_A .'
            ');
            $setting = $this->paymentService->getSetting();
            $regisProcedureServiceFee = $this->paymentService->getPeriodRegistrationRepository(MPriceList::REGISTRATION, MPriceList::REGISTRATION_UP_3_PRODS);
            $productAddOnFee = $this->paymentService->getSystemFee(MPriceList::REGISTRATION, MPriceList::REGISTRATION_EACH_3_PRODS);
            foreach ($trademarks as $trademark) {
                $model = new Trademark();
                $model->fill(json_decode(json_encode($trademark), true));
                $matchingResult = $model->getMatchingResultFrmDocName(N_FLOW_TYPE_REGISTRATION);
                if (!$matchingResult) {
                    continue;
                }

                $registerTrademark = $this->registerTrademarkService->find($trademark->register_trademark_id);
                $trademarkInfo = $this->trademarkInfoService->findByCondition(['trademark_id', $trademark->id])->orderBy('id', SORT_BY_DESC)->first();

                $registerTrademark->update([
                    'trademark_info_id' => $trademarkInfo->id,
                    'info_type_acc' => $trademarkInfo->type_acc,
                    'trademark_info_nation_id' => $trademarkInfo->m_nation_id,
                    'trademark_info_address_first' => $trademarkInfo->m_prefecture_id,
                    'trademark_info_address_second' => $trademarkInfo->address_second,
                    'trademark_info_address_three' => $trademarkInfo->address_three,
                    'trademark_info_name' => $trademarkInfo->name,
                    'is_payment' => RegisterTrademark::IS_PAYMENT,
                    'is_register' => RegisterTrademark::IS_REGISTER,
                    'is_confirm' => RegisterTrademark::IS_PAYMENT
                ]);
                $appTrademarkProds = $this->appTrademarkProdService->findByCondition([
                    'app_trademark_id' => $trademark->app_trademark_id,
                    'is_apply' => true
                ])->get();
                foreach ($appTrademarkProds as $appTrademarkProd) {
                    $this->registerTrademarkProdService->updateOrCreate([
                        'register_trademark_id' => $registerTrademark->id,
                    ], [
                        'app_trademark_prod_id' => $appTrademarkProd->id,
                        'is_apply' => $appTrademarkProd->is_apply ?? false,
                        'm_product_id' => $appTrademarkProd->m_product_id ?? 0,
                    ]);
                }
                $totalProduct = $appTrademarkProds->count();
                $costServiceBase = ($regisProcedureServiceFee->base_price + $regisProcedureServiceFee->base_price * ($setting->value / 100));
                $totalProductAddOn = $totalProduct - 3 > 0 ? $totalProduct - 3 : 0;
                $costProductAddOn = ($productAddOnFee['cost_service_base'] ?? 0) * ceil($totalProductAddOn / 3);
                $commission = $regisProcedureServiceFee->base_price + $productAddOnFee['commission'] * ceil($totalProductAddOn / 3);
                $tax = $regisProcedureServiceFee->base_price + $productAddOnFee['commission'] * ceil($totalProductAddOn / 3);
                $totalAmount = $costServiceBase + $costProductAddOn;
                $payerInfo = $this->payerInfoService->findByCondition([
                    'target_id' => $registerTrademark->id,
                    'type' => TYPE_TRADEMARK_REGISTRATION
                ])->orderBy('id', SORT_BY_DESC)->first();

                $payment = $this->paymentService->updateOrCreate([
                    'from_page' => U302,
                    'type' => TYPE_TRADEMARK_REGISTRATION,
                    'tax_withholding' => 0,
                    'payment_amount' => 0,
                    'payment_status' => Payment::STATUS_PAID,
                    'cost_service_base' => $costServiceBase ?? 0,
                    'subtotal' => $totalAmount ?? 0,
                    'tax' => $tax ?? 0,
                    'commission' => $commission ?? 0,
                    'total_amount' => $totalAmount ?? 0,
                    'cost_bank_transfer' => null,
                    'payer_info_id' => $payerInfo->id,
                    'trademark_id' => $trademark->id,
                    'reduce_number_distitions' => $totalProductAddOn ?? 0,
                    'target_id' => $registerTrademark->id,
                ]);

                $registerTrademarkProds = $this->registerTrademarkProdService->findByCondition([
                    'register_trademark_id' => $registerTrademark->id,
                    'is_apply' => true
                ])->get();

                foreach ($registerTrademarkProds as $registerTrademarkProd) {
                    $this->paymentProductRepository->updateOrCreate([
                        'payment_id' => $payment->id,
                        'm_product_id' => $registerTrademarkProd->m_product_id,
                    ], [
                        'payment_id' => $payment->id,
                        'm_product_id' => $registerTrademarkProd->m_product_id,
                    ]);
                }

                $paymentNotice = App::make(PaymentNotice::class);
                $paymentNotice->setTrademark($model);
                $paymentNotice->setCurrentUser(User::find($model->user_id));
                $paymentNotice->setData([]);

                $paymentNotice->noticeU302Auto(['matching_result' => $matchingResult, 'register_trademark_id' => $registerTrademark->id]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
        }
    }
}
