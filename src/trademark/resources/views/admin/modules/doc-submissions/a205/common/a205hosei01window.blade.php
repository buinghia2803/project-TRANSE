<!-- contents inner -->
<div class="clearfix" style="text-align:left;padding-bottom:40px;">
    <div class="wide">
        <h4>{!! __('labels.a205hosei01window.procedural_amendment') !!}</h4>

        <h4>{!! __('labels.a205hosei01window.amendment') !!}</h4>
        <dl class="w14em clearfix">

            <dt>{!! __('labels.a205hosei01window.name_of_document') !!}</dt>
            <dd>{{  __('labels.a205hosei01window.regis_app') }}</dd>

            <dt>{!! __('labels.a205hosei01window.target_item') !!}</dt>
            <dd>{{  __('labels.a205hosei01window.goods_and_services') }}</dd>

            <dt>{!! __('labels.a205hosei01window.method') !!}</dt>
            <dd>{{  __('labels.a205hosei01window.change') }}</dd>
        </dl>

        <h5 style="margin-bottom:1em;">{!! __('labels.a205hosei01window.correction') !!}<br/>
            　{!! __('labels.a205hosei01window.designated_goods') !!}</h5>

        @foreach ($dataCommonA205Hosei01['data_products'] as $mDistinctionName => $itemData)
            <p class="mb00">
                　【{{ __('labels.support_first_times.No') }}{{ $mDistinctionName }}{{ __('labels.support_first_times.kind') }}
                】</p>
            <dl class="w14em clearfix">
                <dt>{!! __('labels.a205hosei01window.product_and_distintion') !!}</dt>
                <dd>{{ $itemData->implode('name', ', ') }}</dd>
            </dl>
        @endforeach

        @if($dataCommonA205Hosei01['slag_show_total_amount'])
            <h4>{!! __('labels.a205hosei01window.info_payment') !!}</h4>
            <dl class="w14em eol clearfix">
                @if($dataCommonA205Hosei01['deposit_type'] == \App\Models\Agent::DEPOSIT_TYPE_ADVENCE)
                    <dt>{!! __('labels.a205hosei01window.deposit_account_number') !!}</dt>
                    <dd>{{ $dataCommonA205Hosei01['deposit_account_number'] }}</dd>
                @elseif($dataCommonA205Hosei01['deposit_type'] == \App\Models\Agent::DEPOSIT_TYPE_CREDIT)
                    <dt>{!! __('labels.a205hosei01window.deposit_account_number_v2') !!}</dt>
                    <dd>　</dd>
                @endif

                <dt>{!! __('labels.a205hosei01window.total_amount') !!}</dt>
                <dd>{{ number_format($dataCommonA205Hosei01['total_amount']) ?? 0 }}</dd>
            </dl>
        @endif

        @include('admin.layouts.footer', ['class' => 'footer-custom'])
    </div>
</div>
