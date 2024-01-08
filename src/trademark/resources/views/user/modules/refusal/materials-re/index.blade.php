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
    <form id="form" action="{{ route('user.refusal.materials-re.post', [
            'id' => $comparisonTrademarkResult->id,
            'trademark_plan_id' => $trademarkPlan->id,
            'required_document_id' => request()->__get('required_document_id'),
            'round' => $round
        ]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_04, OVER_04B, OVER_05]))
            @include('user.modules.common.content', [
                'trademark' => $comparisonTrademarkResult->trademark,
            ])
        @endif

        <h2>{{ __('labels.u204n.h2') }}</h2>

        <h3>{{ __('labels.refusal_plans.trademark_info') }}</h3>
        {{-- Trademark table --}}
        @include('user.components.trademark-table', [
            'table' => $trademarkTable
        ])
        {{-- Trademark table --}}

        <dl class="w20em clearfix middle mt-2">
            <dt>
                {{ __('labels.u204n.dt') }}
                {{ CommonHelper::formatTime($comparisonTrademarkResult->sending_noti_rejection_date) }}
            </dt>
            <dd>
                <input type="button" value="{{ __('labels.u204n.input_1') }}" class="btn_b" id="click_file_pdf"/>
                @foreach ($trademarkDocuments as $ele_a)
                    <a hidden href="{{ asset($ele_a) }}" class="click_ele_a" target="_blank">{{ $ele_a }}</a>
                @endforeach
            </dd>
        </dl>
        <p>
            {{ __('labels.u204n.p') }}
            {{ CommonHelper::formatTime($comparisonTrademarkResult->response_deadline) }}
        </p>

        <p class="fs13">
            <strong>
                {{ __('labels.u204n.strong_1') }}<br />
                {{ __('labels.u204n.strong_2') }}
            </strong>
        </p>

        <p class="eol red">
            {{ __('labels.u204n.red_1') }}<br />
            {{ __('labels.u204n.red_2') }}<br />
            {{ __('labels.u204n.red_3') }}
            <br />
        </p>

        <dl class="w16em clearfix">
            <dt>
                <h3><strong>{{ __('labels.u204n.AMS') }}</strong></h3>
            </dt>
            <dd>
                <h3><strong>{{ !empty($requiredDocument->response_deadline) ? CommonHelper::formatTime($requiredDocument->response_deadline) : '' }}</strong></h3>
            </dd>
        </dl>

        @if (in_array($comparisonTrademarkResult->trademark->block_by, [OVER_03, ALERT_01]))
            @include('user.modules.common.content', [
                'trademark' => $comparisonTrademarkResult->trademark,
            ])
        @endif

        @if (count($plansIsNotCompleted) > 0)
            @foreach ($plansIsNotCompleted as $key => $plan)
            <hr>
                <h3>
                    {{ __('labels.u204n.h3', [
                        'attr1' => ++$key,
                        'attr2' => $plan->reason_name,
                    ]) }}
                </h3>

                <div class="js-scrollable eol">
                    <table class="normal_b mw740">
                        <tr>
                            <th style="width: 70%;">{{ __('labels.u204n.th_1') }}</th>
                            <th>
                                {{ __('labels.u204n.th_2') }}<br />
                                {{ __('labels.u204n.th_3') }}
                            </th>
                            <th>{{ __('labels.u204n.th_4') }}</th>
                        </tr>
                        <tr>
                            <td>
                                {{ $plan->plan_detail_is_choice->plan_description ?? null }}
                            </td>
                            <td class="center">
                                {{ $plan->plan_detail_is_choice->text_revolution_v2 ?? null }}<br />
                                {{ $plan->plan_detail_is_choice->text_revolution ?? null }}
                            </td>
                            <td class="center">
                                {{ $plan->plan_detail_is_choice->type_plan_name ?? null }}
                            </td>
                        </tr>
                    </table>
                </div>
                <!-- /scroll wrap -->

                <br />
                <br />
                @if ($plan->plan_detail_is_choice->is_type_plan_name)
                    <h3>{{ __('labels.u204n.h3_1') }}</h3>

                    @php
                       $requiredDocMissPlan = $requiredDocMiss->where('plan_id', $plan->id)->last() ?? null
                    @endphp
                    @if ($requiredDocMissPlan)
                        <p style="white-space: pre-line;">{{ date_format($requiredDocMissPlan->created_at, 'Y/m/d') . '　' . $requiredDocMissPlan->description_docs_miss }}</p>
                    @endif

                    <div class="js-scrollable eol">
                        <table class="normal_b mw780">
                            <tr>
                                <th style="width: 20%;">{{ __('labels.u204n.th_5') }}</th>
                                <th>{{ __('labels.u204n.th_6') }}</th>
                                <th style="width: 32%;">{{ __('labels.u204n.th_7') }}</th>
                            </tr>
                            @foreach ($plan->plan_detail_doc_is_not_completed ?? [] as $planDetailDoc)
                                @php
                                    $requiredDocumentDetail = $planDetailDoc->requiredDocumentDetail;
                                @endphp
                                @if($requiredDocumentDetail && $requiredDocumentDetail->is_completed == 0)
                                    <tr>
                                        <td>{{ $planDetailDoc->MTypePlanDoc->name ?? null }}</td>
                                        <td>{{ $planDetailDoc->doc_requirement_des ?? null }}</td>
                                        <td>
                                            @php
                                                $idx = 0;
                                            @endphp
                                            <div class="attach-group_old">
                                                {{-- File upload last time --}}
                                                @foreach ($requiredDocumentDetails[$planDetailDoc->id] ?? [] as $requiredDocumentDetail)
                                                    @foreach (json_decode($requiredDocumentDetail->attachment_user) ?? [] as $attachmentUser)
                                                        @if ($attachmentUser->type == ATTACH)
                                                            @php
                                                                $replaceAttachmentUser = str_replace('/uploads/materials/', '', $attachmentUser->value);
                                                            @endphp
                                                            <div class="attach-item" style="display: flex">
                                                                {{-- <input type="hidden" class="data-hidden" name="data[{{ $plan->id }}][plan_detail_doc][{{ $planDetailDoc->id }}][attach][{{ $idx++ }}]" value="{{ $attachmentUser->value }}"> --}}
                                                                <span class="line line-2">{{ $replaceAttachmentUser }}</span>
                                                                <span class="delete-file" data-plan_detail_doc_id="{{ $planDetailDoc->id }}" data-file="{{ $attachmentUser->value }}">&times;</span>
                                                            </div>
                                                            @if (count(json_decode($requiredDocumentDetail->attachment_user ?? '[]', true)) && $requiredDocumentDetail->from_send_doc)
                                                                @php
                                                                    $explodeFromSendDoc = explode('_', $requiredDocumentDetail->from_send_doc);
                                                                    $currentRound = request()->get('round');
                                                                @endphp
                                                                @if(isset($explodeFromSendDoc[1]) && isset($currentRound) && ($explodeFromSendDoc[1] == $currentRound))
                                                                    <p class="is_has_file" hidden></p>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                            </div>
                                            <input type="button" value="{{ __('labels.u204n.input_2') }}" class="btn_b mb05 btn_upload" /><br />
                                            <input type="file" class="file_upload" hidden multiple data-plan_id="{{ $plan->id }}" data-plan_detail_doc_id="{{ $planDetailDoc->id }}">
                                            {{-- File upload current time --}}
                                            <div class="attach-group">
                                                @foreach (json_decode($planDetailDoc->attachment_user) ?? [] as $attachmentUser)
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
                                                                        URL <input type="text" class="em18 mb05 input_url data-hidden {{ $idx == 0 ? 'input_first' : '' }}" name="data[{{ $plan->id }}][plan_detail_doc][{{ $planDetailDoc->id }}][url][{{ $idx++ }}]" value="{{ $attachmentUser->value }}" nospace/>
                                                                    </div>
                                                                @endif
                                                            @empty
                                                                <div class="url-item">
                                                                    URL <input type="text" class="em18 mb05 input_url data-hidden input_first" name="data[{{ $plan->id }}][plan_detail_doc][{{ $planDetailDoc->id }}][url][0]" nospace/>
                                                                </div>
                                                            @endforelse
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <a href="javascript:void(0)" class="click_append" style="text-decoration: none" data-plan_id="{{ $plan->id }}" data-plan_detail_doc_id="{{ $planDetailDoc->id }}">+</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            @if(!count($plan->plan_detail_doc_is_not_completed))
                            <tr>
                                <td style="text-align: center;" colspan="3">{{ __('messages.general.Common_E032') }}</td>
                            </tr>
                            @endif
                        </table>
                    </div>
                    <!-- /scroll wrap -->

                    <p class="eol">
                        {{ __('labels.u204n.AMS_content') }}<br />
                        <textarea class="normal content data-hidden" name="data[{{ $plan->id }}][content]" data-m_type_plan_id="{{ $plan->plan_detail_is_choice->mTypePlan->id ?? '' }}">{{ $plan->content_plan_doc_cmt->content ?? null }}</textarea>
                    </p>
                @endif
            @endforeach

            @if (Route::is(['user.refusal.materials-re.index']))
                <ul class="footerBtn clearfix">
                    <li><input type="submit" name="submit" value="{{ __('labels.u204n.submit') }}" class="btn_b" /></li>
                </ul>
            @elseif (Route::is(['user.refusal.materials-re.confirm.index']))
                <ul class="footerBtn clearfix">
                    <li><input type="submit" name="submit_confirm" value="{{ __('labels.submit') }}" class="btn_b" /></li>
                </ul>
            @endif

            <hr />
        @endif

        <h3>{{ __('labels.u204n.h3_3') }}</h3>

        @foreach ($plansIsCompleted as $key => $plan)
            <h3>
                {{ __('labels.u204n.h3', [
                    'attr1' => ++$key,
                    'attr2' => $plan->reason_name,
                ]) }}
            </h3>

            <div class="js-scrollable eol">
                <table class="normal_b mw740">
                    <tr>
                        <th style="width: 70%;">{{ __('labels.u204n.th_1') }}</th>
                        <th>{{ __('labels.u204n.th_3') }}</th>
                        <th>{{ __('labels.u204n.th_4') }}</th>
                    </tr>
                    <tr>
                        <td>
                            {{ $plan->plan_detail_is_choice->plan_description ?? null }}
                        </td>
                        <td class="center">
                            {{ $plan->plan_detail_is_choice->text_revolution_v2 ?? null }}<br />
                            {{ $plan->plan_detail_is_choice->text_revolution ?? null }}
                        </td>
                        <td class="center">
                            {{ $plan->plan_detail_is_choice->type_plan_name ?? null }}
                        </td>
                    </tr>
                </table>
            </div>
            <!-- /scroll wrap -->

            @if ($plan->plan_detail_is_choice->is_type_plan_name)
                <h3>{{ __('labels.u204n.h3_2') }}</h3>
                <div class="js-scrollable eol">
                    <table class="normal_b w480">
                        <tr>
                            <th>{{ __('labels.u204n.th_8') }}</th>
                            <th>{{ __('labels.u204n.th_9') }}</th>
                            <th>{{ __('labels.u204n.th_10') }}</th>
                        </tr>
                        @if(!count($plan->plan_detail_doc_is_completed))
                            <tr>
                                <td colspan="3" style="text-align: center;">{{ __('messages.general.Common_E032') }}</td>
                            </tr>
                        @else
                            @foreach ($plan->plan_detail_doc_is_completed ?? [] as $planDetailDoc)
                                @if($planDetailDoc->requiredDocumentDetail && $planDetailDoc->requiredDocumentDetail->attachment_user)
                                    @php
                                        $attachmentData = collect(json_decode($planDetailDoc->requiredDocumentDetail->attachment_user, true))->groupBy('sending_date') ?? []
                                    @endphp
                                    @foreach ($attachmentData as $sendingDate => $attachmentUsers)
                                        <tr>
                                            @if($loop->first)
                                                <td rowspan="{{ count($attachmentData) }}">
                                                    {{ $planDetailDoc->MTypePlanDoc->name ?? null }}<br />
                                                    <a href="javascript:void(0)" class="click_download">{{ __('labels.u204n.download') }} &gt;&gt;</a>
                                                </td>
                                            @endif
                                            <td>
                                                @php
                                                    echo str_replace('-', '/', $sendingDate)
                                                @endphp
                                            </td>
                                            <td>
                                                @foreach ($attachmentUsers as $item)
                                                    {{ $item['value'] ?? null }}<br />
                                                    <a href="{{ asset($item['value']) }}" class="download_a_hidden" download hidden></a>
                                                @endforeach
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            @endforeach
                        @endif
                    </table>
                </div>
                <!-- /scroll wrap -->
            @endif
        @endforeach

        @if (Route::is(['user.refusal.materials-re.index']))
            <ul class="footerBtn clearfix">
                <li><input type="submit" name="submit" value="{{ __('labels.u204n.submit') }}" class="btn_b" /></li>
            </ul>
            <ul class="footerBtn clearfix">
                <li><input type="submit" name="draft" value="{{ __('labels.u204n.draft') }}" class="btn_a" /></li>
            </ul>
        @elseif (Route::is(['user.refusal.materials-re.confirm.index']))
            <ul class="footerBtn clearfix">
                <li><input type="submit" name="submit_confirm" value="{{ __('labels.submit') }}" class="btn_b" /></li>
            </ul>
            <ul class="footerBtn clearfix">
                <li><input type="submit" name="draft_confirm" value="{{ __('labels.u204n.draft_confirm') }}" class="btn_a" /></li>
            </ul>
        @endif
    </form>
</div>
@endsection
@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        const errorMessageRequired = '{{ __('messages.common.errors.Common_E001') }}';
        const routeAjaxMaterial = '{{ route('user.refusal.materials.ajax') }}';
        const routeAjaxMaterialDelete = '{{ route('user.refusal.materials.ajax_delete') }}';
        const routeAjaxMaterialReDownload = '{{ route('user.refusal.materials-re.ajax_download') }}';
        const correspondence_U204_E004 = '{{ __('messages.general.correspondence_U204_E004') }}';
        const correspondence_U204_E005 = '{{ __('messages.general.correspondence_U204_E005') }}';
        const Common_E028 = '{{ __('messages.general.Common_E028') }}';
        const Import_A000_E001 = '{{ __('messages.general.Import_A000_E001') }}';
        const Common_E001 = '{{ __('messages.general.Common_E001') }}';
        const Common_E026 = '{{ __('messages.general.Common_E026') }}';
        const routeConfirm = @JSON(Route::is(['user.refusal.materials-re.confirm.index']));
        const requiredDocument = @JSON($requiredDocument);
        const isBlockScreen = @JSON($isBlockScreen);
        const trademarkPlanId = @json(request()->__get('trademark_plan_id'));
        const round = @JSON(request()->__get('round'));
    </script>
    <script type="text/JavaScript" src="{{ asset('end-user/refusal/material-re.js') }}"></script>
@endsection
