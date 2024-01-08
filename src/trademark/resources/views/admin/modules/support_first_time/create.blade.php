@extends('admin.layouts.app')
@section('headerSection')
    <link rel="stylesheet" href="{{ asset('common/css/custom-css.css') }}">
@endsection
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            <form id="form" action="{{ route('admin.support-first-time.store', $trademark->id) }}" method="POST"
                autocomplete="off">
                @csrf
                @include('admin.components.includes.messages')

                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable,
                ])

                <input name="code" type="hidden" id="code-button" value="" />

                <h3>{{ __('labels.create_support_first_time.title') }}</h3>
                <p>{{ __('labels.create_support_first_time.content_of_product') }}</p>
                <ul class="eol">
                    @if ($sft->StfContentProduct->count())
                        @foreach ($sft->StfContentProduct as $j => $value)
                            <li><label><input type="checkbox" name="is_choice_admin[]" class="is_choice_admin"
                                        value="{{ $value->id }}"
                                        {{ $value->is_choice_admin == $isChoiceAdminTrue ? 'checked' : '' }} />
                                    {{ $value->name }}
                                </label></li>
                        @endforeach
                        <li class="errorRequiredSftContentProd"></li>
                    @endif
                </ul>
                <ul class="btn_left mb20">
                    <li><a href="{{ route('admin.goods-master-search') }}" target="_blank" type="button"
                            class="btn_a">{{ __('labels.product_master') }}</a></li>
                </ul>
                <a name="shutsgan_t"></a>

                @include('admin.modules.support_first_time.create._table', [
                    'sft' => $sft,
                    'sftSuitableProduct' => $sftSuitableProduct,
                ])
                <!-- /table 区分、商品・サービス名 1 -->
                <h5>{{ __('labels.create_support_first_time.comment_AMS') }}</h5>
                <p class="eol">
                    <textarea class="normal resize-none" name="comment[0][content]">{{ $commentAMS ? $commentAMS->content : '' }}</textarea>
                </p>
                <input hidden type="text" value="2" name="comment[0][type]" class="btn_b" />

                <h5>{{ __('labels.precheck.comment_internal') }}</h5>
                <p class="eol">
                    <textarea class="normal resize-none" name="comment[1][content]">{{ $commentInsider ? $commentInsider->content : '' }}</textarea>
                </p>
                <input hidden type="text" value="1" name="comment[1][type]" class="btn_b" />


                <ul class="footerBtn clearfix">
                    <li><input type="button" onclick="window.location='{{ route('admin.home') }}'" value="{{ __('labels.back') }}"
                            class="btn_a" /></li>
                    <li><input type="submit" value="{{ __('labels.save') }}" name="save"
                            class="btn_b saveDraft {{ $sft->flag_role == $flagRoleSeki ? 'disabled-btn' : '' }}"
                            {{ $sft->flag_role == $flagRoleSeki ? 'disabled' : '' }} /></li>
                    <li><input type="submit" value="{{ __('labels.btn_confirm') }}" name="save"
                            class="btn_c saveNotice {{ $sft->flag_role == $flagRoleSeki ? 'disabled-btn' : '' }}"
                            {{ $sft->flag_role == $flagRoleSeki ? 'disabled' : '' }} /></li>
                </ul>

                <!-- Trigger/Open The Modal -->
                <!-- modal require name prod -->
                <div id="validate_modal" class="modal">
                    <div class="modal-content modal_validate_prod">
                        <span class="close">&times;</span>
                        <p class="content">{{ __('labels.create_support_first_time.title_duplicate_modal') }}</p>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn_b" id="btn_ok">{{ __('labels.agent.btn_ok') }}</button>

                            <button type="button" class="btn_d ml-2"
                                id="btn_cancel">{{ __('labels.agent.btn_cancel') }}</button>
                        </div>
                    </div>
                </div>

                <!-- modal duplicate name prod -->
                <div id="duplicate_modal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <p class="content">{{ __('labels.create_support_first_time.title_required_modal') }}</p>
                        <div class="d-flex justify-content-center">
                            <button type="button" class="btn_b" id="btn_confirm">{{ __('labels.agent.btn_ok') }}</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>

    </div>
    <!-- /contents inner -->
    </div>
    <!-- /contents -->
@endsection
@section('script')
    <script type="text/JavaScript">
        const view = @json(VIEW);
        const flugRole = @json($sft->flag_role);
        const flagRoleSeki = @json($flagRoleSeki);
        const notice = @json(NOTICE_CODE);
        const request = @json(Request::get('type'));
        const errorMessageMaxLength500 = '{{ __('messages.common.errors.Common_E024') }}';
        const errorMessageYouCanEnter100Items = '{{ __('messages.you_can_enter_up_to_100_items') }}';
        const errorCheckAll = '{{ __('messages.check_all') }}';
        const errorSelectProductNameAndEdit = '{{ __('messages.general.support_A011_E004') }}';
        const errorRequiredProduct = '{{ __('messages.required_product') }}';
        const errorUniqueProduct = '{{ __('messages.unique_product') }}';
        const errorIsValid = '{{ __('messages.common.errors.support_U011_E001') }}';
        const SuggestURL = '{{ route('admin.sft.suggest-product') }}';
        const SuggestURLItem = '{{ route('admin.sft.suggest-product-item') }}';
        const errorMessageFormatCode = '{{ __('messages.general.support_A011_E003') }}'
        const errorMessageFormatProduct = '{{ __('messages.general.support_U011_E001') }}';
        const messageFlagRoleSeki = '{{ __('messages.support_first_time.message_flag_role_seki_a011') }}';
        const routeAnkenTop = '{{ route('admin.home') }}';
        const labelAnkenTop = '{{ __('labels.to_anken_top') }}';
        const deleteAllText = '{{ __('labels.delete_all') }}';
        const btnCancel = '{{ __('labels.btn_ok') }}';
        const confirmText = '{{ __('labels.confirm') }}';
        const deleteText = '{{ __('labels.delete') }}';
        const editText = '{{ __('labels.edit') }}';
        const addCodeText = '{{ __('labels.create_support_first_time.add_code') }}';
        let backLabel = '{{__('labels.back')}}';
        const limitAddRowConst = @json(LIMIT_ADD_ROW);
    </script>
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/pages/support_first_time/create/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/pages/support_first_time/create/index.js') }}"></script>
    <style>
        .disabled-input {
            cursor: not-allowed !important;
        }

        .bg_gray {
            background: #dfdfdf !important;
        }

        .jconfirm.jconfirm-white .jconfirm-box .jconfirm-buttons,
        .jconfirm.jconfirm-light .jconfirm-box .jconfirm-buttons {
            float: none;
            text-align: center;
        }

        .disabled-btn {
            cursor: not-allowed !important;
        }

        .error {
            display: block;
        }

        .boxes {
            position: relative;
        }

        .search-suggest {
            position: absolute;
            top: 67px;
            left: 0;
            right: 0;
            z-index: 1001;
            background: #fff;
            box-shadow: 0 10px 25px -5px rgb(31 31 31 / 50%);
            opacity: 1;
            visibility: visible;
            transition: .2s;
        }

        .bg_pink .search-suggest {
            top: 30px;
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
            margin-top: 5px;
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

        .resize-none {
            resize: none;
        }

        textarea {
            height: 150px !important;
            color: #000000 !important;
        }

        .jconfirm-title {
            font-size: 16px !important;
            line-height: 24px !important;
        }

        #loading_box {
            display: none !important;
        }
        .no_item {
            display: none;
        }
    </style>
    <script>
        const DISTINCTION = '{!! json_encode($distinction ?? []) !!}';
    </script>
    @include('compoments.readonly', ['only' => [ROLE_MANAGER]])
    @if (Request::get('type') == VIEW || $sft->flag_role == $flagRoleSeki)
        <script>disabledScreen();</script>
    @endif
@endsection
