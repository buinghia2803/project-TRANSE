<table class="normal_b mb10 column1">
    <tr>
        <th>{{ __('labels.payment_all.created_at') }}</th>
        <th>{{ __('labels.payment_all.user_info_name') }}</th>
        <th>{{ __('labels.payment_all.payer_info_payer_name') }}</th>
        <th>{{ __('labels.payment_all.申込番号/請求書番号') }}</th>
        <th>{{ __('labels.payment_all.請求金額') }}</th>
        <th>{{ __('labels.payment_all.入金日/処理日') }}</th>
        <th>{{ __('labels.payment_all.未処理に戻す') }}</th>
        <th>{{ __('labels.payment_all.備考') }}</th>
        <th>{{ __('labels.payment_all.商標名') }}</th>
    </tr>
    @if(count($payments) > 0)
        @foreach($payments as $k => $item)
            <tr class="tr-id-{{ $item->id }}">
                <td>
                    {{ $item->created_at ? \Carbon\Carbon::parse($item->created_at)->format('Y/m/d') : '' }}
                </td>
                <td nowrap>{{ $item->trademark && $item->trademark->user ? $item->trademark->user->info_name : '' }}</td>
                <td>{{ $item->payerInfo ? $item->payerInfo->payer_name : '' }}</td>
                <td>
                    @if($item->trademark)
                        <a href="{{ route('admin.application-detail.index', $item->trademark->id) }}" target="_blank">{{ $item->trademark->trademark_number }}</a><br />
                    @endif
                    <a href="{{ route('admin.quote', $item->id) }}" target="_blank">{{ $item->invoice_number }}</a></td>
                <td class="left">￥{{ $item->payment_amount ? number_format($item->payment_amount) : '' }}</td>
                <td nowrap>
                    {{ $item->payerInfo && $item->payerInfo->payment_type == $typeCreditCard ? '（ク）' : '' }}{{ $item->payment_date ? \Carbon\Carbon::parse($item->payment_date)->format('Y/m/d') : '' }}<br />
                    {{ $item->treatment_date ? \Carbon\Carbon::parse($item->treatment_date)->format('Y/m/d') : '' }}</td>
                <td>
                    @if($item->showButtonDeletePaymentAll())
                    <input type="submit" value="未処理に戻す" class="btn_a deletePayment" data-id-payment="{{ $item->id }}" />
                    @endif
                </td>
                <td>
                    {{ $item->comment }}
                </td>
                <td style="min-width: 168px">
                    @if ($item->trademark)
                        @if($item->trademark->isTrademarkLetter())
                            {{ $item->trademark->name_trademark}}
                        @else
                            <a href="{{ route('admin.application-detail.index', $item->trademark->id) }}" title="{{ __('labels.payment_all.view_more') }}">{{ __('labels.payment_all.view_more') }}</a>
                        @endif
                    @endif

                </td>
            </tr>
        @endforeach
    @else
      <tr>
          <td colspan="9"><p style="text-align:center">{{ __('messages.not_data') }}</p></td>
      </tr>
    @endif
</table>
{{ $payments->appends(Request()->all())->links() }}
