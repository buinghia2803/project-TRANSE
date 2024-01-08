@extends('user.layouts.app')
@section('main-content')
<style>
    .delete-file {
        cursor: pointer;
        opacity: .5;
        font-weight: 700;
        margin-left: 5px;
    }
</style>
<!-- contents -->
<div id="contents" class="normal">
    @include('compoments.messages')

    <form id="form" action="{{ route('user.refusal.materials.post', ['id' => $comparisonTrademarkResult->id, 'trademark_plan_id' => $trademarkPlan->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- Trademark table --}}
        @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_04, OVER_04B, OVER_05]))
            @include('user.modules.common.content', [
                'trademark' => $comparisonTrademarkResult->trademark,
            ])
        @endif

        <h2>{{ __('labels.u204.h2') }}</h2>

        <h3>{{ __('labels.refusal_plans.trademark_info') }}</h3>
        @include('user.components.trademark-table', [
            'table' => $trademarkTable
        ])
        {{-- Trademark table --}}
        <dl class="w20em clearfix middle mt-2">
            <dt>
                {{ __('labels.u204.dt') }}
                {{ CommonHelper::formatTime($comparisonTrademarkResult->sending_noti_rejection_date) }}
            </dt>
            <dd>
                <input type="button" {{ $isBlockScreen ? 'disabled' : '' }} value="{{ __('labels.u204.input_1') }}" class="btn_b" id="{{ $isBlockScreen ? '' : 'click_file_pdf' }}"/>
                @foreach ($trademarkDocuments as $ele_a)
                    <a hidden href="{{ asset($ele_a) }}" class="click_ele_a" target="_blank">{{ $ele_a }}</a>
                @endforeach
            </dd>
        </dl>
        <p>
            {{ __('labels.u204.p') }}
            {{ CommonHelper::formatTime($comparisonTrademarkResult->response_deadline) }}
        </p>

        <p class="fs12">
            {{ __('labels.u204.p_1') }}<br />
            {{ __('labels.u204.p_2') }}<br />
            {{ __('labels.u204.p_3') }}<br />
            {{ __('labels.u204.p_4') }}
        </p>

        @if (!in_array($comparisonTrademarkResult->trademark->block_by, [OVER_03, ALERT_01, OVER_04, OVER_04B, OVER_05]))
            <p class="red">
                {{ __('labels.u204.p_5') }}<br />
                {{ __('labels.u204.p_6') }}
            </p>
            <p class="eol"><input type="button" onclick="window.location='{{ route('user.refusal.extension-period.alert', ['id' => $trademark->id]) }}'" value="{{ __('labels.u204.input_8') }}" class="btn_b" /></p>
        @endif

        <dl class="w16em clearfix">
            <dt style="margin-bottom: 0;">
                <h3><strong>{{ __('labels.u204.strong') }}</strong></h3>
            </dt>
            <dd style="margin-bottom: 0;">
                <h3><strong>{{ !empty($trademarkPlan->sending_docs_deadline) ? CommonHelper::formatTime($trademarkPlan->sending_docs_deadline) : '' }}</strong></h3>
            </dd>
        </dl>

        @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_03, ALERT_01]))
            @include('user.modules.common.content', [
                'trademark' => $comparisonTrademarkResult->trademark,
            ])
        @endif

        @foreach ($plans as $key => $plan)
            <hr />
            <h3>
                {{ __('labels.u204.h3', [
                    'attr1' => ++$loop->index,
                    'attr2' => $plan->reason_name,
                ]) }}
            </h3>
            @foreach ($plan->planDetails->where('is_choice', 1) as $item)
                @php
                    $isTypePlan = $item->getTypePlanName();
                    $planDetailDocs = $item->planDetailDocs;
                @endphp
                <div class="js-scrollable eol">
                    <table class="normal_b mw740">
                        <tr>
                            <th style="width: 70%;">{{ __('labels.u204.th_1') }}</th>
                            <th>
                                {{ __('labels.u204.th_2') }}<br />
                                {{ __('labels.u204.th_3') }}
                            </th>
                            <th>{{ __('labels.u204.th_4') }}</th>
                        </tr>
                        <tr>
                            <td>
                                {{ $item->plan_description }}
                            </td>
                            <td class="center">
                                {{ $item->getTextRevolutionV2() }}<br />
                                {{ $item->getTextRevolution() }}
                            </td>
                            <td class="center">
                                {{ $isTypePlan }}
                            </td>
                        </tr>
                    </table>
                </div>

                @if ($isTypePlan == __('labels.a203c_rui.requirement'))
                    <h3>{{ __('labels.u204.h3_1') }}</h3>
                    <div class="js-scrollable mb10">
                        <table class="normal_b mw780">
                            <tr>
                                <th>{{ __('labels.u204.th_5') }}</th>
                                <th class="em40">{{ __('labels.u204.th_6') }}</th>
                                <th>{{ __('labels.u204.th_7') }}</th>
                            </tr>
                            @foreach ($planDetailDocs as $keyPlanDetailDoc => $planDetailDoc)
                                <tr>
                                    <td>
                                        {{ $planDetailDoc->MTypePlanDoc->name ?? '' }}
                                        @if ($planDetailDoc->MTypePlanDoc && $planDetailDoc->MTypePlanDoc->url != null)
                                            <input type="button" value="{{ __('labels.u204.input_2') }}" class="btn_c click_btn_ele_download" />
                                            <a hidden href="{{ asset($planDetailDoc->MTypePlanDoc->url) }}" class="click_ele_download" target="_blank">{{ asset($planDetailDoc->MTypePlanDoc->url) }}</a>
                                            <a hidden href="{{ asset('common/images/u204jigyo.pdf') }}" class="click_ele_download" target="_blank">{{ asset('common/images/u204jigyo.pdf') }}</a>
                                        @endif
                                    </td>
                                    <td>{{ $planDetailDoc->doc_requirement_des }}</td>
                                    <td>
                                        <input type="button" value="{{ __('labels.u204.input_3') }}" class="btn_b mb05 btn_upload" /><br />
                                        <input type="file" class="file_upload" hidden multiple data-plan_id="{{ $plan->id }}" data-plan_detail_doc_id="{{ $planDetailDoc->id }}">
                                        @php
                                            $idx = 0;
                                        @endphp
                                        <div class="attach-group">
                                            @if(isset($requiredDocumentDetails[$planDetailDoc->id]) && $requiredDocumentDetails[$planDetailDoc->id])
                                                @foreach ($requiredDocumentDetails[$planDetailDoc->id]?? [] as $reqDocDetail)
                                                    @foreach (json_decode($reqDocDetail->attachment_user) ?? [] as $attachmentUser)
                                                        @if ($attachmentUser->type == ATTACH)
                                                            @php
                                                                $replaceAttachmentUser = str_replace('/uploads/materials/', '', $attachmentUser->value);
                                                            @endphp
                                                            <div class="attach-item" style="display: flex">
                                                                <input type="hidden" class="data-hidden" name="data[{{ $plan->id }}][plan_detail_doc][{{ $planDetailDoc->id }}][attach][{{ $idx++ }}]" value="{{ $attachmentUser->value }}">
                                                                <span class="line line-2">{{ $replaceAttachmentUser }}</span>
                                                                <span class="delete-file" data-plan_detail_doc_id="{{ $planDetailDoc->id }}" data-file="{{ $attachmentUser->value }}">&times;</span>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                            @endif
                                        </div>

                                        @php
                                            $idx = 0;
                                        @endphp

                                        @if ($planDetailDoc->MTypePlanDoc && $planDetailDoc->MTypePlanDoc->id == 7)
                                            <div class="url-group">
                                                @if(isset($requiredDocumentDetails[$planDetailDoc->id]) && $requiredDocumentDetails[$planDetailDoc->id])
                                                    @foreach ($requiredDocumentDetails[$planDetailDoc->id]?? [] as $reqDocDetail)
                                                        @forelse (json_decode($reqDocDetail->attachment_user) ?? [] as $attachmentUser)
                                                            @if ($attachmentUser->type == URL)
                                                                <div class="url-item">
                                                                    URL <input type="text" class="em18 mb05 data-hidden input_url {{ $idx == 0 ? 'input_first' : '' }}" name="data[{{ $plan->id }}][plan_detail_doc][{{ $planDetailDoc->id }}][url][{{ $idx++ }}]" value="{{ $attachmentUser->value }}" nospace/>
                                                                </div>
                                                            @endif
                                                        @empty
                                                            <div class="url-item">
                                                                URL <input type="text" class="em18 mb05 data-hidden input_url input_first" name="data[{{ $plan->id }}][plan_detail_doc][{{ $planDetailDoc->id }}][url][0]" nospace/>
                                                            </div>
                                                        @endforelse
                                                    @endforeach
                                                @endif
                                            </div>
                                            <a href="javascript:void(0)" class="click_append" style="text-decoration: none" data-plan_id="{{ $plan->id }}" data-plan_detail_doc_id="{{ $planDetailDoc->id }}">+</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>

                    <p class="eol">
                        {{ __('labels.u204.AMS') }}<br />
                        <textarea class="normal content data-hidden" name="data[{{ $plan->id }}][content]" >{{ $plan->content_plan_doc_cmt->content ?? null }}</textarea>
                    </p>
                @endif
            @endforeach
        @endforeach
        @if (Route::is(['user.refusal.materials.index']))
            <ul class="footerBtn clearfix">
                <li><input type="submit" name="submit" value="{{ __('labels.u204.input_4') }}" class="btn_b" /></li>
            </ul>
            <ul class="footerBtn clearfix">
                <li><input type="submit" name="draft" value="{{ __('labels.u204.input_5') }}" class="btn_a" /></li>
            </ul>
        @elseif (Route::is(['user.refusal.materials.confirm.index']))
            <ul class="footerBtn clearfix">
                <li><input type="submit" name="submit_confirm" value="{{ __('labels.submit') }}" class="btn_b" /></li>
            </ul>
            <ul class="footerBtn clearfix">
                <li><input type="submit" name="draft_confirm" value="{{ __('labels.u204.input_7') }}" class="btn_a" /></li>
            </ul>
        @endif
    </form>
</div>
<!-- /contents -->
@endsection
@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        const routeAjaxMaterial = '{{ route('user.refusal.materials.ajax') }}';
        const routeAjaxMaterialDelete = '{{ route('user.refusal.materials.ajax_delete') }}';
        const correspondence_U204_E004 = '{{ __('messages.general.correspondence_U204_E004') }}';
        const Common_E028 = '{{ __('messages.general.Common_E028') }}';
        const Import_A000_E001 = '{{ __('messages.general.Import_A000_E001') }}';
        const Common_E001 = '{{ __('messages.general.Common_E001') }}';
        const Common_E026 = '{{ __('messages.general.Common_E026') }}';
        const correspondence_U204_E005 = '{{ __('messages.general.correspondence_U204_E005') }}';
        const routeConfirm = @JSON(Route::is(['user.refusal.materials.confirm.index']));
        const isBlockScreen = @json($isBlockScreen);
        const trademarkPlanId = @json(request()->__get('trademark_plan_id'));
        const requiredDoc = @json($requiredDoc);
    </script>
    <script type="text/JavaScript" src="{{ asset('end-user/refusal/material.js') }}"></script>
@endsection
