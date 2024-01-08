<?php

namespace App\Exports\Admin;

use Maatwebsite\Excel\Concerns\FromCollection;
use App\Models\Payment;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    /**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        $dataExport = $this->data;
        $data = [];
        foreach ($dataExport as $item) {
            $data[] = [
                '0' => $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('Y/m/d') : '',
                '1' => $item->trademark && $item->trademark->user ? $item->trademark->user->info_name : '',
                '2' => $item->payerInfo ? $item->payerInfo->payer_name : '',
                '3' => $item->trademark ? $item->trademark->trademark_number : '',
                '4' => $item->invoice_number,
                '5' => $item->payment_amount ? '¥'.number_format($item->payment_amount) : '',
                '6' => $item->payment_date ? '（ク）'.\Carbon\Carbon::parse($item->payment_date)->format('Y/m/d') : '',
                '7' => $item->treatment_date ? \Carbon\Carbon::parse($item->treatment_date)->format('Y/m/d') : '',
                '8' => $item->comment,
                '9' => ($item->trademark && $item->trademark->isTrademarkLetter()) ?
                    $item->trademark->name_trademark : __('labels.payment_all.view_more') . ': ' . route('admin.application-detail.index', $item->trademark->id),
            ];
        }
        return collect($data);
    }
    public function headings(): array
    {
        return [
            __('labels.payment_all.created_at'),
            __('labels.payment_all.会員名'),
            __('labels.payment_all.payer_info_payer_name'),
            __('labels.payment_all.申込番号'),
            __('labels.payment_all.請求書番号'),
            __('labels.payment_all.請求金額'),
            __('labels.payment_all.payment_date'),
            __('labels.payment_all.処理日'),
            __('labels.payment_all.備考'),
            __('labels.payment_all.商標名'),
        ];
    }
}
