@extends('admin.layouts.app')

@section('main-content')
    <div id="contents">
        <div class="wide clearfix">
            @include('compoments.messages')

            <form id="form" action="{{ route('admin.refusal.eval-report.edit-examine.post', $comparisonTrademarkResult->id) }}" method="post">
                @csrf

                <h3>{{ __('labels.create_examine.title') }}</h3>
                <input type="hidden" name="reason_no_id" value="{{$reasonNo->id}}">
                <div class="overflow-auto column1">
                    <table class="normal_c color zebra" id="reason-table" style="min-width: 1300px;">
                        <tbody>
                        <tr>
                            <td colspan="5" rowspan="3"></td>
                            @foreach($reasons as $reason)
                                <th style="width:4em;">{{ $reason->reason_name ?? '' }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($reasons as $reason)
                                <th>{{ $reason->mLawsRegulation->name ?? '' }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($reasons as $reason)
                                <th>{{ $reason->mLawsRegulation->rank ?? '' }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            <th style="width:38px;">{{ __('labels.create_examine.distinction_name') }}</th>
                            <th style="width:22em;">{{ __('labels.create_examine.product_name') }}</th>
                            <th style="width:6em;">{{ __('labels.create_examine.rank') }}</th>
                            <th style="width:100px;" nowrap="">{{ __('labels.create_examine.code') }}</th>
                            <th style="width:438px;">{{ __('labels.create_examine.comment_patent_agent') }}</th>
                            @foreach($reasons as $reason)
                                <th class="bg_purple">
                                    <input
                                        type="checkbox"
                                        data-check_all
                                        data-key="{{ $loop->index }}"
                                    >
                                    All
                                </th>
                            @endforeach
                        </tr>
                        @foreach($products as $distinctionName => $item)
                            @php $keyCheck = uniqid(); @endphp
                            <tr>
                                <td rowspan="{{ count($item)+1 }}" class="left">{{ $distinctionName ?? '' }}</td>
                                <th colspan="4" class="bg_gray right row2">{{ __('labels.create_examine.check_all') }}</th>
                                @foreach($reasons as $reason)
                                    <td class="bg_gray">
                                        <input
                                            type="checkbox"
                                            data-check_all_group="{{ $keyCheck }}"
                                            data-key="{{ $loop->index }}"
                                        >
                                        All
                                    </td>
                                @endforeach
                            </tr>
                            @foreach($item as $product)
                                @php
                                    $completedEvaluation = $product->plan_correspondence_prod->completed_evaluation;
                                    $isRegister = $product->plan_correspondence_prod->is_register;
                                    $classReason = $completedEvaluation == false ? 'bg_sky' : 'bg_purple';
                                    $classRow = $completedEvaluation == false ? 'bg_sky' : '';
                                @endphp
                                <tr data-id="{{ $product->id }}"
                                    data-is_register="{{ $isRegister ?? true }}"
                                    data-distinction_id="{{ $product->m_distinction_id }}"
                                    data-is_complete="{{ $completedEvaluation ?? false }}">
                                    <td class="row2 {{ $classRow ?? '' }}">
                                        <p class="mb-0" data-limit_length="{{ MAX_PRODUCT_NAME }}">{{ $product->name ?? '' }}</p>
                                        <input type="hidden" name="products[{{ $product->id }}][plan_correspondence_prod_id]"
                                               value="{{ $product->plan_correspondence_prod->id }}"
                                        >
                                    </td>
                                    <td class="center {{ $classRow ?? '' }}">
                                        <span class="rank">{{ __('labels.create_examine.rank') }}：{{ $product->reason_ref_num_prod->rank ?? '' }}</span>
                                        <input type="hidden" class="rank_value" name="products[{{ $product->id }}][rank]" value="{{ $product->reason_ref_num_prod->rank ?? '' }}">
                                    </td>
                                    <td class="row2 {{ $classRow ?? '' }}" style="position:relative;">
                                        @foreach($product->mCode as $code)
                                            <span class="{{ $loop->index > 2 ? 'hidden' : 'd-block' }}">
                                                {{ $code->name ?? '' }}

                                                @if(count($product->mCode) > 3 && $loop->index == 2)
                                                    <span class="show_all_code cursor-pointer">+</span>
                                                @endif
                                            </span>
                                        @endforeach
                                    </td>
                                    <td class="">
                                        @if($isRegister == true && $completedEvaluation == false)
                                            <textarea class="small2 white w-100 comment_patent_agent" name="products[{{ $product->id }}][comment_patent_agent]">{{ $product->comment_patent_agent ?? null }}</textarea>
                                        @elseif($isRegister == false)
                                            {{ __('labels.create_examine.not_register') }}
                                        @else
                                        @endif
                                        <input type="hidden" name="products[{{ $product->id }}][completed_evaluation]" value="{{ $completedEvaluation ?? '' }}">
                                    </td>
                                    @foreach($reasons as $reason)
                                        <td class="center middle {{ $classReason ?? '' }}">
                                            @if($completedEvaluation == false)
                                                <input
                                                    type="checkbox"
                                                    name="products[{{ $product->id }}][vote_reason_id][]"
                                                    value="{{ $reason->id ?? '' }}"
                                                    data-check_item="{{ $keyCheck }}"
                                                    data-key="{{ $loop->index }}"
                                                    data-rank="{{ $reason->mLawsRegulation->rank }}"
                                                    class="@if($isRegister == false) disabled @endif"
                                                    @if(in_array($reason->id, $product->vote_reason_id)) checked @endif
                                                    @if($isRegister == false) disabled @endif
                                                >
                                            @else
                                                @if(in_array($reason->id, $product->vote_reason_id))
                                                    <span data-check_item="{{ $keyCheck }}" data-key="{{ $loop->index }}" data-rank="{{ $reason->mLawsRegulation->rank }}">✓</span>
                                                @else
                                                    <span data-check_item="{{ $keyCheck }}" data-key="{{ $loop->index }}" class="un_tick"></span>
                                                @endif
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <ul class="rank eol">
                    <li>
                        <span>A</span>
                        {{ __('labels.create_examine.rank_desc.text_a') }}
                    </li>
                    <li>
                        <span>B</span>
                        {{ __('labels.create_examine.rank_desc.text_b') }}
                    </li>
                    <li>
                        <span>C</span>
                        {{ __('labels.create_examine.rank_desc.text_c') }}<br>
                        {{ __('labels.create_examine.rank_desc.text_c_2') }}
                    </li>
                    <li>
                        <span>D</span>
                        {{ __('labels.create_examine.rank_desc.text_d') }}
                    </li>
                    <li>
                        <span>E</span>
                        {{ __('labels.create_examine.rank_desc.text_e') }}
                    </li>
                </ul>

                <dl class="eol">
                    <dt>{!! __('labels.create_examine.reason.reason_1') !!}</dt>
                    <dd class="mb10">{!! __('labels.create_examine.reason.reason_1_desc') !!}</dd>

                    <dt>{!! __('labels.create_examine.reason.reason_2') !!}</dt>
                    <dd class="mb10">{!! __('labels.create_examine.reason.reason_2_desc') !!}</dd>

                    <dt>{!! __('labels.create_examine.reason.reason_3') !!}</dt>
                    <dd class="mb10">{!! __('labels.create_examine.reason.reason_3_desc') !!}</dd>

                    <dt>{!! __('labels.create_examine.reason.reason_4') !!}</dt>
                    <dd class="mb10">{!! __('labels.create_examine.reason.reason_4_desc') !!}</dd>

                    <dt>{!! __('labels.create_examine.reason.reason_5') !!}</dt>
                    <dd class="mb10">{!! __('labels.create_examine.reason.reason_5_desc') !!}</dd>

                    <dt>{!! __('labels.create_examine.reason.reason_6') !!}</dt>
                    <dd class="mb10">{!! __('labels.create_examine.reason.reason_6_desc') !!}</dd>

                    <dt>{!! __('labels.create_examine.reason.reason_7') !!}</dt>
                    <dd class="mb10">{!! __('labels.create_examine.reason.reason_7_desc') !!}</dd>

                    <dt>{!! __('labels.create_examine.reason.reason_8') !!}</dt>
                    <dd class="mb10">{!! __('labels.create_examine.reason.reason_8_desc') !!}</dd>

                    <dt>{!! __('labels.create_examine.reason.reason_9') !!}</dt>
                    <dd class="mb10">{!! __('labels.create_examine.reason.reason_9_desc') !!}</dd>

                    <dt>{!! __('labels.create_examine.reason.reason_10') !!}</dt>
                    <dd>{!! __('labels.create_examine.reason.reason_10_desc') !!}</dd>
                </dl>

                <h5>{{ __('labels.create_examine.content') }}</h5>
                <p>
                    <textarea class="middle_c" name="content">{{ $reasonComment->content ?? '' }}</textarea>
                </p>

                @if(!empty($adminUser) && $adminUser->role == ROLE_MANAGER)
                    <ul class="footerBtn clearfix">
                        <li><input type="submit" name="{{ DRAFT }}" value="{{ __('labels.save') }}" class="btn_b"></li>
                        <li><input type="submit" name="{{ SUBMIT }}" value="{{ __('labels.btn_confirm') }}"
                                   class="btn_c"></li>
                    </ul>
                @endif

                {{--@if(!empty($adminUser) && $adminUser->role == ROLE_SUPERVISOR)--}}
                    {{--<ul class="footerBtn clearfix">--}}
                    {{--    <li><input type="submit" name="{{ SUBMIT_SUPERVISOR }}"--}}
                    {{--               value="{{ __('labels.create_examine.submit_seki') }}" class="btn_c"></li>--}}
                    {{--</ul>--}}
                {{--@endif--}}

                <ul class="footerBtn clearfix">
                    <li>
                        <input type="button" value="{{ __('labels.back') }}" class="btn_a"
                               onclick="window.location = '{{ $backUrl ?? route('admin.home') }}'"
                        >
                    </li>
                </ul>
            </form>
        </div>
    </div>
@endsection
@section('footerSection')
    <script>
        const SUBMIT = '{{ SUBMIT }}';
        const SUBMIT_SUPERVISOR = '{{ SUBMIT_SUPERVISOR }}';
        const errorMessageMaxLength1000 = '{{ __('messages.general.Common_E026') }}';
        const errorMessageRequiredCheckReason = '{{ __('messages.general.correspondence_U201_E001') }}';
    </script>
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script src="{{ asset('admin_assets/pages/comparison_trademark_results/create-examine.js') }}"></script>
    @if (!empty(request()->type) && request()->type == VIEW || $hasSendSeki == true)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ ROLE_MANAGER ] ])
@endsection
