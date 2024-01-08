<style>
    .customer_a {
        border: none;
        background-color: unset;
        color: #00233a;
        text-decoration: underline;
        cursor: pointer;
    }
    .customer_a:hover{
        text-decoration: unset;
    }
</style>
@php
    $isSubmit = $isSubmit ?? true;
@endphp
{{-- Notice the reason for the refusal --}}
    @if (Route::is([
        'admin.refusal.response-plan.index', // a203
        'admin.refusal.response-plan.product.create', // a203c
        'admin.refusal.response-plan.product.edit.supervisor', // a203c_shu
        'admin.refusal.response-plan.product.re-create.supervisor', // a203c_n
        'admin.refusal.response-plan.edit.supervisor', // a203shu
        'admin.refusal.response-plan.supervisor', // a203s
        'admin.refusal.response-plan.supervisor-reject', // a203sashi
        'admin.refusal.response-plan-re.supervisor', // a203n
    ]))
        <table class="normal_a eol">
            <caption>
                {{ __('labels.common_a203.common_notice.caption') }}
            </caption>
            <tbody>
                <tr>
                    <th>{{ __('labels.common_a203.common_notice.th_1') }}</th>
                    <td>
                        {{ CommonHelper::formatTime($dataCommon['comparisonTrademarkResult']->sending_noti_rejection_date ?? '', 'Y/m/d') }}
                        <input type="button" value="{{ __('labels.common_a203.common_notice.btn_1') }}" class="btn_b" id="click_file_pdf"/>
                        @foreach ($dataCommon['trademarkDocuments'] as $ele_a)
                            <a hidden href="{{ asset($ele_a) }}" class="click_ele_a" target="_blank">{{ $ele_a }}</a>
                        @endforeach
                        <input type="button" value="{{ __('labels.common_a203.common_notice.btn_2') }}" class="btn_a" onclick="window.open('{{$route ?? route('admin.refusal.eval-report.create-examine', ['id' => $dataCommon['comparisonTrademarkResult']->id, 'type' => VIEW]) }}')"/>
                    </td>
                </tr>
                <tr>
                    <th>{{ __('labels.common_a203.common_notice.th_2') }}</th>
                    <td>
                        {{ CommonHelper::formatTime($dataCommon['comparisonTrademarkResult']->response_deadline ?? '', 'Y/m/d') }}
                    </td>
                </tr>
            </tbody>
        </table>
    @endif
{{-- End notice the reason for the refusal --}}

{{-- Customer reply deadline --}}
    @if (Route::is([
        'admin.refusal.response-plan.index', // a203
        'admin.refusal.response-plan.product.create', // a203c
        'admin.refusal.response-plan.product.edit.supervisor', // a203c_shu
        'admin.refusal.response-plan.product.re-create.supervisor', // a203c_n
        'admin.refusal.response-plan.edit.supervisor', // a203shu
        'admin.refusal.response-plan.supervisor', // a203s
        'admin.refusal.response-plan.supervisor-reject', // a203sashi
        'admin.refusal.response-plan-re.supervisor', // a203n
    ]))
        @if (Route::is([
            'admin.refusal.response-plan.supervisor', // a203s
        ]))
            <h3>{{ __('labels.common_a203.common_reply.h3_datepicker_a203s') }}</h3>
        @elseif(Route::is([
            'admin.refusal.response-plan.supervisor-reject' // a203sashi
        ]))
            <h3>{{ __('labels.common_a203.common_reply.h3_datepicker_a203sashi') }}</h3>
        @elseif(Route::is([
            'admin.refusal.response-plan.product.edit.supervisor' // a203c_shu
        ]))
            <h3>{{ __($title ?? 'labels.common_a203.common_reply.h3_seki') }}</h3>
        @else
            <h3>{{ __($title ?? 'labels.common_a203.common_reply.h3') }}</h3>
        @endif

        <dl class="w16em eol clearfix js-scrollable change_datepicker">
            <dt>{{ __('labels.common_a203.common_reply.dt_1') }}</dt>
            <dd><input type="text" name="response_deadline" id="datepicker" /></dd>
        </dl>

        <input type="hidden" name="link_redirect" value="">
        <input type="hidden" name="name_page" value="">
        <dl class="w10em mb20 clearfix">
            <dt>{{ __('labels.common_a203.common_reply.dt_2') }}</dt>
            <dd>
                <button
                    type="button"
                    name="submit"
                    class="open-modal customer_a"
                    data-route="{{ A202N_S }}"
                    value="draft"
                    data-src-iframe={{ route('admin.refusal.pre-question-re.supervisor.show', ['id' => $dataCommon['comparisonTrademarkResult']->id, 'is_modal' => true, 'type' => VIEW])}}
                >
                    {{ __('labels.common_a203.common_reply.a_1') }}
                </button>
            </dd>
            <dt>{{ __('labels.common_a203.common_reply.dt_3') }}</dt>
            <dd>
                @if ($isSubmit)
                    <button
                        type="submit"
                        name="submit"
                        class="open-modal customer_a"
                        data-route="{{ A203S }}"
                        value="draft"
                        data-src-iframe={{ route('admin.refusal.response-plan.supervisor', ['id' => $dataCommon['comparisonTrademarkResult']->id, 'trademark_plan_id' => $dataCommon['trademarkPlans']->id ?? null, 'is_modal' => true, 'type' => VIEW])}}
                    >{{ __('labels.common_a203.common_reply.a_1') }}</button> |
                @else
                    <button
                        type="button"
                        name="submit"
                        class="open-modal customer_a"
                        data-route="{{ A203S }}"
                        value="draft"
                        data-src-iframe={{ route('admin.refusal.response-plan.supervisor', ['id' => $dataCommon['comparisonTrademarkResult']->id, 'trademark_plan_id' => $dataCommon['trademarkPlans']->id ?? null, 'is_modal' => true, 'type' => VIEW])}}
                    >{{ __('labels.common_a203.common_reply.a_1') }}</button> |
                @endif
                @if ($isSubmit)
                    <button
                        type="submit"
                        name="submit"
                        value="draft"
                    class="redirect_url customer_a"
                    data-redirect="{{ $dataCommon['routeA203OrA203shu'] }}"
                    >{{ __('labels.common_a203.common_reply.a_2') }}</button>
                @else
                    <button
                        type="button"
                        name="submit"
                        value="draft"
                    class="redirect_url customer_a"
                    data-redirect="{{ $dataCommon['routeA203OrA203shu'] }}"
                    >{{ __('labels.common_a203.common_reply.a_2') }}</button>
                @endif
            </dd>

            <dt>{{ __('labels.common_a203.common_reply.dt_4') }}</dt>
            <dd>
                @if ($isSubmit)
                    <button
                        type="submit"
                        name="submit"
                        value="draft"
                        class="open-modal customer_a"
                        data-route="{{ A203CHECK }}"
                        data-src-iframe={{ route('admin.refusal.response-plan.modal-a203check', ['id' => $dataCommon['comparisonTrademarkResult']->id, 'trademark_plan_id' => $dataCommon['trademarkPlans']->id ?? null])}}
                    >{{ __('labels.common_a203.common_reply.a_1') }}</button>
                    |
                @else
                    <button
                        type="button"
                        name="submit"
                        value="draft"
                        class="open-modal customer_a"
                        data-route="{{ A203CHECK }}"
                        data-src-iframe={{ route('admin.refusal.response-plan.modal-a203check', ['id' => $dataCommon['comparisonTrademarkResult']->id, 'trademark_plan_id' => $dataCommon['trademarkPlans']->id ?? null])}}
                    >{{ __('labels.common_a203.common_reply.a_1') }}</button>
                    |
                @endif
                @if ($isSubmit)
                    <button
                        type="submit"
                        name="submit"
                        value="draft"
                        class="redirect_url customer_a"
                        data-redirect="{{ $dataCommon['routeA203cOrA203c_shu'] }}"
                    >{{ __('labels.common_a203.common_reply.a_2') }}</button>
                @else
                    <button
                        type="button"
                        name="submit"
                        value="draft"
                        class="redirect_url customer_a"
                        data-redirect="{{ $dataCommon['routeA203cOrA203c_shu'] }}"
                    >{{ __('labels.common_a203.common_reply.a_2') }}</button>

                @endif
            </dd>

            <dt>{{ __('labels.common_a203.common_reply.dt_5') }}</dt>
            <dd>
                @if ($isSubmit)
                    <button
                        type="submit"
                        name="submit"
                        value="draft"
                        class="open-modal customer_a"
                        data-route="{{ A203C_RUI }}"
                        data-src-iframe={{ route('admin.refusal.response-plan.product-group', ['id' => $dataCommon['trademarkPlans'] ? $dataCommon['trademarkPlans']->id : '0000', 'is_modal' => 1, 'type' => VIEW])}}
                    >{{ __('labels.common_a203.common_reply.a_1') }}</button>
                    |
                @else
                    <button
                        type="button"
                        name="submit"
                        value="draft"
                        class="open-modal customer_a"
                        data-route="{{ A203C_RUI }}"
                        data-src-iframe={{ route('admin.refusal.response-plan.product-group', ['id' => $dataCommon['trademarkPlans'] ? $dataCommon['trademarkPlans']->id : '0000', 'is_modal' => 1, 'type' => VIEW])}}
                    >{{ __('labels.common_a203.common_reply.a_1') }}</button>
                    |
                @endif
                @if ($isSubmit)
                    <button
                        type="submit"
                        name="submit"
                        value="draft"
                        class="redirect_url customer_a"
                        data-redirect="{{ route('admin.refusal.response-plan.product-group', ['id' => $dataCommon['trademarkPlans'] ? $dataCommon['trademarkPlans']->id : '0000']) }}"
                    >{{ __('labels.common_a203.common_reply.a_2') }}</button>
                @else
                    <button
                        type="button"
                        name="submit"
                        value="draft"
                        class="redirect_url customer_a"
                        data-redirect="{{ route('admin.refusal.response-plan.product-group', ['id' => $dataCommon['trademarkPlans'] ? $dataCommon['trademarkPlans']->id : '0000']) }}"
                    >{{ __('labels.common_a203.common_reply.a_2') }}</button>
                @endif
            </dd>

            <dt>{{ __('labels.common_a203.common_reply.dt_6') }}</dt>
            <dd>
                @if ($isSubmit)
                    <button
                        type="submit"
                        name="submit"
                        value="draft"
                        class="open-modal customer_a"
                        data-route="{{ U203 }}"
                        data-src-iframe={{ route('user.refusal.response-plan.refusal_response_plan', [
                            'comparison_trademark_result_id' => $dataCommon['comparisonTrademarkResult']->id,
                            'trademark_plan_id' => $dataCommon['trademarkPlans']->id ?? null,
                            'show' => ADMIN_ROLE,
                            'type' => VIEW
                        ])}}
                    >{{ __('labels.common_a203.common_reply.a_1') }}</button>
                @else
                    <button
                        type="button"
                        name="submit"
                        value="draft"
                        class="open-modal customer_a"
                        data-route="{{ U203 }}"
                        data-src-iframe={{ route('user.refusal.response-plan.refusal_response_plan', [
                            'comparison_trademark_result_id' => $dataCommon['comparisonTrademarkResult']->id,
                            'trademark_plan_id' => $dataCommon['trademarkPlans']->id ?? null,
                            'show' => ADMIN_ROLE,
                            'type' => VIEW
                        ])}}
                    >{{ __('labels.common_a203.common_reply.a_1') }}</button>
                @endif
            </dd>
        </dl>

        <div id="open-modal-iframe" class="open-modal-iframe modal fade" role="dialog">
            <div class="modal-dialog" style="min-width: 90%; min-height: 90%;">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header" style="height: 52px;">
                        <button type="button" class="close-alert" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <div class="content loaded">
                            <iframe class="src-iframe" src="" style="width: 100%; height: 84vh;" frameborder="0"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <p><a
            class="btn_a
            {{ $dataCommon['btnReasonForRefusalN'] ? '' : 'disabled' }}"
            href="{{ $dataCommon['btnReasonForRefusalN'] ? $dataCommon['routeA205kakunin'] : 'javascript:void(0)' }}"
            target="{{ $dataCommon['btnReasonForRefusalN'] ? '_blank' : '' }}"
        >
            {{ __('labels.common_a203.common_reply.a_3') }}
        </a></p>
    @endif
{{-- End customer reply deadline --}}

{{-- Content of refusal reasons --}}

    @if (Route::is([
        'admin.refusal.response-plan.index',
        'admin.refusal.response-plan.edit.supervisor',
        'admin.refusal.response-plan.supervisor', // a203s
        'admin.refusal.response-plan.supervisor-reject', // a203sashi
        'admin.refusal.response-plan-re.supervisor',
    ]))
        <h3>{{ __('labels.common_a203.common_content.h3') }}</h3>
        @foreach ($dataCommon['reasons'] ?? [] as $item)
            <p>
                {{ __('labels.common_a203.common_content.p', ['attr' => $item->reason_name ?? '']) }}<br />
                {{ !empty($item->mLawsRegulation->id) ? $dataCommon['mLawRegulationContentDefault'][$item->mLawsRegulation->id] : ''}}
            </p>
        @endforeach
    @endif
{{-- End Content of refusal reasons --}}

<script>
    const Common_E001 = '{{ __('messages.general.Common_E001') }}';
    const wrong_format = '{{ __('messages.general.wrong_format') }}';
    const Common_E038 = '{{ __('messages.general.Common_E038') }}';
    const responseDeadline = @JSON($dataCommon['responseDeadline']);
    const comparisonTrademarkResultResponseDeadline = @JSON($dataCommon['comparisonTrademarkResult']->response_deadline);
    const OPEN_MODAL = '{{ request()->modal ?? '' }}';
    const IS_SUBMIT = @JSON($isSubmit);

    const common203Rule  = {
        response_deadline: {
            required: true,
        },
    }
    const common203Message = {
        response_deadline: {
            required: Common_E001,
        },
    }
</script>
<script type="text/JavaScript" src="{{ asset('admin_assets/plan/common.js') }}"></script>
