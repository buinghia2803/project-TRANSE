@extends('admin.layouts.app')

@section('main-content')
    <div id="contents" class="admin_wide">
        <!-- contents inner -->
        <div class="wide clearfix">
            @include('compoments.messages')

            <form>

                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable,
                ])

                @include('admin.modules.materials.partials.reasons-refusal', [
                    'comparisonTrademarkResult' => $comparisonTrademarkResult,
                    'trademarkPlan' => $trademarkPlan,
                ])

                <h3>{{ __('labels.a204han.a204kakunin_title') }}</h3>

                <p class="eol">
                    <input type="button" value="{{ __('labels.common_a204.pre-question') }}" class="btn_b"
                           onclick="window.open('{{ route('admin.refusal.pre-question-re.supervisor.show', [
                            'id' => $comparisonTrademarkResult->id,
                            'type' => VIEW
                       ]) }}')"
                    >
                </p>

                <p>{{ __('labels.a204no_mat.step') }}</p>

                {{-- Table a204no_mat --}}
                @if($totalRequiredTypePlan > 0)
                    <table class="normal_b eol">
                        <tbody>
                        <tr>
                            <td colspan="4">&nbsp;</td>
                            @foreach($plans as $plan)
                                <th>
                                    {{ __('labels.a203c.plan_table.plan') }}-{{ $loop->iteration ?? '' }} {{ $plan->reason_name ?? '' }}
                                    <br>
                                    {{ __('labels.a204no_mat.draft_policy') }}({{ $plan->isChoiceDetail->index ?? '' }})
                                </th>
                            @endforeach
                        </tr>
                        <tr>
                            <th colspan="4" class="right">{{ __('labels.a204no_mat.draft_policy') }}</th>
                            @foreach($plans as $plan)
                                <td style="width:20em;">
                                    <span class="white-space-pre-line">{!! $plan->isChoiceDetail->plan_description ?? '' !!}</span>
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <th colspan="4" class="right">{{ __('labels.a204no_mat.refusal_resolvability') }}</th>
                            @foreach($plans as $plan)
                                <td class="center">{{ $plan->isChoiceDetail->text_revolution ?? '' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th colspan="4" class="right">{{ __('labels.a204no_mat.type_plan_name') }}</th>
                            @foreach($plans as $plan)
                                <td class="">
                                    @if($plan->isChoiceDetail->isRequiredTypePlan())
                                        @php
                                            $mTypePlan = $plan->isChoiceDetail->mTypePlan ?? null;
                                            $mTypePlanDocs = $mTypePlan->mTypePlanDocs ?? collect([]);
                                        @endphp
                                        @foreach($mTypePlanDocs as $mTypePlanDoc)
                                            @if(!empty($mTypePlanDoc->url))
                                                <a href="{{ $mTypePlanDoc->url }}" class="white-space-pre-line">{{ $mTypePlanDoc->name ?? '' }}</a>
                                            @else
                                                <p class="white-space-pre-line">{{ $mTypePlanDoc->name ?? '' }}</p>
                                            @endif
                                            <br>
                                            <br>
                                        @endforeach
                                    @else
                                        {{ $plan->getTypePlanName ?? '' }}
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <th colspan="4" class="right">{{ __('labels.a204no_mat.additional') }}</th>
                            @foreach($plans as $plan)
                                <td rowspan="2" class="center">{{ $plan->isChoiceDetail->distinct_is_add_text ?? '' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th style="width:5em;">{{ __('labels.a204no_mat.distinction') }}</th>
                            <th style="width:15em;">{{ __('labels.a204no_mat.product_name') }}</th>
                            <th style="width:15em;">{{ __('labels.a204no_mat.code') }}</th>
                            <th style="width:5em;">{{ __('labels.a204no_mat.rank') }}</th>
                        </tr>

                        @foreach($products as $product)
                            @php
                                $planDetailProduct = $product['plan_detail_product'] ?? null;
                                $productData = $product['product'] ?? null;
                                $planDetailDistinction = $product['plan_detail_distinction'] ?? null;
                                $distinction = $product['distinction'] ?? null;
                                $codes = $product['codes'] ?? null;
                                $reasonRefNumProd = $product['reasonRefNumProd'] ?? null;
                                $planDetails = collect($product['plan_details'] ?? []);

                                $tdClass = 'bg_yellow';
                                if ($planDetailProduct->isRoleAddSupervisor()) {
                                    $tdClass = 'bg_purple2';
                                } elseif ($planDetailProduct->isRoleAddUser()) {
                                    $tdClass = '';
                                }

                                $firstPlan = $plans->first();
                                $firstPlanDetail = $planDetails->where('id', $firstPlan->isChoiceDetail->id)->first();
                                $firstPlanDetailProduct = $firstPlanDetail['plan_detail_product'] ?? null;
                            @endphp
                            <tr>
                                <td class="center {{ $tdClass ?? '' }}">{{ $distinction->name ?? '' }}</td>
                                <td class="{{ $tdClass ?? '' }}">{{ $productData->name ?? '' }}</td>
                                <td class="{{ $tdClass ?? '' }}">
                                    <div class="code-block">
                                        @foreach($codes as $code)
                                            <span data-type="{{ $code->type }}" class="{{ $loop->index > 2 ? 'hidden' : '' }}">
                                                {{ $code->name ?? '' }}

                                                @if(count($codes) > 3 && $loop->index == 2)
                                                    <span class="show_all_code cursor-pointer">+</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="center {{ $tdClass ?? '' }}">
                                    @if($planDetailProduct->is_choice == false)
                                        {{ __('labels.a203c.no_application') }}
                                    @else
                                        {{ $reasonRefNumProd->rank ?? '-' }}
                                    @endif
                                </td>
                                @foreach($plans as $plan)
                                    @php
                                        $planDetail = $planDetails->where('id', $plan->isChoiceDetail->id ?? 0)->first();
                                        $planDetailProductData = $planDetail['plan_detail_product'] ?? null;

                                        $leaveStatus = $planDetailProductData->leave_status ?? null;
                                        $leaveStatusText = LEAVE_STATUS_TYPES[$leaveStatus] ?? '';

                                        $leaveStatusOther = $planDetailProductData->leave_status_other ?? null;
                                        $leaveStatusOther = json_decode($leaveStatusOther ?? '[]', true);

                                        // bg bg_pink bg_green bg_yellow
                                        $leaveStatusModel = new \App\Models\PlanDetailProduct;
                                        $detailClass = null;
                                        if ($leaveStatus == $leaveStatusModel::LEAVE_STATUS_2) {
                                            $detailClass = 'bg_pink';
                                        } elseif ($leaveStatus == $leaveStatusModel::LEAVE_STATUS_3) {
                                            $detailClass = 'bg_green';
                                        } elseif ($leaveStatus == $leaveStatusModel::LEAVE_STATUS_6) {
                                            $detailClass = 'bg_yellow';
                                        }

                                        if ($planDetailProductData->is_choice == false) {
                                            $detailClass = null;
                                        }
                                    @endphp

                                    <td class="center {{ $detailClass ?? $tdClass ?? '' }}">
                                        @if($planDetailProductData->is_choice == false)
                                            -
                                        @else
                                            @if(!empty($leaveStatus))
                                                {{ $leaveStatusText ?? '' }}
                                            @elseif(empty($leaveStatus) && empty($leaveStatusOther))
                                                {{ __('labels.a203s.level_all') }}
                                            @elseif(empty($leaveStatus) && !empty($leaveStatusOther))
                                                @php
                                                    $leaveStatusOther = collect($leaveStatusOther)->where('plan_product_detail_id', $firstPlanDetail['id'] ?? 0)->first();
                                                    $leaveStatusOther = $leaveStatusOther['value'] ?? '';
                                                @endphp
                                                @if($leaveStatusOther == $leaveStatusModel::LEAVE_STATUS_3)
                                                    {{ LEAVE_STATUS_TYPES[$leaveStatusOther] ?? '' }}
                                                @elseif($leaveStatusOther == $leaveStatusModel::LEAVE_STATUS_5)
                                                    {{ LEAVE_STATUS_TYPES[$leaveStatusOther] ?? '' }}
                                                @elseif($leaveStatusOther == $leaveStatusModel::LEAVE_STATUS_4)
                                                    @if($firstPlanDetailProduct->leave_status == $leaveStatusModel::LEAVE_STATUS_3)
                                                        {{ LEAVE_STATUS_TYPES[$firstPlanDetailProduct->leave_status] ?? '' }}
                                                    @elseif($firstPlanDetailProduct->leave_status == $leaveStatusModel::LEAVE_STATUS_6)
                                                        {{ __('labels.common_a204.addition') }} {{ $plan->isChoiceDetail->getTextRevolution() }}
                                                    @elseif($firstPlanDetailProduct->leave_status == $leaveStatusModel::LEAVE_STATUS_7)
                                                        {{ __('labels.common_a204.leave_all') }}
                                                    @endif
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endif

                <dl class="w08em eol clearfix">
                    @foreach($planComments as $item)
                        <dt>{{ __('labels.a203c.comment') }}：</dt>
                        <dd class="white-space-pre-line">{{ $item->created_at->format('Y/m/d') }}　{!! $item->content !!}</dd>
                    @endforeach
                </dl>

                {{-- Table a204han --}}
                @foreach($plans as $keyPlan => $plan)
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
                                            @if($planDetail->isRequiredTypePlan())
                                                @php
                                                    $mTypePlan = $planDetail->mTypePlan ?? null;
                                                    $mTypePlanDocs = $mTypePlan->mTypePlanDocs ?? collect([]);
                                                @endphp
                                                @foreach($mTypePlanDocs as $mTypePlanDoc)
                                                    @if(!empty($mTypePlanDoc->url))
                                                        <a href="{{ $mTypePlanDoc->url }}" class="white-space-pre-line">{{ $mTypePlanDoc->name ?? '' }}</a>
                                                    @else
                                                        <p class="white-space-pre-line">{{ $mTypePlanDoc->name ?? '' }}</p>
                                                    @endif
                                                    <br>
                                                    <br>
                                                @endforeach
                                            @else
                                                {{ $planDetail->getTypePlanName() }}<br>
                                            @endif
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

                        {{-- Table 2 --}}
                        @if($plan->total_attachment_user > 0)
                            <table class="normal_b eol doc-table">
                                <tbody>
                                <tr>
                                    <th style="width:25%;" class="bg_pink">{{ __('labels.a204han.table.type_plan_doc_name') }}</th>
                                    <th style="" class="bg_pink">{{ __('labels.a204han.table.doc_requirement_des') }}</th>
                                    <th style="width:7em;" class="bg_pink">{{ __('labels.a204han.table.type_plan_doc_url') }}</th>
                                    <th style="width:12em;" class="bg_pink">{{ __('labels.a204han.table.sending_date') }}</th>
                                    <th style="width:25%;" class="bg_pink">{{ __('labels.a204han.table.description_documents_miss') }}</th>
                                    <th style="width:12em;" class="bg_pink">{{ __('labels.a204han.table.attachment_user') }}</th>
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
                                                            <a href="{{ $planDetailDoc->MTypePlanDoc->url ?? '' }}" target="_blank">{{ $planDetailDoc->MTypePlanDoc->url ?? '' }}</a>
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

                        @if(!empty($plan->planDocCmts))
                            @php
                                $planDocCmts = $plan->planDocCmts->sortByDesc('created_at');
                            @endphp
                            <dl class="w16em eol clearfix">
                                @foreach($planDocCmts as $planDocCmt)
                                    <dt>{{ __('labels.a204han.doc_content') }}：</dt>
                                    <dd>{{ $planDocCmt->content ?? '' }}</dd>
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
                    </div>

                    @if($loop->last == false)
                        <hr>
                    @endif
                @endforeach
            </form>
        </div>
    </div>
@endsection

@section('footerSection')
    <script src="{{ asset('admin_assets/pages/materials/no-materials.js') }}"></script>
@endsection
