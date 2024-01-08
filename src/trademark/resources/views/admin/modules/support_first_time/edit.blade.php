@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            <form action="" method="POST" id="form" autocomplete="off">
                @csrf
                @include('admin.components.includes.messages')

                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable
                ])

                <h3>{{ __('labels.admin_sft_edit.title_h3') }}</h3>

                <p>{{ __('labels.admin_sft_edit.prod_sft_content') }}</p>
                <ul class="eol">
                    @if ($stfContentProds = $supFirstTimeData->StfContentProduct)
                        @foreach ($stfContentProds as $item)
                            <li>
                                <label>
                                    @if ($sftKeepData)
                                        <input type="checkbox" name="stf_content_product_ids[]" class="stf_content_product_ids" {{ in_array($item->id, json_decode($sftKeepData->content_product, true)) ? 'checked' : '' }} value="{{ $item->id }}" /> {{ $item->name }}
                                    @else
                                        <input type="checkbox" name="stf_content_product_ids[]" class="stf_content_product_ids" {{ $item->is_choice_admin == \App\Models\SFTContentProduct::IS_CHOICE_ADMIN_TRUE ? 'checked' : '' }} value="{{ $item->id }}" /> {{ $item->name }}
                                    @endif
                                </label>
                            </li>
                        @endforeach
                        <li class="errorRequiredSftContentProd"></li>
                    @endif
                </ul>

                <ul class="btn_left mb20">
                    <li><a href="{{ route('admin.goods-master-search') }}" target="_blank" type="button" class="btn_a">{{ __('labels.product_master') }}</a></li>

                </ul>
                <a name="shutsgan_t"></a>

                <!--type action when submit-->
                <input type="hidden" name="code" id="code-button" value="" />

                @if ($supFirstTimeData->sftKeepData && $supFirstTimeData->sftKeepData->sftKeepDataProds->count() > 0)
                    @include('admin.modules.support_first_time.edit._table_keep_data', ['supFirstTimeData' => $supFirstTimeData, 'distinctions' => $distinctions])
                @else
                    @include('admin.modules.support_first_time.edit._table', ['supFirstTimeData' => $supFirstTimeData, 'distinctions' => $distinctions])
                @endif

                <!-- /table 区分、商品・サービス名 1 -->
                @foreach($supFirstTimeData->stfComment as $comment)
                    <p class="eol">{{ __('labels.support_first_times.comment') }}{{ $comment->created_at ? \Carbon\Carbon::parse($comment->created_at)->format('Y/m/d') : '' }}　<span class="white-space-pre-line">{{ $comment->content }}</span></p>
                @endforeach

                <!--comment in sft_keep_datas table -->
                @if ($supFirstTimeData->sftKeepData && $supFirstTimeData->sftKeepData->sftKeepDataProds->count() > 0)
                    <h5>{{ __('labels.support_first_times.customer_comment') }}</h5>
                    <p>
                        <textarea class="normal" name="comments[to_user]" type="{{ $cmtTypeCustomer }}">{{ $sftKeepData ? $sftKeepData->comment_from_ams : '' }}</textarea>
                    </p>

                    <h5>{{ __('labels.precheck.comment_internal') }}</h5>
                    <p><textarea class="normal" name="comments[to_admin]" type="{{ $cmtTypeInsider }}">{{ $sftKeepData ? $sftKeepData->comment_internal : '' }}</textarea></p>
                @else
                    <!-- comment in sft_comments -->
                    <h5>{{ __('labels.precheck.comment_send_customers') }}</h5>
                    <p>
                        <textarea class="normal" name="comments[to_user]" type="{{ $cmtTypeCustomer }}">{{ $commentAMS ? $commentAMS->content : '' }}</textarea>
                    </p>

                    <h5>{{ __('labels.precheck.comment_internal') }}</h5>
                    <p><textarea class="normal" name="comments[to_admin]" type="{{ $cmtTypeInsider }}"></textarea></p>
                @endif

                <ul class="footerBtn clearfix">
                    <li><input type="button" onclick="history.back()" value="{{ __('labels.back') }}" class="btn_a" /></li>
                    <li><input type="submit" value="{{ __('labels.save') }}" class="btn_b saveDraft" /></li>
                    <li><input type="submit" value="{{ __('labels.admin_sft_edit.submit_to_end_user') }}" class="btn_c saveNotice" /></li>
                </ul>
                @include('admin.modules.support_first_time._modal')
            </form>

        </div><!-- /contents inner -->

    </div><!-- /contents -->
@endsection
@section('script')
    <script>
        const isConfirmTrue = @json($isConfirmTrue);
        const isConfirmOfSFT = @json($sft->is_confirm);
        const notice = @json(NOTICE_CODE);
        const flugSftKeepData = @json($flugSftKeepData);
        const routeUpdateIsBlockAjax = "{{ route('admin.sft-suitable-product.update-is-block-ajax') }}";
        const routeUpdateTypeMProduct = '{{ route('admin.m-product.update-type-ajax') }}';
        const routeMproductGetCodeAndDistinction = '{{ route('admin.m-product.get-code-and-distinction') }}';
        const routeAnkenTop = '{{ route('admin.home') }}';
        const SuggestURL = '{{ route('admin.sft.suggest-product') }}';
        const SuggestURLItem = '{{ route('admin.sft.suggest-product-item') }}';
        const isBlockConst = "{{ \App\Models\SFTSuitableProduct::IS_BLOCK }}";
        const notIsBlockConst = "{{ \App\Models\SFTSuitableProduct::NOT_IS_BLOCK }}";
        const support_A011_E005 = '{{ __('messages.support_first_times.support_A011_E005') }}';
        const DISTINCTION = '{!! json_encode($distinctions ?? []) !!}';
        const MPRODUCTS = '{!! json_encode($mProducts ?? []) !!}';
        const mProductType3 = "{{ \App\Models\MProduct::TYPE_CREATIVE_CLEAN }}";
        const mProductType4 = "{{ \App\Models\MProduct::TYPE_SEMI_CLEAN }}";
        const notIsDecision = "{{ \App\Models\SFTKeepDataProd::NOT_IS_DECISION }}";
        const draftIsDecision = "{{ \App\Models\SFTKeepDataProd::DRAFT_IS_DECISION }}";
        const editIsDecision = "{{ \App\Models\SFTKeepDataProd::EDIT_IS_DECISION }}";
        const errorMessageMaxLength500 = '{{ __('messages.common.errors.Common_E024') }}';
        const errorMessageFormatCode = '{{ __('messages.general.support_A011_E003') }}'
        const errorMessageFormatProduct = '{{ __('messages.general.support_U011_E001') }}';
        const messageFlagRoleSeki = '{{ __('messages.is_confirm_a011s') }}';
        const labelAnkenTop = '{{ __('labels.to_anken_top') }}';
        const errorPleaseChooseProduct = '{{ __('messages.required_choose_product_all') }}';
        const errorNotCheckAllIsBlock = '{{ __('messages.precheck.error_not_select_suitable_u021n') }}';
        const errorNotIsDecisionAllData = '{{ __('messages.error_not_is_decision_all_data') }}';
        const disabledChecked = '{{ __('labels.a203shu.check_disabled') }}';
        const addCodeText = '{{ __('labels.create_support_first_time.add_code') }}';
        const decisionText = '{{ __('labels.decision') }}';
        const deleteText = '{{ __('labels.delete') }}';
        const confirmText = '{{ __('labels.confirm') }}'
        const backLabel = '{{__('labels.back')}}';
        const confirmTextEdit = '{{__('labels.admin_sft_edit.confirm')}}';
        const errorMessageNothingInDecision = '{{ __('messages.support_first_time.message_nothing_in_decision') }}';
        const errorMessageYouCanEnter100Items = '{{ __('messages.you_can_enter_up_to_100_items') }}';
        const limitAddRowConst = @json(LIMIT_ADD_ROW);
    </script>
    <script src="{{ asset('admin_assets/pages/support_first_time/edit/validate.js') }}"></script>
    <script src="{{ asset('admin_assets/pages/support_first_time/edit/index.js') }}"></script>
    <script src="{{ asset('admin_assets/pages/support_first_time/edit/ajax.js') }}"></script>
    <script>
        $(document).ready(function() {
            if (isConfirmTrue == isConfirmOfSFT) {
                $.confirm({
                    title: '',
                    content: messageFlagRoleSeki,
                    buttons: {
                        ok: {
                            text: labelAnkenTop,
                            btnClass: 'btn-blue',
                            action: function () {
                                window.location.href=routeAnkenTop
                            }
                        }
                    }
                });
            }
        })
    </script>
    @include('compoments.readonly', [ 'only' => [ ROLE_SUPERVISOR ] ])
@endsection
@section('css')
    <style>
        .pointer-events-none {
            pointer-events: none;
        }
        select {
            margin-bottom: 5px;
        }
        .error {
            display: block;
        }

        .boxes {
            position: relative;
        }

        .search-suggest {
            position: absolute;
            top: 27px;
            left: 0;
            right: 0;
            z-index: 1001;
            background: #fff;
            box-shadow: 0 10px 25px -5px rgb(31 31 31 / 50%);
            opacity: 1;
            visibility: visible;
            transition: .2s;
        }

        .open {
            opacity: 1;
            visibility: visible;
            height: 100vh;
        }

        .item {
            margin: 2px 10px;
            cursor: pointer;
        }

        .item:hover {
            background-color: #ddd;
        }

        .no_item {
            display: flex;
            justify-content: center;
            margin: 10px;
        }

        .search-suggest {
            overflow: auto;
            max-height: 200px;
        }

        .prod_code {
            margin-bottom: 5px;
        }
        .prod_code:last-child {
            margin-bottom: 5px;
        }

        .add_code {
            border: none;
            background: no-repeat;
            text-decoration: underline;
            cursor: pointer;
        }

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
        .bg_pink {
            background: #f1b5d0;
        }
        .bg_yellow {
            background: #fff9ab;
        }
        .bg_gray {
            background: #dfdfdf!important;
        }
        .select2-container {
            width: 100% !important;
        }
        .select2-results {
            font-size: 14px;
        }
        .search-suggest {
            position: absolute;
            top: 27px;
            left: 0;
            right: 0;
            z-index: 1001;
            background: #fff;
            box-shadow: 0 10px 25px -5px rgb(31 31 31 / 50%);
            opacity: 1;
            visibility: visible;
            transition: .2s;
        }
        .search-suggest {
            overflow: auto;
            max-height: 200px;
        }
        .search-suggest__list .item {
            padding: 5px 10px;
            cursor: pointer;
        }
        .search-suggest__list .item:hover {
            background: #f5f5f5;
        }
        .w135 {
            width: 135px;
        }
        .add_code_old_data, #add_prod, #add_prod_free {
            border: none;
            background: no-repeat;
            text-decoration: underline;
            cursor: pointer;
        }
        button[disabled=disabled], button:disabled {
            opacity: 0.65;
            cursor: not-allowed;
        }
        .error-validate {
            display: block;
            font-size: 11px;
        }
        .prod_code {
            width: 10em!important;
        }
        .jconfirm-title {
            font-size: 16px!important;
            line-height: 24px!important;
        }
        #loading_box {
            display: none!important;
        }
        .jconfirm.jconfirm-white .jconfirm-box .jconfirm-buttons, .jconfirm.jconfirm-light .jconfirm-box .jconfirm-buttons {
            float: none!important;
            text-align:center;
        }
        .no_item {
            display: none;
        }
    </style>
@endsection
