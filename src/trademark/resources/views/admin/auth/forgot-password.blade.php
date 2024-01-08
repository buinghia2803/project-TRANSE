<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>{{ __('labels.forgot_password') }}</title>
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
            <p class="login-box-msg">{{ __('labels.forgot_password') }}</p>

            @include('admin.components.includes.messages')

            <form action="{{ route('admin.set-forgot-password') }}" id="form" method="post">
                {{ csrf_field() }}

                <div class="form-group">
                    <input type="text" class="form-control" name="email" placeholder="{{ __('labels.email') }}" value="{{ old('email') }}">
                    @error('email') <span class="error">{{ $message ?? '' }}</span> @enderror
                </div>

                <div class="row">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-block">{{ __('labels.submit') }}</button>
                        <a href="{{ route('admin.login') }}" class="btn btn-link btn-block">{{ __('labels.login') }}</a>
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
    const errorMessageEmailRequired = '{{ __('validation.required', ['attribute' => __('labels.email')]) }}';
    const errorMessageIsValidEmail = '{{ __('validation.email', ['attribute' => __('labels.email') ]) }}';

    validation('#form', {
        'email': {
            required: true,
            isValidEmail: true,
        },
    }, {
        'email': {
            required: errorMessageEmailRequired,
            isValidEmail: errorMessageIsValidEmail,
        },
    });
</script>
</body>

</html>
