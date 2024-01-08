@extends('admin.layouts.app')

@section('main-content')
    <div id="contents" class="admin">
        <div class="wide clearfix">
            @include('compoments.messages')

            <form id="form" action="{{ route('admin.refusal.material.no-material.post', [
                'id' => $comparisonTrademarkResult->id,
                'trademark_plan_id' => $trademarkPlan->id,
            ]) }}" method="post">
                @csrf

                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable,
                ])

                @include('admin.modules.materials.partials.reasons-refusal', [
                    'comparisonTrademarkResult' => $comparisonTrademarkResult,
                    'trademarkPlan' => $trademarkPlan,
                ])

                <h3>{{ __('labels.a204no_mat.title') }}</h3>

                <p class="eol">
                    <input type="button" value="{{ __('labels.common_a204.pre-question') }}" class="btn_b no_disabled"
                       onclick="window.open('{{ route('admin.refusal.pre-question-re.supervisor.show', [
                            'id' => $comparisonTrademarkResult->id,
                            'type' => VIEW
                       ]) }}')"
                    >
                </p>

                <p>{{ __('labels.a204no_mat.step') }}</p>

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
                                <td class="center">{{ $plan->getTypePlanName ?? '' }}</td>
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
                                        $planDetailData = $planDetail['plan_detail_product'] ?? null;

                                        $leaveStatus = $planDetailData->leave_status ?? null;
                                        $leaveStatusText = LEAVE_STATUS_TYPES[$leaveStatus] ?? '';

                                        $leaveStatusOther = $planDetailData->leave_status_other ?? null;
                                        $leaveStatusOther = json_decode($leaveStatusOther ?? '[]', true);
                                        $leaveStatusOtherText = collect();
                                        foreach ($leaveStatusOther as $other) {
                                            if (!empty($other['value'])) {
                                                $leaveStatusOtherText->push(LEAVE_STATUS_TYPES[$other['value']] ?? null);
                                            }
                                        }
                                        $leaveStatusOtherText = $leaveStatusOtherText->unique()->toArray();
                                        $leaveStatusOtherText = implode('、', $leaveStatusOtherText);

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
                                    @endphp

                                    <td class="center {{ $detailClass ?? $tdClass ?? '' }}">
                                        @if(!empty($leaveStatus))
                                            {{ $leaveStatusText ?? '' }}
                                        @elseif(!empty($leaveStatusOther))
                                            {{ $leaveStatusOtherText ?? '' }}
                                        @elseif(!empty($planDetailData) && $planDetailData->is_choice != 0)
                                            {{ __('labels.a203c.plan_table.leave_all') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <dl class="w08em eol clearfix">
                    @foreach($planComments as $item)
                        <dt>{{ __('labels.a203c.comment') }}：</dt>
                        <dd class="white-space-pre-line">{{ $item->created_at->format('Y/m/d') }}　{!! $item->content !!}</dd>
                    @endforeach
                </dl>

                <ul class="footerBtn clearfix">
                    <li><input type="submit" value="{{ __('labels.a204no_mat.btn_submit') }}" class="btn_c"></li>
                </ul>
            </form>
        </div>
    </div>
@endsection

@section('footerSection')
    <script src="{{ asset('admin_assets/pages/materials/no-materials.js') }}"></script>

    @if(in_array($adminUser->role, [ ROLE_SUPERVISOR ]) && empty(request()->type) || !empty(request()->type))
        <script>
            NoMaterial = new NoMaterial;
            NoMaterial.disableInput();
        </script>
    @endif
    @if (Request::get('type') == 'view')
        <script>
            NoMaterial = new NoMaterial;
            NoMaterial.disableInput();
        </script>
    @endif
@endsection
