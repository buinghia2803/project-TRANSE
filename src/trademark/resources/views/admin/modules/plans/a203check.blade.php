<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <title>AMS オンライン出願サービス</title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />

    <link href="{{ asset('common/css/contents.css') }}" rel="stylesheet" type="text/css" />
    <script type="text/JavaScript" src="{{asset('common/js/jquery-3.6.0.min.js')}}"></script>
    {{-- jquery-confirm --}}
    <link href="{{ asset('common/libs/jquery-confirm/jquery-confirm.min.css') }}" rel="stylesheet" type="text/css" />
    <script src="{{ asset('common/libs/jquery-confirm/jquery-confirm.min.js') }}"></script>
</head>
<body>
    <style>
        #contents {
            padding-bottom: 3vh !important;
        }
    </style>
    <div id="wrapper" style="height: 80%;">
        <div id="contents">
            <!-- contents inner -->
            <div class="wide clearfix">
                <form>
                    <table class="normal_b column1">
                        <tr>
                            <td colspan="3" rowspan="2">
                                {{ __('labels.a203check.title') }}
                            </td>
                            @foreach($plans as $plan)
                                <th colspan="{{ count($plan->planDetails) }}">
                                    {{ __('labels.a203check.text_plan1') }}{{ $loop->iteration ?? '' }} {{ __('labels.a203check.text_plan2') }}{{ $plan->reason_name ?? '' }}
                                </th>
                            @endforeach
                        </tr>
                        <tr>
                            @php
                                $white = 0;
                            @endphp
                            @foreach($plans as $keyPlan => $plan)
                                @foreach($plan->planDetails as $planDetail)
                                    <th>
                                        <input
                                            name="input-{{$plan->id}}"
                                            data-plan_nth="{{$keyPlan + 1}}"
                                            data-plan_id="{{$plan->id}}"
                                            data-plan_detail_id="{{$planDetail->id}}"
                                            class="white-{{$white++}} choose_plan_detail"
                                            data-white="white-plan-{{$plan->id}}"
                                            type="radio"
                                        />
                                        {{ __('labels.a203c.plan_table.plan_detail') }}({{ $loop->iteration }})
                                    </th>
                                @endforeach
                            @endforeach
                            @php
                                $white = 0;
                            @endphp
                        </tr>
                        <tr>
                            @php
                                $white = 0;
                            @endphp
                            <th colspan="3" class="right">{{ __('labels.a203c.revolution') }}</th>
                            @foreach($plans as $plan)
                                @foreach($plan->planDetails as $planDetail)
                                    <th rowspan="2" class="white-{{$white++}} white-plan-{{$plan->id}}-revol">{{ $planDetail->text_revolution_v2 ?? '' }}</th>
                                @endforeach
                            @endforeach
                            @php
                                $white = 0;
                            @endphp
                        </tr>
                        <tr>
                            <th>{{ __('labels.a203c.distinction') }}</th>
                            <th style="width:18%;">{{ __('labels.a203c.product_name') }}</th>
                            <th class="middle bg_pink">{{ __('labels.a203check.text_over1') }}<br />{{ __('labels.a203check.text_over2') }}</th>
                        </tr>
                        @forelse($products as $keyProd => $product)
                            @php
                                $productData = $product['product'] ?? null;
                                $distinction = $product['distinction'] ?? null;
                                $planDetails = collect($product['plan_details'] ?? []);
                                $roleAddTwo = $planDetails->whereIn('plan_detail_product.role_add', [ROLE_MANAGER, ROLE_SUPERVISOR])->count();
                                $roleAddDifferentOne = $planDetails->where('plan_detail_product.role_add', '!=', ROLE_OFFICE_MANAGER)->count();
                                $roleAddOneAndLeaveStatusTwo = $planDetails->where('plan_detail_product.role_add', ROLE_OFFICE_MANAGER)->where('plan_detail_product.leave_status', LEAVE_STATUS_2)->count();
                                $roleAddOneAndLeaveStatusDifferentTwo = $planDetails->where('plan_detail_product.role_add', ROLE_OFFICE_MANAGER)->where('plan_detail_product.leave_status', '!=' , LEAVE_STATUS_2)->max('possibility_resolution');
                                $overallRating = null;
                                foreach ($planDetails as $key => $item) {
                                    if ($roleAddDifferentOne) {
                                        $overallRating = '－';
                                    } elseif ($roleAddOneAndLeaveStatusTwo) {
                                        $overallRating = '削除';
                                    } elseif ($roleAddOneAndLeaveStatusDifferentTwo) {
                                        $overallRating = \App\Models\PlanDetail::listPossibilityResolution()[$roleAddOneAndLeaveStatusDifferentTwo] ?? null;
                                    }
                                }
                            @endphp
                            <tr>
                                <td class="right">
                                    @if ($roleAddTwo)
                                        <span class="icon_add">{{ __('labels.a203check.add') }}</span><br />
                                    @endif
                                    {{ $distinction->name ?? '' }}
                                </td>
                                <td>
                                    @if ($roleAddTwo)
                                        <span class="icon_add">{{ __('labels.a203check.add') }}</span>
                                    @endif
                                    {{ $productData->name ?? '' }}
                                </td>
                                <td class="center bg_pink">
                                    {{ $overallRating ?? '' }}
                                </td>
                                @php
                                    $white = 0;
                                @endphp
                                @foreach($planDetails as $planDetail)
                                    @php
                                        $planDetailProd = $planDetail['plan_detail_product'] ?? null;
                                    @endphp
                                    <td
                                        class="center bg_gray white-{{$white++}} white-plan-{{$planDetail['plan_id']}}"
                                        data-child_plan_id="{{ $planDetail['plan_id'] ?? '' }}"
                                        data-plan_detail_id="{{ $planDetail['id'] ?? '' }}"
                                        data-role_add="{{ $planDetailProd['role_add'] ?? '' }}"
                                        data-leave_status="{{ $planDetailProd['leave_status'] ?? '' }}"
                                        data-leave_status_other="{{ $planDetailProd->leave_status_other }}"
                                        data-revolution="{{$planDetail['text_revolution']}}"
                                    >
                                        @if ($planDetailProd->role_add == ROLE_OFFICE_MANAGER && $planDetailProd->leave_status == LEAVE_STATUS_2)
                                            {{ __('labels.a203check.delete') }}
                                        @elseif ($planDetailProd->role_add == ROLE_OFFICE_MANAGER && $planDetailProd->leave_status != LEAVE_STATUS_2)
                                            {{ $planDetail['text_revolution'] ?? '' }}
                                        @elseif (($planDetailProd->role_add == ROLE_MANAGER || $planDetailProd->role_add == ROLE_SUPERVISOR)
                                            && ($planDetailProd->leave_status == LEAVE_STATUS_7 || $planDetailProd->leave_status == LEAVE_STATUS_9)
                                        )
                                            {{ LEAVE_STATUS_TYPES[$planDetailProd->leave_status] ?? null }}
                                        @elseif (($planDetailProd->role_add == ROLE_MANAGER || $planDetailProd->role_add == ROLE_SUPERVISOR)
                                            && ($planDetailProd->leave_status != LEAVE_STATUS_7 || $planDetailProd->leave_status != LEAVE_STATUS_9)
                                        )
                                            {{ LEAVE_STATUS_TYPES[$planDetailProd->leave_status] ?? null }} {{ $planDetail['text_revolution'] }}
                                        @endif
                                    </td>
                                @endforeach
                                @php
                                    $white = 0;
                                @endphp
                            </tr>
                        @empty
                            @php
                                $colspan = 3;
                            @endphp
                            @foreach($plans as $plan)
                                @php
                                    $colspan = count($plan->planDetails) + $colspan;
                                @endphp
                            @endforeach
                            <tr>
                                <th colspan="{{ $colspan }}">
                                    {{ __('messages.general.Common_E032') }}
                                </th>
                            </tr>
                        @endforelse
                    </table>
                    <!-- /scroll wrap -->
                    <table class="normal_b mb30">
                        <tr>
                            <th>{{ __('labels.refusal_plans.u203c.explain.explain_value_1') }}</th>
                            <td>{{ __('labels.refusal_plans.u203c.explain.explain_text_1') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.refusal_plans.u203c.explain.explain_value_2') }}</th>
                            <td>{{ __('labels.refusal_plans.u203c.explain.explain_text_2') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.refusal_plans.u203c.explain.explain_value_3') }}</th>
                            <td>{{ __('labels.refusal_plans.u203c.explain.explain_text_3') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.refusal_plans.u203c.explain.explain_value_4') }}</th>
                            <td>{{ __('labels.refusal_plans.u203c.explain.explain_text_4') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.refusal_plans.u203c.explain.explain_value_5') }}</th>
                            <td>{{ __('labels.refusal_plans.u203c.explain.explain_text_5') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.refusal_plans.u203c.explain.explain_value_6') }}</th>
                            <td>{{ __('labels.refusal_plans.u203c.explain.explain_text_6') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.refusal_plans.u203c.explain.explain_value_7') }}</th>
                            <td>{{ __('labels.refusal_plans.u203c.explain.explain_text_7') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('labels.refusal_plans.u203c.explain.explain_value_8') }}</th>
                            <td>{{ __('labels.refusal_plans.u203c.explain.explain_text_8') }}</td>
                        </tr>
                    </table>
                    <p class="center fs12"><a href="#" id="closeModal" onClick="window.close(); return false;" class="btn_a">{{ __('labels.close') }}</a></p>
                </form>
            </div>
            <!-- /contents inner -->
        </div>

        @include('compoments.readonly', [ 'only' => [ROLE_SUPERVISOR, ROLE_MANAGER] ])
        <script src="{{ asset('common/js/functions.js') }}"></script>
        <script>
            const labelClose = '{{ __('labels.close') }}'
            const messsagesErrorChooseNG = '{{ __('messages.general.Common_E058') }}'
            const planIdFirst = @json($plans->first()->id);
            const ROLE_MANAGER = @json(ROLE_MANAGER);
            const ROLE_SUPERVISOR = @json(ROLE_SUPERVISOR);
            const LEAVE_STATUS_TYPES = @json(LEAVE_STATUS_TYPES);
            const LEAVE_STATUS_3 = @json(LEAVE_STATUS_3);
            const LEAVE_STATUS_4 = @json(LEAVE_STATUS_4);
            const LEAVE_STATUS_5 = @json(LEAVE_STATUS_5);
            const LEAVE_STATUS_6 = @json(LEAVE_STATUS_6);
            const LEAVE_STATUS_7 = @json(LEAVE_STATUS_7);
        </script>
        <script type="text/JavaScript" src="{{asset('admin_assets/plan/a203check.js')}}"></script>
    </div>
</body>
</html>
