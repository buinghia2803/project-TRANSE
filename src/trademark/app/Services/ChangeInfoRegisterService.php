<?php

namespace App\Services;

use App\Models\ChangeInfoRegister;
use App\Models\MPrefecture;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Trademark;
use App\Models\User;
use App\Repositories\AppTrademarkRepository;
use App\Repositories\ChangeInfoRegisterRepository;
use App\Repositories\PayerInfoRepository;
use App\Repositories\PaymentRepository;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Ramsey\Uuid\Type\Integer;
use SebastianBergmann\Type\NullType;

class ChangeInfoRegisterService extends BaseService
{
    protected $changeInfoRegisterRepository;
    protected $payerInfoRepository;
    protected $appTrademarkRepository;
    protected $paymentRepository;

    /**
     * Initializing the instances and variables
     *
     * @param   ChangeInfoRegisterRepository $changeInfoRegisterRepository
     */
    public function __construct(
        ChangeInfoRegisterRepository $changeInfoRegisterRepository,
        PayerInfoRepository $payerInfoRepository,
        AppTrademarkRepository $appTrademarkRepository,
        PaymentRepository $paymentRepository
    )
    {
        $this->repository = $changeInfoRegisterRepository;
        $this->payerInfoRepository = $payerInfoRepository;
        $this->appTrademarkRepository = $appTrademarkRepository;
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * Create Payment , Payer Info and Change Info Registers
     *
     * @param  array $request
     * @param  int $trademarkId
     * @return mixed
     */
    public function createPayment(array $params, int $trademarkId)
    {
        try {
            DB::beginTransaction();
            $appTrademark = $this->appTrademarkRepository
            ->findByCondition(['trademark_id' => $trademarkId], ['trademark'])
            ->first();

            $paymentTrademark = $this->paymentRepository
                ->findByCondition(['trademark_id' => $trademarkId])
                ->orderBy('id', SORT_BY_DESC)
                ->first();

            if ($params['submit_type'] == REDIRECT_TO_COMMON_PAYMENT_KENRISHA
                || $params['submit_type'] == REDIRECT_TO_COMMON_QUOTE_KENRISHA
            ) {
                $changeInfoRegisterDraft = $this->getChangeInfoRegisterKenrisha($trademarkId);
            } else {
                $changeInfoRegisterDraft = $this->getChangeInfoRegister($trademarkId);
            }

            $payerType = $params['submit_type'] == REDIRECT_TO_COMMON_PAYMENT_KENRISHA || $params['submit_type'] == REDIRECT_TO_COMMON_QUOTE_KENRISHA
                ? TYPE_LIST_CHANGE_ADDRESS_KENRISHA
                : TYPE_LIST_CHANGE_ADDRESS;
            $dataPayerInfo = [
                'target_id' => $appTrademark->id,
                'payment_type' => $params['payment_type'] ?? null,
                'payer_type' => $params['payer_type'] ?? 0,
                'm_nation_id' => $params['m_nation_id'] ?? 0,
                'payer_name' => $params['payer_name'] ?? '',
                'payer_name_furigana' => $params['payer_name_furigana'] ?? '',
                'postal_code' => $params['postal_code'] ?? null,
                'm_prefecture_id' => $params['m_prefecture_id'] ?? null,
                'address_second' => $params['address_second'] ?? '',
                'address_three' => $params['address_three'] ?? '',
                'type' => $payerType
            ];

            // Create Payer Info
            $payerInfo = $this->payerInfoRepository->create($dataPayerInfo);

            // Create Payment
            $paymentStatus = Payment::STATUS_WAITING_PAYMENT;
            $isTreatment = Payment::IS_TREATMENT_WAIT;

            $dataPayment = [
                'cost_service_base' => DEFAULT_COST_SERVICE_BASE_LIST_CHANGE_ADDRESS,
                'target_id' => $appTrademark->id,
                'trademark_id' => $trademarkId,
                'payer_info_id' => $payerInfo->id,
                'commission' => $params['commission'] ?? $paymentTrademark->commission,
                'tax' => $params['tax_input'] ?? $paymentTrademark->tax,
                'payment_status' => $paymentStatus,
                'is_treatment' => $isTreatment,
                'payment_date' => now(),
                'cost_print_address' => isset($params['is_change_address_free']) && $params['is_change_address_free'] == 1 ? $params['cost_print_address_input'] : COST_DEFAULT,
                'cost_print_name' => $params['cost_print_name_input'] ?? COST_DEFAULT,
                'from_page' => $params['from_page'] ?? '',
                'type' => TYPE_LIST_CHANGE_ADDRESS,
            ];
            // Set value for dataPayment
            $transferFee = $params['cost_bank_transfer_input'] ?? $paymentTrademark->cost_bank_transfer;
            $isChangeFree = $params['is_change_address_free'] ?? '';
            $changeNameFee = $params['cost_change_name'] ?? $paymentTrademark->cost_change_name;
            $changeAddressFee = $params['cost_change_address'] ?? $paymentTrademark->cost_change_address;
            $args = [];
            switch ($params['type_change']) {
                case TYPE_CHANGE_NAME:
                    $args = [ $isChangeFree, $changeNameFee, COST_DEFAULT ];
                    $dataPayment['cost_change_name'] = $changeNameFee;
                    $dataPayment['cost_change_address'] = 0;
                    break;
                case TYPE_CHANGE_ADDRESS:
                    $args = [ $isChangeFree, COST_DEFAULT, $changeAddressFee ];
                    $dataPayment['cost_change_address'] = $changeAddressFee;
                    $dataPayment['cost_change_name'] = 0;
                    break;
                case TYPE_CHANG_DOUBLE:
                    $args = [ $isChangeFree, $changeNameFee, $changeAddressFee ];
                    $dataPayment['cost_change_name'] = $changeNameFee;
                    $dataPayment['cost_change_address'] = $changeAddressFee;
                    break;
            }

            if ($params['payment_type'] == BANK_TRANSFER) {
                array_push($args, $transferFee);
                $dataPayment['cost_bank_transfer'] = $transferFee;
            } else {
                array_push($args, COST_DEFAULT);
            }

            if (count($args)) {
                $dataPayment['subtotal'] = $this->calculateSubtotal(...$args);
            }

            if ($params['submit_type'] == REDIRECT_TO_COMMON_PAYMENT_KENRISHA || $params['submit_type'] == REDIRECT_TO_COMMON_QUOTE_KENRISHA) {
                $dataPayment['total_amount'] = $this->calculateTotalAmount(
                    $isChangeFree,
                    $dataPayment['subtotal'],
                    $params['cost_print_address_input'],
                    $params['cost_print_name_input']
                );
            } else {
                $dataPayment['total_amount'] = $dataPayment['subtotal'];
            }

            $dataPayment['tax_withholding'] = $this->getTaxWithHolding($payerInfo, $dataPayment['total_amount']);

            $dataPayment['payment_amount'] = $dataPayment['total_amount'] - $dataPayment['tax_withholding'];
            $payment = $this->paymentRepository->create($dataPayment);

            $dataUpdatePayment = [];
            if (!$payment->quote_number) {
                $dataUpdatePayment['quote_number'] = $this->generateQIR($appTrademark->trademark->trademark_number, 'quote');
            }
            if (!$payment->invoice_number) {
                $dataUpdatePayment['invoice_number'] = $this->generateQIR('', 'invoice');
            }
            if (!$payment->invoice_number) {
                $dataUpdatePayment['receipt_number'] = $this->generateQIR('', 'receipt');
            }
            $payment->update($dataUpdatePayment);
            $trademark = Trademark::where('id', $trademarkId)->with('registerTrademark')->first();
            $registerTrademark = $trademark->registerTrademark;
            // Create Or Update Change Info Register
            if ($params['change_info_register_m_nation_id'] != NATION_JAPAN_ID) {
                   $mPrefectureId = MPrefecture::where('m_nation_id', $params['change_info_register_m_nation_id'])->first()->id;
            }
            $dataChangeInfoRegister = [
                'trademark_id' => $trademark->id,
                'payment_id' => $payment->id,
                'trademark_info_id' => $params['trademark_info_id'] ?? null,
                'register_trademark_id' => $registerTrademark->id ?? null,
                'm_nation_id' => $params['change_info_register_m_nation_id'] ?? ($changeInfoRegisterDraft->m_nation_id ?? ''),
                'm_prefectures_id' => isset($params['change_info_register_m_prefectures_id']) ?
                    $params['change_info_register_m_prefectures_id'] : ($changeInfoRegisterDraft->m_prefectures_id ?? $mPrefectureId),
                'address_second' => isset($params['trademark_infos_address_second']) ?
                    $params['trademark_infos_address_second'] : ($changeInfoRegisterDraft->address_second ?? ''),
                'address_three' => $params['trademark_infos_address_three'] ?? ($changeInfoRegisterDraft->address_three ?? ''),
                'name' => $params['name'] ?? ($changeInfoRegisterDraft->name ?? ''),
                'type' => $params['submit_type'] == REDIRECT_TO_COMMON_PAYMENT_KENRISHA ||
                    $params['submit_type'] == REDIRECT_TO_COMMON_QUOTE_KENRISHA ? REGISTER : APPLICATION,
                'is_send' => IS_SEND_FALSE,
                'is_change_address_free' => isset($params['is_change_address_free']) ?
                    ChangeInfoRegister::IS_CHANGE_ADDRESS_FREE : ChangeInfoRegister::IS_CHANGE_ADDRESS_NOT_FREE,
                'representative_name' => $params['representative_name'] ?? '',
                'type_acc' => $params['trademark_infos_type_acc'] ?? ($changeInfoRegisterDraft->type_acc ?? '')
            ];
            if ($changeInfoRegisterDraft) {
                $this->repository->update($changeInfoRegisterDraft, $dataChangeInfoRegister);
            } else {
                $changeInfoRegisterDraft = $this->repository->create($dataChangeInfoRegister);
            }

            $redirectTo = null;
            $key = Str::random(11);
            switch ($params['submit_type']) {
                case REDIRECT_TO_COMMON_PAYMENT:
                    $dataPayment['from_page'] = U000LIST_CHANGE_ADDRESS_02;
                    $dataPayment['payment_id'] = $payment->id;
                    $dataPayment['change_info_register_id'] = $changeInfoRegisterDraft->id;
                    $dataPayment['payment_type'] = $params['payment_type'] ?? Payment::CREDIT_CARD;
                    Session::put($key, $dataPayment);
                    $redirectTo = REDIRECT_TO_COMMON_PAYMENT;
                    break;
                case REDIRECT_TO_COMMON_PAYMENT_KENRISHA:
                    $dataPayment['from_page'] = U000LIST_CHANGE_ADDRESS_02_KENRISHA;
                    $dataPayment['payment_id'] = $payment->id;
                    $dataPayment['payment_type'] = $params['payment_type'] ?? Payment::CREDIT_CARD;

                    Session::put($key, $dataPayment);
                    $redirectTo = REDIRECT_TO_COMMON_PAYMENT;
                    break;
                case REDIRECT_TO_COMMON_QUOTE:
                case REDIRECT_TO_COMMON_QUOTE_KENRISHA:
                    $redirectTo = REDIRECT_TO_COMMON_QUOTE;
                    break;
            }

            DB::commit();

            return [
                'redirect_to' => $redirectTo,
                'payment_id' => $payment->id ?? '',
                'key_session' => $key,
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Calculate Subtotal
     *
     * @param  mixed $freeCostChangeAddress
     * @param  mixed $costChangeName
     * @param  mixed $costChangeAddress
     * @param  mixed $costBankTransfer
     * @return void
     */
    public function calculateSubtotal($freeCostChangeAddress, $costChangeName, $costChangeAddress, $costBankTransfer)
    {
        $subTotal = $costChangeName + $costChangeAddress + $costBankTransfer;

        return $subTotal;
    }

    /**
     * Calculate Total Amount
     *
     * @param  mixed $freeCostChangeAddress
     * @param  mixed $subTotal
     * @param  mixed $costPrintName
     * @param  mixed $costPrintAddress
     * @return void
     */
    public function calculateTotalAmount($freeCostChangeAddress, $subTotal, $costPrintName, $costPrintAddress)
    {
        if ($freeCostChangeAddress) {
            $totalAmount = $subTotal + COST_DEFAULT + $costPrintAddress;
        } else {
            $totalAmount = $subTotal + $costPrintName + $costPrintAddress;
        }

        return $totalAmount;
    }

    /**
     * Get Change Info Register
     *
     * @param  mixed $trademarkId
     * @return Model|null
     */
    public function getChangeInfoRegister($trademarkId): ?Model
    {
        return $this->repository->getChangeInfoRegister($trademarkId);
    }

    /**
     * Get Change Info Register
     *
     * @param  mixed $trademarkId
     * @return Model|null
     */
    public function getChangeInfoRegisterKenrisha($trademarkId): ?Model
    {
        return $this->repository->getChangeInfoRegisterKenrisha($trademarkId);
    }
}
