<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ __('labels.reset_password') }}</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@300;400;700&display=swap" rel="stylesheet">
    {{-- Core JS Footer --}}
    <link rel="stylesheet" href="{{ asset('common/css/admin.css') }}">
</head>

<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="/"><b>{{ config('app.name') }}</b></a>
    </div>

    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg">{{ __('labels.reset_password') }}</p>

            @include('admin.components.includes.messages')

            <form action="{{ route('admin.set-password') }}" id="form" method="post">
                {{ csrf_field() }}
                <input type="hidden" name="token" value="{{ request()->token ?? '' }}">

                <div class="form-group">
                    <input type="password" class="form-control" name="password" id="password" placeholder="{{ __('labels.password') }}">
                    @error('password') <span class="error">{{ $message ?? '' }}</span> @enderror
                </div>

                <div class="form-group">
                    <input type="password" class="form-control" name="password_confirm" id="password_confirm" placeholder="{{ __('labels.password_confirm') }}">
                    @error('password_confirm') <span class="error">{{ $message ?? '' }}</span> @enderror
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">{{ __('labels.confirm') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('admin.auth.partials.language')
</div>

<!-- jQuery 2.2.3 -->
<script src="{{ asset('admin_assets/themes/plugins/jquery/jquery.min.js') }}"></script>
<!-- Bootstrap 3.3.6 -->
<script src="{{ asset('admin_assets/themes/bootstrap/js/bootstrap.min.js') }}"></script>
{{-- jquery-validation --}}
<script src="{{ asset('admin_assets/themes/plugins/jquery-validation/jquery.validate.min.js') }}"></script>
{{-- Core JS Footer --}}
<script src="{{ asset('admin_assets/js/functions.min.js') }}"></script>
{{-- Validate --}}
<script src="{{ asset('admin_assets/js/validate.min.js') }}"></script>
<script type="text/javascript">
    const errorMessagePasswordRequired = '{{ __('validation.required', ['attribute' => __('labels.password')]) }}';
    const errorMessageValidPassword = '{{ __('messages.valid_password') }}';
    const errorMessagePasswordMaxCharacter = '{{ __('validation.max.string', ['attribute' => __('labels.password'), 'max' => '32']) }}';
    const errorMessagePasswordMinCharacter = '{{ __('validation.min.string', ['attribute' => __('labels.password'), 'min' => '8']) }}';

    const errorMessagePasswordConfirmRequired = '{{ __('validation.required', ['attribute' => __('labels.password_confirm')]) }}';
    const errorMessagePasswordConfirmEqualTo = '{{ __('validation.same', ['attribute' => __('labels.password_confirm'), 'other' => __('labels.password')]) }}';

    validation('#form', {
        'password': {
            required: true,
            minlength : 8,
            maxlength : 32,
            isValidPassword: true,
        },
        'password_confirm': {
            required: true,
            equalTo: '#password'
        },
    }, {
        'password': {
            required: errorMessagePasswordRequired,
            minlength : errorMessagePasswordMinCharacter,
            maxlength : errorMessagePasswordMaxCharacter,
            isValidPassword: errorMessageValidPassword,
        },
        'password_confirm': {
            required: errorMessagePasswordConfirmRequired,
            equalTo: errorMessagePasswordConfirmEqualTo
        },
    });
</script>
</body>

</html>
