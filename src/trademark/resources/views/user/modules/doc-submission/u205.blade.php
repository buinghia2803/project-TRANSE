@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <form>
            @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_04, OVER_04B, OVER_05]))
                @include('user.modules.common.content', [
                    'trademark' => $comparisonTrademarkResult->trademark,
                ])
            @endif

            <h2>{{ __('labels.u205.title') }}</h2>

            <h3>{{ __('labels.refusal_plans.trademark_info') }}</h3>
            <div class="info mb10">
                {{-- Trademark table --}}
                @include('user.components.trademark-table', [
                    'table' => $trademarkTable,
                ])
            </div>
            <!-- /info -->

            <dl class="w20em clearfix middle">
                <dt>{{ __('labels.u205.sending_noti_rejecttion_date') }}{{ $comparisonTrademarkResult->parseSendingNotiRejecttionDate() }}
                </dt>
                <dd>
                    <input type="button" value="{{ __('labels.a205_common.reply_reason_refusal.btn.open_file') }}"
                        class="btn_b" id="openAllFileAttach" />
                </dd>
            </dl>
            <p>{{ __('labels.u205.response_deadline') }}{{ $comparisonTrademarkResult->parseResponseDeadline() }}</p>

            @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_03, ALERT_01]))
                @include('user.modules.common.content', [
                    'trademark' => $comparisonTrademarkResult->trademark,
                ])
            @endif

            <p>{{ __('labels.u205.text_1') }}</p>

            <dl class="w16em clearfix">
                <dt>{{ __('labels.a205_common.trademark_info.content') }}</dt>
                <dd>{{ __('labels.u205.content_1') }}</dd>
                <dt>{{ __('labels.a205_common.trademark_info.trademark_number') }}</dt>

                <dd>{{ $trademarkInfo['trademark_number'] }}
                </dd>
                {{-- <dt>{{ __('labels.a205_common.trademark_info.application_date') }}</dt>
                <dd>{{ $trademarkInfo['application_date'] }}
                </dd> --}}
                <dt>{{ __('labels.a205_common.trademark_info.content_2') }}</dt>
                <dd>{{ __('labels.a205_common.trademark_info.content_3') }}
                </dd>
            </dl>
            <p>{{ __('labels.a205_common.trademark_info.content_4') }}</p>
            <dl class="w16em clearfix">
                <dt>{{ __('labels.a205_common.trademark_info.application_number') }}</dt>
                <dd>{{ $trademarkInfo['application_number'] }}
                </dd>
            </dl>
            <p>{{ __('labels.a205_common.trademark_info.content_5_v2') }}</p>
            <dl class="w16em clearfix">
                <dt>{{ __('labels.a205_common.trademark_info.address') }}</dt>
                <dd>{{ $trademarkInfo['prefecture_name'] ?? '' }}{{ $trademarkInfo['address_second'] ?? '' }}{{ $trademarkInfo['address_three'] ?? '' }}
                </dd>
                <dt>{{ __('labels.a205_common.trademark_info.trademark_info_name') }}</dt>
                <dd>{{ $trademarkInfo['trademark_info_name'] }}
                </dd>
            </dl>
            <p>{{ __('labels.a205_common.trademark_info.agent_title') }}</p>
            <dl class="w16em clearfix">
                <dt>{{ __('labels.a205_common.trademark_info.identification_number') }}</dt>
                <dd>{{ $trademarkInfo['identification_number'] }}
                </dd>
                <dt>{{ __('labels.a205_common.trademark_info.content_6') }}</dt>
                <dd>&nbsp;</dd>
                <dt>{{ __('labels.a205_common.trademark_info.agent_name') }}</dt>
                <dd>{{ $trademarkInfo['agent_name'] }}
                </dd>
                <br>
                <dt>{{ __('labels.a205_common.trademark_info.pi_dispatch_number') }}</dt>
                <dd>{{ $trademarkInfo['pi_dispatch_number'] }}
                </dd>
            </dl>
            <h4>{{ __('labels.u205.text_2') }}</h4>
            <dl class="w14em clearfix">

                <dt>{{ __('labels.u205.text_3') }}</dt>
                <dd>{{ __('labels.u205.text_4') }}</dd>

                <dt>{{ __('labels.u205.text_5') }}</dt>
                <dd>{{ __('labels.u205.text_6') }}</dd>

                <dt>{{ __('labels.u205.text_7') }}</dt>
                <dd>{{ __('labels.u205.text_8') }}</dd>

            </dl>
            <h5 style="margin-bottom:1em;">{{ __('labels.u205.text_9') }}</h5>

            @foreach ($dataCommon['data_products'] as $nameDistinct => $dataProduct)
                <p class="mb00">
                    {{ __('labels.u205.name_distinct', ['attr' => $nameDistinct]) }}</p>
                <dl class="w14em clearfix">
                    <dt>{{ __('labels.u205.name_product') }}</dt>
                    <dd>
                        @foreach ($dataProduct as $key => $mProduct)
                            {{ $dataProduct->count() > 1 ? $mProduct->name . ($key < $dataProduct->count() - 1 ? ',' : '') : $mProduct->name }}
                        @endforeach
                    </dd>
                </dl>
            @endforeach

            @if(isset($dataCommon['slag_show_total_amount']) && $dataCommon['slag_show_total_amount'])
                <p></p>
                <h4>{{ __('labels.u205.title_deposit_account_number') }}</h4>
                <dl class="w14em eol clearfix">
                    @if($dataCommon['deposit_type'] == \App\Models\Agent::DEPOSIT_TYPE_ADVENCE)
                        <dt>{{ __('labels.u205.deposit_account_number') }}</dt>
                        <dd>{{ $dataCommon['deposit_account_number'] }}</dd>
                    @elseif($dataCommon['deposit_type'] == \App\Models\Agent::DEPOSIT_TYPE_CREDIT)
                        <dt>{{ __('labels.u205.deposit_account_number_v2') }}</dt>
                        <dd>　</dd>
                    @endif

                    <dt>{{ __('labels.u205.total_amount') }}</dt>
                    <dd>{{ $dataCommon['total_amount'] }}</dd>
                </dl>
            @endif

            <hr />
            <dl class="w16em clearfix">
                <dt>{{ __('labels.a205_common.trademark_info.content') }}</dt>
                <dd>{{ __('labels.a205_common.trademark_info.content_1') }}</dd>
                <dt>{{ __('labels.a205_common.trademark_info.trademark_number') }}</dt>
                <dd>{{ $trademarkInfo['trademark_number'] }}</dd>
                <dt>{{ __('labels.a205_common.trademark_info.application_date') }}</dt>
                <dd>{{ !empty($trademarkInfo['application_date']) ? mb_convert_kana(\CommonHelper::formatTime($trademarkInfo['application_date'], 'Ee年n月j日'), 'N') : '' }}</dd>
                <dt>{{ __('labels.a205_common.trademark_info.content_2') }}</dt>
                <dd>{{ __('labels.a205_common.trademark_info.content_3') }}</dd>
            </dl>
            <p>{{ __('labels.a205_common.trademark_info.content_4') }}</p>
            <dl class="w16em clearfix">
                <dt>{{ __('labels.a205_common.trademark_info.application_number') }}</dt>
                <dd>{{ $trademarkInfo['application_number'] }}</dd>
            </dl>
            <p>{{ __('labels.a205_common.trademark_info.content_5_v2') }}</p>
            <dl class="w16em clearfix">
                <dt>{{ __('labels.a205_common.trademark_info.address') }}</dt>
                <dd>{{ $trademarkInfo['prefecture_name'] ?? '' }}{{ $trademarkInfo['address_second'] ?? '' }}{{ $trademarkInfo['address_three'] ?? '' }}
                </dd>
                <dt>{{ __('labels.a205_common.trademark_info.trademark_info_name') }}</dt>
                <dd>{{ $trademarkInfo['trademark_info_name'] }}</dd>
            </dl>
            <p>{{ __('labels.a205_common.trademark_info.agent_title') }}</p>
            <dl class="w16em clearfix">
                <dt>{{ __('labels.a205_common.trademark_info.identification_number') }}</dt>
                <dd>{{ $trademarkInfo['identification_number'] }}</dd>
                <dt>{{ __('labels.a205_common.trademark_info.content_6') }}</dt>
                <dd>&nbsp;</dd>
                <dt>{{ __('labels.a205_common.trademark_info.agent_name') }}</dt>
                <dd>{{ $trademarkInfo['agent_name'] }}</dd>
                <br>
                <dt>{{ __('labels.a205_common.trademark_info.pi_dispatch_number') }}</dt>
                <dd>{{ $trademarkInfo['pi_dispatch_number'] }}</dd>
            </dl>

            <h4>{{ __('labels.u205.description_written_opinion') }}</h4>
            <dl class="w16em clearfix">
                <dt> </dt>
                <dd>
                    <div class="mb05 white-space-pre-line">{{ $docSubmission->is_written_opinion == IS_NOT_WRITTEN_OPINION
                        ? $docSubmission->description_written_opinion
                        : __('labels.a205shu02_window.opinion_not_required')}}</div>
                </dd>
            </dl>

            @if($docSubmissionAttachProperties && $docSubmissionAttachProperties->count() > 0)
                <h4>{{ __('labels.u205.text_11') }}</h4>
                <dl class="w16em eol clearfix">
                    @foreach ($docSubmissionAttachProperties as $docSubmissionAttachPropertie)
                        <dt>{{ __('labels.u205.doc_sub_attach_propertie_name') }}</dt>
                        <dd>{{ $docSubmissionAttachPropertie->name }}</dd>
                    @endforeach
                </dl>
                <h4>{{ __('labels.u205.text_12') }}</h4>
                <dl class="w16em eol clearfix">
                    @foreach ($docSubmissionAttachProperties as $docSubmissionAttachPropertie)
                        <dt>{{ __('labels.u205.doc_sub_attach_propertie_name') }}</dt>
                        <dd>{{ $docSubmissionAttachPropertie->name }}</dd>

                        <dt>{{ __('labels.u205.doc_sub_attach') }}</dt>
                        @foreach ($docSubmissionAttachPropertie->docSubmissionAttachments as $docSubmissionAttachment)
                            <dd>
                                <div class="image_box">
                                    <img src="{{ asset($docSubmissionAttachment->attach_file) }}" /><br />
                                </div>
                            </dd>
                        @endforeach
                    @endforeach
                </dl>
            @endif

            <div class="eol"></div>

            <ul class="footerBtn clearfix">
                <li>
                    <a href="{{ route('user.application-detail.index', ['id' => $trademark->id]) }}"
                        class="btn_a">{{ __('labels.u205.back') }}</a>
                </li>
            </ul>
        </form>
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
    <script>
        const trademarkDocument = @json($trademarkDocuments);

        openAllFileAttach(trademarkDocument);
    </script>
@endsection
