@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            <form action="{{ route('admin.refusal.create-request.update_data_alert', ['id' => $id]) }}" id="form"
                method="post">
                @csrf
                <div class="info mb20" id="info">
                    {{-- Trademark table --}}
                    @include('admin.components.includes.trademark-table', [
                        'table' => $trademarkTable,
                    ])
                </div>
                <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->
                <h3>{{ __('labels.a210.text_1') }}</h3>

                <dl class="w12em clearfix">
                    <dt>{{ __('labels.a210.text_2') }}</dt>
                    <dd>{{ __('labels.a210.text_3') }}</dd>

                    <dt>{{ __('labels.a210.trademark_number') }}</dt>
                    <dd>{{ $data['trademark_number'] }}</dd>

                    <dt>{{ __('labels.a210.date_click') }}</dt>
                    <dd>{{ __('labels.a205_common.trademark_info.html_auto_date') }}</dd>

                    <dt>{{ __('labels.a210.text_4') }}</dt>
                    <dd>{{ __('labels.a210.text_5') }}</dd>
                </dl>


                <h4>{{ __('labels.a210.text_6') }}</h4>
                <dl class="w12em clearfix">
                    <dt>{{ __('labels.a210.application_number') }}</dt>
                    <dd>{{ $data['application_number'] }}</dd>
                </dl>
                <h4>{{ __('labels.a210.text_7') }}</h4>
                <dl class="w12em clearfix">
                    <dt>{{ __('labels.a210.address') }}</dt>
                    <dd>{{ $data['nation_name'] }}{{ $data['prefecture_name'] }}{{ $data['address_second'] }}{{ $data['address_three'] }}</dd>

                    <dt>{{ __('labels.a210.trademark_info_name') }}</dt>
                    <dd>{{ $data['trademark_info_name'] }}</dd>
                </dl>
                <h4>{{ __('labels.a210.text_8') }}</h4>
                <dl class="w12em clearfix">
                    <dt>{{ __('labels.a210.identification_number') }}</dt>
                    <dd>{{ $data['identification_number'] }}</dd>

                    <dt>{{ __('labels.a210.text_9') }}</dt>
                    <dd>&nbsp;</dd>

                    <dt>　{{ __('labels.a210.agent_name') }}</dt>
                    <dd>{{ $data['agent_name'] }}</dd>
                </dl>
                <dl class="w12em clearfix">
                    <dt>{{ __('labels.a210.pi_dispatch_number') }}</dt>
                    <dd>{{ $data['pi_dispatch_number'] }}</dd>

                    <dt>{{ __('labels.a210.text_10') }}</dt>
                    <dd>{{ __('labels.a210.text_11') }}</dd>
                </dl>

                @if(isset($data['print_fee']) && $data['print_fee'] > 0)
                    <h4>{{ __('labels.a210.text_12') }}</h4>
                    <dl class="w12em clearfix">
                        @if($data['deposit_type'] == \App\Models\Agent::DEPOSIT_TYPE_ADVENCE)
                            <dt>{{ __('labels.a210.deposit_account_number') }}</dt>
                            <dd>{{ $data['deposit_account_number'] }}</dd>
                        @elseif($data['deposit_type'] == \App\Models\Agent::DEPOSIT_TYPE_CREDIT)
                            <dt>{{ __('labels.a210.deposit_account_number_v2') }}</dt>
                            <dd>　</dd>
                        @endif

                        <dt>{{ __('labels.a210.print_fee') }}</dt>
                        <dd>{{ mb_convert_kana($data['print_fee'], 'N') }}</dd>
                    </dl>
                @endif

                <ul class="footerBtn clearfix" id="footerBtn">
                    <li><input type="button" value="{{ __('labels.a210.back') }}" class="btn_a"
                            onclick="history.back()" /></li>
                    <li><input type="submit" value="{{ __('labels.a210.submit') }}" class="btn_b" /></li>
                </ul>
                @foreach ($data['register_trademark_renewals'] as $registerTrademarkRenewal)
                    <input type="hidden" name="register_trademark_renewals[]" value="{{ $registerTrademarkRenewal->id }}">
                @endforeach
                <input type="hidden" name="matching_result_id" value="{{ $data['matching_result_id'] }}">
                <input type="hidden" name="from_page" value="{{ A210Alert }}">
            </form>
        </div>
        <!-- /contents inner -->
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
    @if (Request::get('type') == 'view')
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', ['only' => [ROLE_OFFICE_MANAGER]])
@endsection
