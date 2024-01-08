@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">

            <form method="POST" action="{{ route('admin.support-first-time.update-role-sft', ['id' => $sft->id]) }}" id="form">
                @csrf
                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable
                ])

                <h3>{{ __('labels.support_first_times.prod_service_content') }}</h3>
                <ul class="eol">
                    @if ($sft->StfContentProduct->count())
                        @foreach ($sft->StfContentProduct as $value)
                            <li><label><input type="checkbox" name="sft_content_product_ids[]" class="sft_content_product_ids"
                                        {{ $value->is_choice_admin == IS_CHOICE_ADMIN_CHECKED ? 'checked' : '' }} value="{{ $value->id }}" />
                                    {{ $value->name }}</label></li>
                        @endforeach
                        <li class="errorRequiredSftContentProd"></li>
                    @endif
                </ul>

                <h3>{{ __('labels.support_first_times.title_support_first_time') }}</h3>

                <ul class="btn_left mb20">
{{--                    <li><input type="button" onclick="window.location='{{ route('admin.goods-master-search') }}'" value="{{ __('labels.support_first_times.product_master') }}" class="btn_a" />--}}
{{--                    </li>--}}
                    <li><a href="{{ route('admin.goods-master-search') }}" target="_blank" type="button" class="btn_a">{{ __('labels.product_master') }}</a></li>
                </ul>

                <a name="shutsgan_t"></a>
                <table class="normal_b mb20" style="width: 100%">
                    <caption>
                        {{ __('labels.support_first_times.category_product_name') }}<br />{{ __('labels.support_first_times.final_update') }}{{ date_format($sft->updated_at, 'Y/m/d') }}　{{ $sft->admin ? $sft->admin->name : '' }}
                    </caption>
                    <tr>
                        <th style="width:14%;">{{ __('labels.support_first_times.trademark_info_label') }}</th>
                        <th style="width:43%;">{{ __('labels.support_first_times.product_name') }}</th>
                        <th style="width:43%;">{{ __('labels.support_first_times.group_code') }}</th>
                    </tr>
                    @foreach ($sftSuitableProduct as $key => $value)
                        <tr>
                            <td @if ($value->count() > 1) rowspan={{ $value->count() }} @endif>{{ __('labels.support_first_times.No') }}{{ $key }}{{ __('labels.support_first_times.kind') }}
                            </td>
                            @foreach ($value as $key => $item)
                                @if ($key == 0)
                                    <td
                                        class="{{ $item->mProduct->type == SEMI_CLEAN ? 'bg_pink' : ($item->mProduct->type == CREATIVE_CLEAN ? 'bg_yellow' : '') }}">
                                        {{ $item->name }}</td>
                                    <td
                                        class="{{ $item->mProduct->type == SEMI_CLEAN ? 'bg_pink' : ($item->mProduct->type == CREATIVE_CLEAN ? 'bg_yellow' : '') }}">
                                        @if ($item->mProduct->count() && $item->mProduct->productCode->count())
                                            @foreach ($item->mProduct->productCode as $code)
                                                {{ $code->code_name }}
                                            @endforeach
                                        @endif
                                    </td>
                        @else
                            <tr>
                                <td
                                    class="{{ $item->mProduct->type == SEMI_CLEAN ? 'bg_pink' : ($item->mProduct->type == CREATIVE_CLEAN ? 'bg_yellow' : '') }}">
                                    {{ $item->name }}</td>
                                <td
                                    class="{{ $item->mProduct->type == SEMI_CLEAN ? 'bg_pink' : ($item->mProduct->type == CREATIVE_CLEAN ? 'bg_yellow' : '') }}">
                                    @if ($item->mProduct && $item->mProduct->productCode)
                                        @foreach ($item->mProduct->productCode as $code)
                                            {{ $code->code_name }}
                                        @endforeach
                                    @endif
                                </td>
                            </tr>
                            @endif
                            @endforeach
                            </tr>
                            @endforeach
                </table>
                <!-- /table 区分、商品・サービス名 1 -->
                @if ($sft && $sft->stfComment->count())
                    @foreach ($sft->stfComment as $value)
                        @if ($value->type == $typeCmtInsider)
                            <p>
                                {{ __('labels.support_first_times.comment') }}
                                {{ $value->created_at ? date_format($value->created_at, 'Y/m/d') : '' }}　
                                <span class="white-space-pre-line">{{ $value->content }}</span>
                            </p>
                        @endif
                    @endforeach
                @endif

                <hr />

                <h5>{{ __('labels.support_first_times.customer_comment') }}</h5>
                @if ($sft && $sft->stfComment->count())
                    @foreach ($sft->stfComment as $value)
                        @if ($value->type == $typeCmtCustom)
                            <p class="white-space-pre-line">{{ $value->content }}</p>
                        @endif
                    @endforeach
                @endif

                <ul class="footerBtn clearfix">
                    <li><input type="button" onclick="window.location='{{ route('admin.support-first-time.edit', ['id' => $trademark->id]) }}'" value="{{ __('labels.support_first_times.fix') }}" class="btn_a saveDraft {{ $sft->is_confirm == $isConfirmTrue ? 'disabled-btn' : '' }}" {{ $sft->is_confirm == $isConfirmTrue ? 'disabled' : '' }} /></li>
                    <li><input type="submit" value="{{ __('labels.support_first_times.display_customer') }}"
                               class="btn_b {{ $sft->is_confirm == $isConfirmTrue ? 'disabled-btn' : '' }}" {{ $sft->is_confirm == $isConfirmTrue ? 'disabled' : '' }} /></li>
                </ul>

            </form>

        </div><!-- /contents inner -->
        <!-- modal flagRoleSeki -->
        <div id="flagRoleSeki" class="modal">
            <div class="modal-content modal_validate_prod">
                <span class="close">&times;</span>
                <p class="content">{{ __('labels.label_notice_sft') }}</p>
                <div class="d-flex justify-content-center" style="text-align:center">
                    <button type="button" class="btn_b"
                            id="btn_cancel" onclick="window.location='{{ route('admin.home') }}'">{{ __('labels.to_anken_top') }}</button>
                </div>
            </div>
        </div>

    </div>
    <!-- /contents -->
@endsection
@section('script')
    <script>
        const isConfirmTrue = @json($isConfirmTrue);
        const isConfirmOfSFT = @json($sft->is_confirm);
        const messageFlagRoleSeki = '<?php echo e(__('messages.flug_role_seki')); ?>';
        const messageIsConfirmA011s = '<?php echo e(__('messages.is_confirm_a011s')); ?>';
        const labelAnkenTop = '<?php echo e(__('labels.to_anken_top')); ?>';
        const routeAnkenTop = '<?php echo e(route('admin.home')); ?>';
        const errorPleaseChooseProduct = '{{ __('messages.required_choose_product_all') }}';
    </script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/pages/support_first_time/a011s/validate.js') }}"></script>
    @if ($sft->flag_role == FLAG_ROLE_SEKI && $sft->is_confirm == SFT_IS_CONFIRM)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ ROLE_SUPERVISOR ] ])
@endsection
@section('css')
    <style>
        .modal-content {
            position: relative;
        }

        .close {
            position: absolute;
            top: 0;
            right: 20px;
        }

        .content {
            text-align: center;
        }
        .resize-none {
            resize: none;
        }
        .disabled-btn {
            cursor: not-allowed!important;
        }
        .jconfirm-title {
            font-size: 16px!important;
            line-height: 24px!important;
        }
        #loading_box {
            display: none!important;
        }
        .jconfirm .jconfirm-content div {
            display: none;
        }
        .jconfirm.jconfirm-white .jconfirm-box .jconfirm-buttons, .jconfirm.jconfirm-light .jconfirm-box .jconfirm-buttons {
            float: none!important;
            text-align:center;
        }
    </style>
@endsection
