@extends('user.layouts.app')

@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.u000taikai02.title') }}</h2>
        <form id="form" method="POST">
            @csrf
            @include('admin.components.includes.messages')
            <div id="msg_box" class="alert alert-danger alert-dismissible d-none">
                <button type="button" class="close-alert" data-dismiss="alert" aria-label="close">&times;</button>
                <span id="msg_error"></span>
            </div>
            <p>{{ __('labels.u000taikai02.attention') }}</p>
            <dl class="w08em clearfix">
                <dt>
                    ID <span class="red">*</span>
                </dt>
                <dd>
                    <input type="text" name="info_member_id" value="" />
                </dd>
                <dt>
                    {{ __('labels.u000taikai02.password') }} <span class="red">*</span>
                </dt>
                <dd>
                    <input type="password" name="password" value="" />
                </dd>
                <dt>
                    {{ __('labels.u000taikai02.code') }} <span class="red">*</span>
                </dt>
                <dd>
                    <input type="text" name="code" value="" />
                </dd>
                <dt>{{ __('labels.u000taikai02.reason_withdraw') }}</dt>
                <dd>
                    <textarea name="reason_withdraw" class="middle_b"></textarea>
                </dd>
            </dl>
            <dl class="w08em eol clearfix">
                <dt>
                    {{ __('labels.u000taikai02.checklist') }} <span class="red">*</span>
                </dt>
                <dd>
                    {{ __('labels.u000taikai02.checklist_1') }}...<label>{{ __('labels.u000taikai02.checklist_1_label') }} 
                    <input type="checkbox" class="check_list" /></label>
                </dd>
                <dt>
                </dt>
                <dd>
                    {{ __('labels.u000taikai02.checklist_2') }}...<label>{{ __('labels.u000taikai02.checklist_2_label') }} 
                    <input type="checkbox" class="check_list" /></label>
                </dd>
                <dt></dt>
                <dd>
                    {{ __('labels.u000taikai02.checklist_3') }}...<label>{{ __('labels.u000taikai02.checklist_3_label') }} 
                    <input type="checkbox" class="check_list" /></label>
                    <div class="check_list-error red">

                    </div>
                </dd>
            </dl>
            <p>
                <a href="{{ route('user.withdraw.anken-list') }}">{{ __('labels.u000taikai02.gonna_anken_list') }} &gt;&gt;</a>
            </p>
            <ul class="footerBtn clearfix">
                <li>
                    <a href="{{ route('user.top') }}" style="padding:0; text-align: center; line-height:38px" class="btn_a btn_cancel">
                        {{ __('labels.u000taikai02.cancel') }}
                    </a>
                </li>
                <li>
                    <button type="button" style="font-size: 1.3em;" class="btn_d btn_withdraw">{{ __('labels.u000taikai02.submit') }}</button>
                </li>
            </ul>
        </form>
    </div>
    <style>
        .btn_cancel {
            height: 38px;
            width: 166px;
        }
        .btn_withdraw {
            height: 38px;
            width: 112px;
        }
        .d-none {
            display: none;
        }
        .d-block {
            display: block;
        }
    </style>
    <!-- /contents -->
@endsection
@section('script')
    <script>
        const errorMessageIsValidRequired = '{{ __('messages.general.Common_E001') }}';
        const errorMessageValidPassword = '{{ __('messages.general.Common_E005') }}';
        const errorMessageMaxLength255 = '{{ __('messages.general.Common_E029') }}';
        const messageCheckPlease = '{{ __('messages.u000taikai02.check_please') }}';
        const errorMessageIsValidInfoMemberId = '{{ __('messages.general.Common_E007') }}';
        const errorMessagePasswordInvalid = '{{ __('messages.common.errors.Common_E005') }}'
        const routePreConfirm = @json(route('user.withdraw.pre-confirm.verification'));
        const token = @json(request()->__get('token'));
    </script>
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/withdraw/u000taikai02.js') }}"></script>
@endsection