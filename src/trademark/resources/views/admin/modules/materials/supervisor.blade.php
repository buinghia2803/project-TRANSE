@extends('admin.layouts.app')

@section('main-content')
    <div id="contents" class="admin">

        <!-- contents inner -->
        <div class="wide clearfix">
            @include('compoments.messages')

            <form id="form" action="{{ route('admin.refusal.materials.supervisor.post', [
                'id' => $comparisonTrademarkResult->id,
                'trademark_plan_id' => $trademarkPlan->id,
            ]) }}" method="post">
                @csrf

                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable
                ])

                @include('admin.modules.materials.partials.reasons-refusal', [
                    'comparisonTrademarkResult' => $comparisonTrademarkResult,
                    'trademarkPlan' => $trademarkPlan,
                ])

                <h3>{{__('labels.a204han.title_1')}}</h3>
                <input type="hidden" name="required_document_id" value="{{$requiredDocumentId}}">
                @foreach($plans as $keyPlan => $plan)
                    @if(isset($plan->requiredDocuments) && count($plan->requiredDocuments) > 0)
                        <div class="plan-item">
                            <h3>{{__('labels.a204han.plan')}}-{{$keyPlan+1}} {{ $plan->reason_name ?? '' }}</h3>

                            {{-- Table 1 --}}
                            <table class="normal_b eol column1 is-choice-table">
                                <tbody>
                                <tr>
                                    <th style="width:12em;"></th>
                                    <th style="width:60%;">{{ __('labels.a204han.table.plan_detail_index') }}</th>
                                    <th style="width:80px;">{{ __('labels.a204han.table.revolution') }}</th>
                                    <th>{{ __('labels.a204han.table.type_plan_name') }}</th>
                                    @if($keyPlan != 0)
                                        <th>{{ __('labels.a204han.table.distinct_settlements') }}</th>
                                        <th>{{ __('labels.a204han.table.leave_all_product_name') }}</th>
                                    @endif
                                </tr>
                                @if(count($plan->planDetailsIsChoices) > 0)
                                    @foreach($plan->planDetailsIsChoices as $planDetail)
                                        <tr>
                                            <th>{{ __('labels.a204han.table.plan_detail_index') }}({{ $planDetail->index ?? '' }})</th>
                                            <td>
                                                <span class="white-space-pre-line">{!! $planDetail->plan_description !!}</span>
                                            </td>
                                            <td class="center">
                                                {{$planDetail->getStrRevolution()}}
                                                <br>{{$planDetail->getTextRevolution()}}
                                            </td>
                                            <td>
                                                {{$planDetail->getTypePlanName()}}<br>
                                            </td>
                                            @if($keyPlan != 0)
                                                <td class="center">
                                                    @php
                                                        $planDetailDistinctSettlements = $planDetail->planDetailDistinctSettlements ?? collect()
                                                    @endphp
                                                    @if($planDetailDistinctSettlements->count() > 0)
                                                        {{ $planDetailDistinctSettlements->count() }}
                                                    @else
                                                        {{ __('labels.a204han.no_addition') }}
                                                    @endif
                                                </td>
                                                <td class="center">
                                                    <span>{{ $planDetail->is_leave_all == true ? __('labels.a204han.table.leave_all') : '' }}</span>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center" colspan="100%">{{__('labels.no_data')}}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                            {{-- Table 2 --}}
                            @if($plan->total_attachment_user > 0)
                                <table class="normal_b doc-table">
                                    <tbody>
                                    <tr>
                                        <th style="width:25%;" class="bg_pink">{{ __('labels.a204han.table.type_plan_doc_name') }}</th>
                                        <th style="" class="bg_pink">{{ __('labels.a204han.table.doc_requirement_des') }}</th>
                                        <th style="width:7em;" class="bg_pink">{{ __('labels.a204han.table.type_plan_doc_url') }}</th>
                                        <th style="width:12em;" class="bg_pink">{{ __('labels.a204han.table.sending_date') }}</th>
                                        <th style="width:25%;" class="bg_pink">{{ __('labels.a204han.table.description_documents_miss') }}</th>
                                        <th style="width:12em;" class="bg_pink">{{ __('labels.a204han.table.attachment_user') }}</th>
                                        <th style="width:2em;" class="bg_pink">{{ __('labels.a204han.table.is_completed') }}</th>
                                    </tr>
                                    @php
                                        $planDetailDocs = $plan->planDetailDocs;
                                    @endphp
                                    @if(count($planDetailDocs) > 0)
                                        @foreach($planDetailDocs as $planDetailDoc)
                                            @php
                                                $requiredDocumentDetails = $planDetailDoc->required_document_details ?? collect([]);
                                            @endphp
                                            @foreach($requiredDocumentDetails as $requiredDocumentDetail)
                                                @php
                                                    $attachmentUserGroup = $requiredDocumentDetail->attachment_user_group;
                                                @endphp
                                                @foreach($attachmentUserGroup as $seedingData => $attachmentUser)
                                                    <tr>
                                                        @if($loop->first == true)
                                                            <td rowspan="{{ count($attachmentUserGroup) }}">{{ $planDetailDoc->MTypePlanDoc->name ?? '' }}</td>
                                                            <td rowspan="{{ count($attachmentUserGroup) }}">
                                                                <span class="white-space-pre-line">{!! $planDetailDoc->doc_requirement_des !!}</span>
                                                            </td>
                                                            <td rowspan="{{ count($attachmentUserGroup) }}">
                                                                <a href="{{ asset($planDetailDoc->MTypePlanDoc->url ?? '') }}" target="_blank">{{ $planDetailDoc->MTypePlanDoc->url ?? '' }}</a>
                                                            </td>
                                                        @endif

                                                        <td>{{ \CommonHelper::formatTime($seedingData ?? '', 'Y/m/d') }}</td>
                                                        <td>
                                                            @if($loop->parent->first == true && $loop->first == true)
                                                                {{ $plan->description_documents_miss ?? '' }}
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @foreach($attachmentUser ?? [] as $item)
                                                                <a href="{{ $item['value'] ?? '' }}" target="_blank">{{ $item['name'] ?? '' }}</a><br>
                                                            @endforeach
                                                        </td>
                                                        @if($loop->first == true)
                                                            <td rowspan="{{ count($attachmentUserGroup) }}" class="center">
                                                                <input
                                                                    data-doc_is_complete
                                                                    type="checkbox"
                                                                    name="plan_detail_docs[{{ $requiredDocumentDetail->id }}][is_completed]"
                                                                    {{ $requiredDocumentDetail->is_completed == true ? 'checked' : '' }}
                                                                >
                                                                <input type="hidden" name="plan_detail_docs[{{ $requiredDocumentDetail->id }}][id]" value="{{ $requiredDocumentDetail->id }}">
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            @endforeach
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="100%" class="text-center">{{__('labels.no_data')}}</td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                                <div class="eol"></div>
                            @endif

                            @if(!empty($plan->planDocCmts))
                                @php
                                    $planDocCmts = $plan->planDocCmts->sortByDesc('created_at');
                                @endphp
                                <dl class="w16em eol clearfix">
                                    @foreach($planDocCmts as $planDocCmt)
                                        <dt>{{ __('labels.a204han.doc_content') }}：</dt>
                                        <dd class="white-space-pre-line">{{ $planDocCmt->content ?? '' }}</dd>
                                    @endforeach
                                </dl>
                            @endif

                            {{-- Table 3 --}}
                            <table class="reject eol column1 is-not-choice-table">
                                <tbody>
                                <tr>
                                    <th style="width:12em;"></th>
                                    <th style="width:40%;">{{ __('labels.a204han.table.plan_detail_index') }}</th>
                                    <th>{{ __('labels.a204han.table.revolution') }}</th>
                                    <th>{{ __('labels.a204han.table.type_plan_name') }}</th>
                                    <th>{{ __('labels.a204han.table.distinct_settlements') }}</th>
                                    <th>{{ __('labels.a204han.table.leave_all_product_name') }}</th>
                                </tr>
                                @if(count($plan->planDetailsNotChoices) > 0)
                                    @foreach($plan->planDetailsNotChoices as $planDetail)
                                        <tr>
                                            <th>{{ __('labels.a204han.table.plan_detail_index') }}({{ $planDetail->index ?? '' }})<br><br></th>
                                            <td>
                                                <span class="white-space-pre-line">{!! $planDetail->plan_description !!}</span>
                                            </td>
                                            <td class="center">
                                                {{ $planDetail->getStrRevolution() }}
                                                <br>
                                                {{ $planDetail->getTextRevolution() }}
                                            </td>
                                            <td class="center">
                                                {{ $planDetail->getTypePlanName() }}<br>
                                            </td>
                                            <td class="center">
                                                @php
                                                    $planDetailDistinctSettlements = $planDetail->planDetailDistinctSettlements ?? collect()
                                                @endphp
                                                @if($planDetailDistinctSettlements->count() > 0)
                                                    {{ $planDetailDistinctSettlements->count() }}
                                                @else
                                                    {{ __('labels.a204han.no_addition') }}
                                                @endif
                                            </td>
                                            <td class="center">
                                                <span>{{ $planDetail->is_leave_all == true ? __('labels.a204han.table.leave_all') : '' }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td class="text-center" colspan="100%">{{__('labels.no_data')}}</td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>
                            <p class="fs14 center eol">
                                <label style="cursor:pointer">
                                    <input
                                        data-plan_is_complete
                                        type="checkbox"
                                        name="plans[{{ $plan->id }}][is_completed]"
                                        {{ $plan->required_document_plan_is_completed == true ? 'checked' : '' }}
                                    >{{ __('labels.a204han.plan_is_completed') }}
                                </label>
                                <input type="hidden" name="plans[{{ $plan->id }}][id]" value="{{ $plan->id }}">
                                <input type="hidden" name="plans[{{ $plan->id }}][required_document_id]" value="{{ $plan->required_document_id }}">
                            </p>
                        </div>
                        <hr>
                    @endif
                @endforeach

                <ul class="footerBtn clearfix">
                    <li><input type="submit" name="{{ SAVE }}" value="{{ __('labels.a204han.btn_save') }}" class="btn_a"></li>
                </ul>

                <ul class="footerBtn clearfix">
                    <li><input type="submit" name="{{ A203SHU }}" value="{{ __('labels.a204han.btn_edit') }}" class="btn_a"></li>
                </ul>

                <ul class="footerBtn clearfix">
                    <li><input type="submit" name="{{ DRAFT }}" value="{{ __('labels.a204han.btn_draft') }}" class="btn_b"></li>
                </ul>

                <hr>

                <h5>{{ __('labels.a204han.content') }}</h5>
                <p class="eol">
                    <textarea class="middle_c" name="content">{{ $planComment->content ?? '' }}</textarea>
                    <input type="hidden" name="required_document_comment_id" value="{{(isset($planComment) && !empty($planComment)) ? $planComment->id : 0}}">
                </p>

                <ul class="footerBtn clearfix">
                    <li><input type="submit" name="{{ SUBMIT }}" value="{{ __('labels.a204han.btn_submit') }}" class="btn_c"></li>
                </ul>
            </form>
        </div>
    </div>
@endsection

@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        const errorMessageRequiredDocComplete = '{{ __('messages.general.correspondence_A204_E001') }}';
        const errorMessageRequiredPlanComplete = '{{ __('messages.general.correspondence_A204_E002') }}';
        const errorMessageRequiredSave = '{{ __('messages.general.correspondence_A204_E003') }}';
        const Common_E025 = '{{ __('messages.general.Common_E025') }}';
        const errorMessageMaxLength1000 = '{{ __('messages.general.Common_E026') }}';

        const SAVE = '{{ SAVE }}';
        const DRAFT = '{{ DRAFT }}';
        const SUBMIT = '{{ SUBMIT }}';
    </script>
    <script src="{{ asset('admin_assets/pages/materials/supervisor.js') }}"></script>
    @if (Request::get('type') == 'view'|| $flagDisabled == true)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ROLE_SUPERVISOR] ])
@endsection
