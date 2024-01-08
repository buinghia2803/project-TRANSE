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
        <form id="payment_form" action="{{ route('user.payment.payment.store')}}" method="POST">
            @csrf
            <p>{{ __('labels.precheck.after_confirming_the_content') }}</p>
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
                            <span class="cost_service_base">{{ number_format($infoBill->cost_service_base) }}</span>{{ __('labels.円') }}
                        </td>
                    </tr>
                    <tr>
                        <td>{{ __('labels.box_cart.cost_service_add_prod') }}</td>
                        <td class="right">
                            <span class="cost_service_add_prod">{{ number_format($infoBill->cost_service_add_prod) }}</span>{{ __('labels.円') }}
                        </td>
                    </tr>
                    @if ($infoPayment->m_nation_id == NATION_JAPAN_ID && $infoPayment->payment_type == \App\Models\Precheck::TYPE_PRECHECK_DETAILED_REPORT)
                    <tr class="tr_cost_bank_transfer">
                        <td>{{ __('labels.box_cart.cost_bank_transfer') }}</td>
                        <td class="right">
                            <span class="cost_bank_transfer">{{ number_format($infoBill->cost_bank_transfer) }}</span>{{ __('labels.円') }}
                        </td>
                    </tr>
                    @endif
                    <tr>
                        <th class="right">{{ __('labels.box_cart.subtotal') }}</th>
                        <th class="right">
                            <strong style="font-size:1.2em;">
                                <span class="subtotal">{{ number_format($infoBill->subtotal) }}</span>{{ __('labels.円') }}
                            </strong>
                        </th>
                    </tr>
                    <tr>
                        <th class="right" colspan="2">
                            <div>{{ __('labels.precheck.action_free') }}<span class="commission">{{ number_format($infoBill->commission) }}</span>{{ __('labels.円') }}<br /></div>
                            @if ($infoPayment->m_nation_id == NATION_JAPAN_ID)
                                <div class="info-tax">{{ __('labels.support_first_times.consumption_tax') }}（<span>{{ number_format($infoBill->tax_percentage) }}</span>％）　<span class="tax">{{ number_format($infoBill->tax) }}</span>{{ __('labels.円') }}</div>
                            @endif
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
                <li><button type="button" id="back_to_previous" class="btn_a" >{{ __('labels.back') }}</button></li>
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
    $("#back_to_previous").on("click", function (){
        history.back();
    });
</script>
@endsection
