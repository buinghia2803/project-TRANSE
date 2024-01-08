@extends('admin.layouts.app')

@section('main-content')
    <div id="contents">
        <div class="wide clearfix">
            @include('compoments.messages')

            <form id="form" action="{{ route('admin.refusal.materials-re.supervisor.post', [
                'id' => $comparisonTrademarkResult->id,
                'trademark_plan_id' => $trademarkPlan->id,
                'round' => request()->round ?? '',
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

                <h3>{{__('labels.a204han.a204n_title')}}</h3>

                <dl class="w16em eol clearfix">
                    <dt>{{__('labels.a204han.sending_docs_deadline')}}</dt>
                    <dd>
                        <input type="date" name="response_deadline" value="{{ $requiredDocument->response_deadline ?? date('Y-m-d') }}">
                    </dd>
                </dl>

                <dl class="w14em eol clearfix">
                    @foreach($planComments as $item)
                        <dt>{{ __('labels.a203c.comment') }}：</dt>
                        <dd class="white-space-pre-line">{{ $item->created_at->format('Y/m/d') }}　{!! $item->content !!}</dd>
                    @endforeach
                </dl>
                <input type="hidden" name="required_document_id" value="{{$requiredDocumentId}}">
                @foreach($plans as $plan)
                    @if(isset($plan->requiredDocuments) && count($plan->requiredDocuments) > 0)
                        @php
                            $planDetailDocs = $plan->planDetailDocs;

                            $planDetailDocCompleted = [];
                            $planDetailDocNotCompleted = [];
                            foreach ($planDetailDocs as $planDetailDoc) {
                                $isCompleted = true;
                                $requiredDocumentDetails = $planDetailDoc->required_document_details ?? collect([]);

                                if (count($requiredDocumentDetails) == 0) {
                                    continue;
                                }

                                foreach ($requiredDocumentDetails as $requiredDocumentDetail) {
                                    if ($requiredDocumentDetail->is_completed == false) {
                                        $isCompleted = false;
                                        break;
                                    }
                                }

                                if ($isCompleted == true) {
                                    $planDetailDocCompleted[] = $planDetailDoc;
                                } else {
                                     $planDetailDocNotCompleted[] = $planDetailDoc;
                                }
                            }
                        @endphp
                        <div class="plan-item">
                            <h3>{{__('labels.a204han.plan')}}-{{ $plan->index }} {{ $plan->reason_name ?? '' }}</h3>

                            {{-- Table 1 --}}
                            <table class="normal_b eol column1 is-choice-table">
                                <tbody>
                                <tr>
                                    <th style="width:12em;"></th>
                                    <th style="width:60%;">{{ __('labels.a204han.table.plan_detail_index') }}</th>
                                    <th style="width:80px;">{{ __('labels.a204han.table.revolution') }}</th>
                                    <th>{{ __('labels.a204han.table.type_plan_name') }}</th>
                                    @if($loop->first == false)
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
                                            @if($loop->parent->first == false)
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
                                @if(count($planDetailDocNotCompleted) > 0)
                                    <table class="normal_b eol column1 doc-table">
                                        <caption>{{ __('labels.a204han.doc_not_completed') }}</caption>
                                        <tbody>
                                        <tr>
                                            <th style="width:12em;" class="bg_pink">{{ __('labels.a204han.table.type_plan_doc_name') }}</th>
                                            <th style="width:25%;" class="bg_pink">{{ __('labels.a204han.table.doc_requirement_des') }}</th>
                                            <th style="" class="bg_pink">{{ __('labels.a204han.table.type_plan_doc_url') }}</th>
                                            <th style="width:7em;" class="bg_pink">{{ __('labels.a204han.table.sending_date') }}</th>
                                            <th style="width:12em;" class="bg_pink">{{ __('labels.a204han.table.attachment_user_owner') }}</th>
                                            <th style="width:25%;" class="bg_pink">{{ __('labels.a204han.table.description_documents_miss') }}</th>
                                        </tr>
                                        @if(count($planDetailDocNotCompleted))
                                            @foreach($planDetailDocNotCompleted as $planDetailDoc)
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
                                                                    <a href="{{ $planDetailDoc->MTypePlanDoc->url ?? '' }}" target="_blank" class="no_disabled">{{ $planDetailDoc->MTypePlanDoc->url ?? '' }}</a>
                                                                </td>
                                                            @endif
                                                            <td>{{ \CommonHelper::formatTime($seedingData ?? '', 'Y/m/d') }}</td>
                                                            <td>
                                                                @foreach($attachmentUser ?? [] as $item)
                                                                    <a href="{{ $item['value'] ?? '' }}" target="_blank" class="no_disabled">{{ $item['name'] ?? '' }}</a><br>
                                                                @endforeach
                                                            </td>
                                                            <td>
                                                                @if($loop->parent->first == true && $loop->first == true)
                                                                    {{ $plan->required_document_miss_description_docs_miss ?? '' }}
                                                                @endif
                                                            </td>
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
                                @endif
                            @endif

                            {{-- Table 3 --}}
                            @if(!empty($plan->planDocCmts) && count($plan->planDocCmts) > 0)
                                @php
                                    $planDocCmts = $plan->planDocCmts->sortByDesc('date_send');
                                @endphp
                                <table class="normal_a mb-3 comment-table">
                                    <tbody>
                                    <tr>
                                        <th>{{ __('labels.a204han.doc_created_at') }}</th>
                                        <th>{{ __('labels.a204han.doc_content') }}</th>
                                    </tr>
                                    @foreach($planDocCmts as $planDocCmt)
                                        <tr>
                                            <td>{{ CommonHelper::formatTime($planDocCmt->date_send ?? '', 'Y/m/d') }}</td>
                                            <td>
                                                <p class="white-space-pre-line">{!! $planDocCmt->content ?? '' !!}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @endif

                            @if(count($planDetailDocNotCompleted) > 0)
                                <dl class="w16em eol clearfix mt-5">
                                    <dt>{{ __('labels.u204n.h3_1') }}：</dt>
                                    <dd>
                                <textarea
                                    class="middle_c"
                                    name="plans[{{ $plan->id }}][description_documents_miss]"
                                    data-description_documents_miss
                                >{{ $plan->required_document_miss_description_docs_miss ?? '' }}</textarea>
                                    </dd>
                                </dl>
                            @endif

                            {{-- Table 4 --}}
                            @if($plan->total_attachment_user > 0)
                                @if(count($planDetailDocCompleted) > 0)
                                    <p>{{ __('labels.a204han.doc_completed') }}</p>
                                    <table class="normal_b eol column1 doc-complete-table">
                                        <tbody>
                                        <tr>
                                            <th style="width:12em;" class="bg_pink">{{ __('labels.a204han.table.type_plan_doc_name') }}</th>
                                            <th style="width:25%;" class="bg_pink">{{ __('labels.a204han.table.doc_requirement_des') }}</th>
                                            <th class="bg_pink">{{ __('labels.a204han.table.type_plan_doc_url') }}</th>
                                            <th style="width:7em;" class="bg_pink">{{ __('labels.a204han.table.sending_date') }}</th>
                                            <th style="width:12em;" class="bg_pink">{{ __('labels.a204han.table.attachment_user_owner') }}</th>
                                            <th style="width:25%;" class="bg_pink">{{ __('labels.a204han.table.description_documents_miss') }}</th>
                                        </tr>
                                        @if(count($planDetailDocCompleted) > 0)
                                            @foreach($planDetailDocCompleted as $planDetailDoc)
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
                                                                <td class="bg_gray" rowspan="{{ count($attachmentUserGroup) }}">{{ $planDetailDoc->MTypePlanDoc->name ?? '' }}</td>
                                                                <td class="bg_gray" rowspan="{{ count($attachmentUserGroup) }}">
                                                                    <span class="white-space-pre-line">{!! $planDetailDoc->doc_requirement_des !!}</span>
                                                                </td>
                                                                <td class="bg_gray" rowspan="{{ count($attachmentUserGroup) }}">
                                                                    <a href="{{ $planDetailDoc->MTypePlanDoc->url ?? '' }}" target="_blank" class="no_disabled">{{ $planDetailDoc->MTypePlanDoc->url ?? '' }}</a>
                                                                </td>
                                                            @endif
                                                            <td class="bg_gray">{{ \CommonHelper::formatTime($seedingData ?? '', 'Y/m/d') }}</td>
                                                            <td class="bg_gray">
                                                                @foreach($attachmentUser ?? [] as $item)
                                                                    <a href="{{ $item['value'] ?? '' }}" target="_blank" class="no_disabled">{{ $item['name'] ?? '' }}</a><br>
                                                                @endforeach
                                                            </td>
                                                            <td class="bg_gray"></td>
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
                                @endif
                            @endif

                            {{-- Table 5 --}}
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

                            @if($loop->last == false)
                                <hr>
                            @endif
                        </div>
                    @endif
                @endforeach

                <h5>{{ __('labels.a204han.content') }}：</h5>
                <p class="eol">
                    <textarea class="middle_c" name="content">{{ $planComment->content ?? '' }}</textarea>
                    <input type="hidden" name="required_document_comment_id" value="{{$planComment ? $planComment->id : 0}}">
                </p>

                <hr>

                <ul class="footerBtn clearfix">
                    <li>
                        <input type="button" value="{{__('labels.back')}}" class="btn_a" onclick="window.location = '{{ $backUrl ??  route('admin.home') }}'">
                    </li>
                    <li><input type="submit" name="{{ DRAFT }}" value="{{ __('labels.a204han.btn_draft') }}" class="btn_b"></li>
                    <li><input type="submit" name="{{ SUBMIT }}" value="{{ __('labels.btn_send_to_user') }}" class="btn_c"></li>
                </ul>
            </form>
        </div>
    </div>
@endsection

@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        const errorMessageRequired = '{{ __('messages.general.Common_E001') }}';
        const errorMessageRequiredDocDeadline = '{{ __('messages.general.Common_E025') }}';
        const errorMessageMinDocDeadline = '{{ __('messages.general.Common_E038') }}';
        const errorMessageMaxLength1000 = '{{ __('messages.general.Common_E026') }}';
        const errorMessageMaxLength255 = '{{ __('messages.general.Common_E031') }}';
    </script>
    <script src="{{ asset('admin_assets/pages/materials/re-supervisor.js') }}"></script>
    @if ($disabledFlag == true)
        <script>disabledScreen();</script>
    @endif
@endsection
