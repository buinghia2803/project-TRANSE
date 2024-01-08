@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.signup.page.title') }}</h2>

        @include('admin.components.includes.messages')

        <form action="{{ route('auth.register') }}" id="form" method="POST" enctype="multipart/form-data" autocomplete="off">
            @csrf

            <p class="eol">
                {{ __('labels.signup.page.desc_1') }}<br />
                {{ __('labels.signup.page.desc_2') }}<br />
                {{ __('labels.signup.page.desc_3') }}<br />
                <span class="red">*</span>{{ __('labels.signup.page.desc_4') }}
            </p>

            <dl class="w10em eol clearfix">
                <dt>{{ __('labels.signup.form.name_trademark') }}</dt>
                <dd>
                    <input type="text" name="name_trademark" value="{{ old('name_trademark') }}" class="em30" />
                </dd>

                <dt></dt>
                <dd>
                    <input type="checkbox" name="is_image_trademark" value="1"
                        {{ old('is_image_trademark') == 1 ? 'checked' : '' }}>
                    {{ __('labels.signup.form.is_image_trademark') }}<br />
                    {{ __('labels.signup.form.is_image_trademark_desc') }}
                </dd>

                <dt>{{ __('labels.signup.form.email') }} <span class="red">*</span></dt>
                <dd>
                    <input type="text" name="email" value="{{ old('email') }}" class="em30 mt_custom" nospace/>
                    @error('email')
                    <div class="notice">{{ $message }}</div>
                    @enderror
                    <p><span class="red">{{ __('messages.signup.text_email') }}</span></p>
                </dd>
            </dl>

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="{{ __('labels.signup.form.submit') }}" class="btn_b" /></li>
            </ul>
        </form>
    </div>
@endsection
@section('headerSection')
    <link rel="stylesheet" href="{{ asset('common/css/custom-css.css') }}">
    <style>
        .close {
            margin-top: 11px;
        }
    </style>
@endsection
@section('footerSection')
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        const errorMessageEmailRequired = '{{ __('messages.signup.form.required') }}';
        const errorMessageEmailMaxLength = '{{ __('messages.signup.form.max_length') }}';
        const errorMessageName = '{{ __('messages.signup.form.Register_U001_E006') }}';
        const errorMessageEmail = '{{ __('messages.signup.form.Common_E002') }}';

        validation('#form', {
            'email': {
                required: true,
                maxlength: 255,
                isValidEmail: true,
            },
            'name_trademark': {
                isOnlySpaceNameFullwidth: true,
                maxlength: 30,
            },
        }, {
            'email': {
                required: errorMessageEmailRequired,
                maxlength: errorMessageEmailMaxLength,
                isValidEmail: errorMessageEmail
            },
            'name_trademark': {
                isOnlySpaceNameFullwidth: errorMessageName,
                maxlength: errorMessageName,
            },
        });

        $('body').on('change', 'input[name=is_image_trademark]', function(e) {
            e.preventDefault();
            let nameTrademark = $('input[name=name_trademark]');
            if ($(this).prop('checked')) {
                nameTrademark.val('').prop('disabled', true);
            } else {
                nameTrademark.val('').prop('disabled', false);
            }
        });
    </script>
@endsection
