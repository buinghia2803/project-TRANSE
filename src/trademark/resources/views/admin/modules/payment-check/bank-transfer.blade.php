@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        @include('compoments.messages')
        <!-- contents inner -->
        <div class="wide clearfix">
            <h3>{{ __('labels.payment_bank_transfer.text_1') }}</h3>
            <table class="normal_b column1">
                <thead>
                    <tr>
                        <th>{{ __('labels.payment_bank_transfer.text_2') }}</th>
                        <th>{{ __('labels.payment_bank_transfer.text_3') }}
                        </th>
                        <th>{{ __('labels.payment_bank_transfer.text_4') }}</th>
                        <th>{{ __('labels.payment_bank_transfer.text_5') }}</th>
                        <th>{{ __('labels.payment_bank_transfer.text_6') }}
                        </th>
                        <th class="em10">{{ __('labels.payment_bank_transfer.text_7') }}</th>
                        <th class="em15">{{ __('labels.payment_bank_transfer.text_8') }}</th>
                        <th>{{ __('labels.payment_bank_transfer.text_9') }}</th>
                        <th class="em15">{{ __('labels.payment_bank_transfer.text_10') }}</th>
                        <th>{{ __('labels.payment_bank_transfer.text_11') }}
                        </th>
                    </tr>
                </thead>
                @if ($listPayment->count() > 0)
                    <tbody>
                        @foreach ($listPayment as $item)
                            <form action="{{ route('admin.update-payment-bank-transfer') }}" id="form" method="POST">
                                @csrf
                                <tr
                                    style="{{ ($item->checkBackground7Day() && in_array((int) $item->type, $applyForSftAndPrecheck)) ||
                                    ($item->checkBackground3Day() && in_array((int) $item->type, $applyForPaymentTerm))
                                        ? ' background-color: #f1b5d0'
                                        : '' }}">

                                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('Y/m/d') }}</td>
                                    <td>
                                        @if (in_array((int) $item->type, $applyForSftAndPrecheck))
                                            {{ \Carbon\Carbon::parse($item->created_at)->addWeek()->format('Y/m/d') }}
                                        @elseif(in_array((int) $item->type, $applyForPaymentTerm))
                                            {{ \Carbon\Carbon::parse($item->created_at)->addDays(3)->format('Y/m/d') }}
                                        @endif
                                    </td>
                                    <td>{{ $item->trademark->user->info_name ?? '' }}</td>
                                    <td>{{ $item->payerInfo->payer_name ?? '' }}</td>
                                    <td><a href="{{ route('admin.application-detail.index', ['id' => $item->trademark->id ?? 1]) }}"
                                            target="_blank">{{ $item->trademark->trademark_number ?? '' }}</a><br />
                                        <a href="{{ route('admin.invoice', ['id' => $item->id]) }}"
                                            target="_blank">{{ $item->invoice_number ?? '' }}</a>
                                    </td>
                                    <td class="left">
                                        ￥{{ $item->payment_amount ? number_format(round($item->payment_amount)) : 0 }}<br />
                                        @if ($item->getCreatedAt() && $roleAdmin != ROLE_MANAGER)
                                            <a class="btn_a" style="padding: 5px;"
                                                href="{{ route('admin.send-mail-remind', ['email' => $item->trademark->user->email]) }}">{{ __('labels.payment_bank_transfer.text_12') }}</a>
                                        @else
                                            <input type="button" disabled value="{{ __('labels.payment.input.text_1') }}"
                                                class="btn_a" />
                                        @endif
                                    </td>

                                    <td class="center">
                                        {{ __('labels.payment_bank_transfer.text_13') }}
                                        <input type="date" id="payment_date_{{ $item->id }}"
                                            value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"class="em08 mb-2"
                                            {{ $roleAdmin != ROLE_MANAGER ? '' : 'disabled' }}><br />
                                        <input type="button" id="click_confirm_{{ $item->id }}"
                                            value="{{ __('labels.payment.input.text_2') }}" class="btn_b"
                                            {{ $roleAdmin != ROLE_MANAGER ? '' : 'disabled' }} /><br />
                                    </td>
                                    <td class="center">
                                        <input type="button" id="handle_{{ $item->id }}"
                                            value="{{ __('labels.payment.input.text_3') }}" class="btn_a"
                                            {{ $roleAdmin != ROLE_MANAGER ? '' : 'disabled' }} />
                                    </td>
                                    <td>
                                        <textarea style="width:100%;height:10em;" id="comment_{{ $item->id }}"
                                            {{ $roleAdmin != ROLE_MANAGER ? '' : 'disabled' }}></textarea>
                                        <div id="error_comment_{{ $item->id }}"></div>
                                    </td>
                                    <td>
                                        @if (isset($item->trademark->type_trademark) && $item->trademark->type_trademark == TYPE_TRADEMARK_CHARACTERS)
                                            {{ $item->trademark->name_trademark }}
                                        @else
                                            @if($roleAdmin != ROLE_MANAGER)
                                                <a href="{{ route('admin.application-detail.index', ['id' => $item->trademark->id ?? 1]) }}">{{ __('labels.payment_bank_transfer.text_14') }}</a>
                                            @else
                                                <span>{{ __('labels.payment_bank_transfer.text_14') }}</span>
                                            @endif
                                        @endif
                                    </td>
                                    {{-- Input Hidden --}}
                                    <input type="hidden" name="id" id="">
                                    <input type="hidden" name="payment_date">
                                    <input type="hidden" name="comment">
                                    <input type="hidden" name="type_submit">
                                </tr>
                            </form>
                        @endforeach
                    </tbody>
                @else
                    <tbody>
                        <tr>
                            <td colspan="10" style="text-align: center">{{ __('messages.not_data') }}</td>
                        </tr>
                    </tbody>
                @endif
            </table>
            {{ $listPayment->links() }}
            <!-- /table 支払い状況 -->
            <ul class="footerBtn clearfix">
                <li>
                    <a href="{{ route('admin.payment-check.all') }}" class="btn_c"
                        style="color: white">{{ __('labels.payment_bank_transfer.text_15') }}</a>
                </li>
            </ul>
        </div>
        <!-- /contents inner -->
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
    <script>
        const DELETE_POPUP_TITLE = '{{ __('labels.payment.delete_message') }}';
        const errorMessageMaxLength = '{{ __('messages.common.errors.Common_E024') }}';
        const YES = '{{ __('labels.payment.yes') }}';
        const NO = '{{ __('labels.payment.no') }}';
        const contentHandle = '{{ __('labels.payment.title_handle') }}';
        const contentConfirm = '{{ __('labels.payment.title_confirm') }}';
        const listPayment = @json($listPayment);
    </script>
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script src="{{ asset('admin_assets/payment-check/bank_transfer.js') }}"></script>
@endsection
