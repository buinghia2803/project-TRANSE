@php $firstPlanData = []; @endphp

@if($trademarkPlan)
    @foreach($trademarkPlan->plans as $k => $plan)
        @php
            $resonNameString = '';
            if ($plan->planReasons) {
                $resonNameString = $plan->planReasons->implode('reason.reason_name', ', ');
            }
        @endphp
        <h3>{{ __('labels.a203s.counter_measure') }}{{ $k + 1 }} {{ $resonNameString }}</h3>

        <h5>{{ __('labels.a203s.enter_the_person_in_charge') }}</h5>
        <table class="normal_b eol">
            <tr>
                <td colspan="4">&nbsp;</td>
                @for($i = 1;$i <= $plan->planDetails->count();$i++)
                    <th>{{ __('labels.a203s.draft_policy') }}({{ $i }})</th>
                @endfor
            </tr>
            <tr>
                <th colspan="4" class="right">{{ __('labels.a203s.draft_policy') }}</th>
                @foreach($plan->planDetails->sortBy('id') as $planDetail)
                    <td style="width:15em;">{{ $planDetail->plan_description }}</td>
                @endforeach
            </tr>
            <tr>
                <th colspan="4" class="right">{{ __('labels.a203s.refusal_resolvability') }}</th>
                @foreach($plan->planDetails as $planDetail)
                    <td class="center">{{ $planDetail->getTextRevolution() }}</td>
                @endforeach
            </tr>
            <tr>
                <th colspan="4" class="right">{{ __('labels.a203s.materials') }}</th>
                @foreach($plan->planDetails as $planDetail)
                    <td class="center">{!! $planDetail->getTypePlanName() !!}</td>
                @endforeach
            </tr>
            <!--products-->
            <tr>
                <th style="width:5em;">{{ __('labels.a203s.division') }}</th>
                <th style="width:15em;">{{ __('labels.a203s.product') }}</th>
                <th style="width:15em;">{{ __('labels.a203s.similar_group_code')}}</th>
                <th style="width:5em;">{{ __('labels.a203s.report')}}</th>
                @for($i = 1;$i <= $plan->planDetails->count();$i++)
                    <td class="center">&nbsp;</td>
                @endfor
            </tr>
            @foreach ($plan->productsByProduct as $product)
                @php
                    $backGroundRow = '';
                    if($product['role_add'] == $roleAddPersonCharge) {
                        $backGroundRow = 'bg-fff9ab';
                    } else if($product['role_add'] == $roleAddResponsiblePerson) {
                        $backGroundRow = 'bg-e5d9ed';
                    }
                @endphp
                <tr class="{{ $backGroundRow }}">
                    <td class="center">{{ $product['mDistinction']['name'] }}</td>
                    <td>{{ $product['name'] }}</td>
                    <td>
                        @foreach($product['m_code_names'] as $i => $name)
                            @if($i <=2)
                                {{ $name. ' ' }}
                            @endif
                        @endforeach
                        @if(count($product['m_code_names']) > 3)
                            <a href="#" class="button-show-more-code">+</a>
                        @endif
                        <div class="show-more-codes">
                            @foreach($product['m_code_names'] as $j => $name)
                                @if($j > 2)
                                    {{ $name. ' ' }}
                                @endif
                            @endforeach
                        </div>
                    </td>
                    <!--show rank -->
                    @if($product['is_choice'] == $isChoiceFalse)
                        <td class="center">{{ __('labels.a203s.no_app')}}</td>
                    @else
                        @if($product['role_add'] == $roleAddOther)
                            <td class="center">{{ $product['rank'] ?? '' }}</td>
                        @elseif(in_array($product['role_add'], [$roleAddPersonCharge, $roleAddResponsiblePerson]))
                            <td class="center">-</td>
                        @else
                            <td class="center"></td>
                        @endif
                    @endif
                    @foreach($plan->planDetails as $item)
                        @php
                            $planDetail = null;
                            foreach($product['plan_details'] as $value) {
                               if($value['plan_detail_id'] == $item->id) {
                                   $planDetail = $value;
                               }
                            }

                            if ($k == 0) {
                                $firstPlanData[] = $planDetail;
                            }
                        @endphp
                        @if($planDetail)
                            @if($planDetail['is_choice'] == $isChoiceFalse)
                                <td class="center">-</td>
                            @else
                                @if($planDetail['leave_status'])
                                    @php
                                        $background = \App\Models\PlanDetailProduct::getBackgroundByLeaveStatus($planDetail['leave_status']);
                                    @endphp
                                    <td class="center"
                                        style="background: {{ $background }}">{{ $listLeaveStatus[$planDetail['leave_status']] }}</td>
                                @elseif(!empty($planDetail['leave_status_other']) && ($planDetail['leave_status_other'] != '[]'))
                                    @php
                                        $arr = [];
                                        $leaveStatusOthers = json_decode($planDetail['leave_status_other'], true);
                                        if(is_array($leaveStatusOthers)) {
                                            foreach ($leaveStatusOthers as $key => $leaveStatusOther) {
                                                $firstPlanDetailData = collect($firstPlanData)
                                                    ->where('m_product_id', $planDetail['m_product_id'])
                                                    ->where('plan_detail_id', $leaveStatusOther['plan_product_detail_id'])->first();

                                                $leaveStatusText = '';
                                                switch ($leaveStatusOther['value']) {
                                                    case LEAVE_STATUS_4:
                                                        if ($firstPlanDetailData['leave_status'] == LEAVE_STATUS_6) {
                                                            $leaveStatusText = LEAVE_STATUS_TYPES[LEAVE_STATUS_6];
                                                        } else if ($firstPlanDetailData['leave_status'] == LEAVE_STATUS_7) {
                                                            $leaveStatusText = LEAVE_STATUS_TYPES[LEAVE_STATUS_7];
                                                        } else if ($firstPlanDetailData['leave_status'] == LEAVE_STATUS_3) {
                                                            $leaveStatusText = LEAVE_STATUS_TYPES[LEAVE_STATUS_3];
                                                        }
                                                        break;
                                                    case LEAVE_STATUS_3:
                                                        if ($firstPlanDetailData['leave_status'] == LEAVE_STATUS_3) {
                                                            $leaveStatusText = LEAVE_STATUS_TYPES[LEAVE_STATUS_6] . LEAVE_STATUS_TYPES[LEAVE_STATUS_3];
                                                        } else {
                                                            $leaveStatusText = LEAVE_STATUS_TYPES[$firstPlanDetailData['leave_status']] . LEAVE_STATUS_TYPES[LEAVE_STATUS_3];
                                                        }
                                                        break;
                                                    default:
                                                        $leaveStatusText = LEAVE_STATUS_TYPES[$leaveStatusOther['value']];
                                                        break;
                                                }

                                                $arr[] = $leaveStatusText;
                                            }
                                        }
                                    @endphp
                                    <td class="center">{{ implode('、', $arr) }}</td>
                                @else
                                    <td class="center">{{ __('labels.a203s.level_all') }}</td>
                                @endif
                            @endif
                        @else
                            <td class="center">-</td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </table>
    @endforeach
@endif
