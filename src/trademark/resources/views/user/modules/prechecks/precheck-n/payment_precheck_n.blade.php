@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.user_common_payment.title') }}</h2>
        {!! \App\Helpers\FlashMessageHelper::getMessage(request()) !!}
        @if ($message = Session::get('success'))
            <div class="alert alert-success message-booking">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <p class="">{{ $message }}</p>
            </div>
        @endif
        @if ($message = Session::get('error'))
            <div class="alert alert-danger message-booking">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <p class="">{{ $message }}</p>
            </div>
        @endif
        @if($message = Session::get('message'))
            <!-- Trigger/Open The Modal -->
            <div id="message_modal" style="display: block" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <p>{{ $message }}</p>
                    <div class="d-flex justify-content-center">
                        <button id="btn_ok" > <a href="{{ route('user.top')}}"> {{ __('labels.back') }} </a> </button>
                    </div>
                </div>
            </div>
        @endif
        <form id="payment_form" action="{{ route('user.payment.payment.store', ['s' => $key_session])}}" method="POST">
            @csrf
            <p>お申込み内容をご確認のうえ、「決定」ボタンを押してください。</p>
            @include('user.modules.common.partials.trademark_info_pay', [
               'data' => $data ?? null
           ])

            <h3>{{ __('labels.u031b.title_table_product_choose') }}</h3>

            <table class="normal_b mw640 eol">
                <tr>
                    <th>{{ __('labels.u031b.m_distinctions') }}</th>
                    <th>{{ __('labels.u031b.m_product') }}</th>
                </tr>
                @foreach ($mProductChoose as $keyCode => $products)
                    @foreach ($products as $keyItem => $item)
                        <tr>
                            @if ($keyItem == 0)
                                <td rowspan="{{ $products->count() > 1 ? $products->count() : '' }}">{{ $keyCode }}</td>
                            @endif
                            <td class="boxes">{{ $item->name }}</td>
                        </tr>
                    @endforeach
                @endforeach

                <tr>
                    <td colspan="2" class="right">{{ __('labels.u031b.num_dis') }}{{ $mProductChoose->count() }}　{{ __('labels.u031b.num_prod') }}{{ $productsCount }}</td>
                </tr>
            </table>

            <div class="">
                <h3>{{ __('labels.list_change_address.cart.text_3') }}</h3>
                <table class="normal_b mw640">
                    <tr>
                        <td>{{ __('labels.box_cart.cost_service_base') }}</td>
                        <td class="right">
                            <span class="cost_service_base">{{ number_format($data['infoBill']->cost_service_base) }}</span>{{ __('labels.円') }}
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('labels.box_cart.cost_service_add_prod') }}</td>
                        <td class="right">
                            <span class="cost_service_add_prod">{{ number_format($data['infoBill']->cost_service_add_prod) }}</span>{{ __('labels.円') }}
                        </td>
                    </tr>
                    @if ($data['m_nation_id'] == NATION_JAPAN_ID && $data['payment_type'] == \App\Models\Precheck::TYPE_PRECHECK_DETAILED_REPORT)
                        <tr class="tr_cost_bank_transfer">
                            <td>{{ __('labels.payment_table.cost_bank_transfer') }}</td>
                            <td class="right">
                                <span class="cost_bank_transfer">{{ number_format($data['infoBill']->cost_bank_transfer) }}</span>{{ __('labels.円') }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <th class="right">{{ __('labels.precheck.subtotal') }}</th>
                        <th class="right">
                            <strong style="font-size:1.2em;">
                                <span class="subtotal">{{ number_format($data['infoBill']->subtotal) }}</span>{{ __('labels.円') }}
                            </strong>
                        </th>
                    </tr>
                    <tr>
                        <th class="right" colspan="2">
                            <div>{{ __('labels.precheck.action_free') }}<span class="commission">{{ number_format($data['infoBill']->commission) }}</span>{{ __('labels.円') }}<br /></div>
                            @if ($data['m_nation_id'] == NATION_JAPAN_ID)
                                <div class="info-tax">{{ __('labels.support_first_times.consumption_tax') }}（<span>{{ number_format($data['infoBill']->tax_percentage) }}</span>％）　<span class="tax">{{ number_format($data['infoBill']->tax) }}</span>{{ __('labels.円') }}</div>
                            @endif
                        </th>
                    </tr>
                    <tr>
                        <th class="right">{{ __('labels.precheck.total') }}</th>
                        <th class="right">
                            <span style="font-size:1.2em;">
                                <span class="total_amount">{{ number_format($data['infoBill']->total_amount) }}</span>{{ __('labels.円') }}
                            </span>
                        </th>
                    </tr>
                    @if ($data['infoBill']->total_amount > \App\Models\Payment::WITH_HOLDING_TAX_NUM)
                        <tr>
                            <th class="right">{{ __('labels.user_common_payment.withholding_tax_amount') }}</th>
                            <th class="right">
                                <span style="font-size:1.2em;">
                                    <span class="tax_withholding">{{ number_format($data['infoBill']->tax_withholding) }}</span>{{ __('labels.円') }}
                                </span>
                            </th>
                        </tr>
                    @endif
                    <tr>
                        <th class="right">{{ __('labels.precheck.payment_amount') }}</th>
                        <th class="right">
                            <span style="font-size:1.2em;">
                                <span class="payment_amount">{{ number_format($data['infoBill']->payment_amount) }}</span>{{ __('labels.円') }}
                            </span>
                        </th>
                    </tr>
                </table>
            </div>

            <p class="red">{{ __('labels.box_cart.note_box_cart') }}</p>

            <p class="eol">
                <label>{{ __('labels.user_common_payment.confirmed') }}<span class="red">*</span>
                    <input type="checkbox" name="is_confirm"/>
                </label>
            </p>

            <ul class="footerBtn clearfix">
                <li><button type="button" id="back_to_previous" onclick="history.back()" class="btn_a" >{{ __('labels.back') }}</button></li>
                <li><button type="submit" class="btn_b" >{{ __('labels.decision') }}</button></li>
            </ul>

        </form>

    </div><!-- /contents -->
    <!-- /contents -->
@endsection

@section('footerSection')
    <style>
        .default_li {
            list-style: disc;
        }
        .list_prod {
            margin-left: 1.75rem;
            margin-bottom: 1rem;
        }
    </style>
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script>
        const errorMessageRequired = '{{ __('messages.common.errors.Common_E025') }}';
        validation('#payment_form', {
            'is_confirm': {
                required: true
            }
        }, {
            is_confirm: {
                required: errorMessageRequired
            }
        })
    </script>
@endsection
