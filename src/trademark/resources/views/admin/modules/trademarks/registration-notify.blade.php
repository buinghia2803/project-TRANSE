@extends('admin.layouts.app')
@section('main-content')
<!-- contents -->
<div id="contents">
    <!-- contents inner -->
    <div class="wide clearfix">
        <form id="form" action="{{ route('admin.registration.notify.post', ['id' => $trademark->id, 'maching_result_id' => $matchingResult->id]) }}" method="POST">
            @csrf
            {{-- Trademark table --}}
            @include('admin.components.includes.trademark-table', [
                'table' => $trademarkTable,
            ])

            <h3>{{ __('labels.a301.h3') }}</h3>

            <dl class="w10em clearfix">
                <dt>{{ __('labels.a301.dt_1') }}</dt>
                <dd>{{ __('labels.a301.dd') }}{{ $trademark->application_number ?? null }}</dd>

                <dt>{{ __('labels.a301.dt_2') }}</dt>
                <dd>{{ $ts }}</dd>
                <div class="js-scrollable change_datepicker">
                    <dt>{{ __('labels.a301.dt_3') }}</dt>
                    <dd><input type="text" name="user_response_deadline" id="datepicker" /></dd>
                </div>

                <dt>{{ __('labels.a301.dt_4') }}</dt>
                <dd>
                    {{ $trademarkInfo->name ?? null }} <input type="button" value="{{ __('labels.a301.btn') }}" class="btn_b" id="redirect_a700shutsugannin01"/>
                </dd>
            </dl>

            <ul class="footerBtn clearfix">
                <li>
                    <input type="submit" value="{{ __('labels.a301.submit') }}" class="btn_c submit_a301" />
                </li>
            </ul>

            <ul class="btn_left eol">
                <li>
                    <input type="button" value="{{ __('labels.a301.back') }}" class="btn_a" onclick="window.location = '{{ $backUrl ?? route('admin.home') }}'">
                </li>
            </ul>
        </form>
    </div>
    <!-- /contents inner -->
</div>
<!-- /contents -->
@endsection
@section('footerSection')
    @include('compoments.readonly', [ 'only' => [ ROLE_OFFICE_MANAGER ], 'hasRemoveSubmit' => false ])
    <script>
        const Common_E038 = '{{ __('messages.general.Common_E038') }}';
        const content = @JSON(__('labels.a301.content'));
        const OK = @JSON(__('labels.a301.OK'));
        const cancel = @JSON(__('labels.a301.cancel'));
        const matchingResultDate = @JSON($matchingResult->pi_dd_date ?? null);
        const routeRedirectA700shutsugannin01 = @JSON($route);
    </script>
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/trademark/registration-notify.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        function disableInput () {
            $('#cart').prop('disabled', false);
            $('.checkQuestion').prop('disabled', true).addClass('disabled')
            const form = $('form').not('#form-logout');
            form.find('input, textarea, select, .open_modal_add_reason, .open_modal_add_reason_branch').prop('disabled', true);
            form.find('#click_file_pdf, .submit_a301').prop('disabled', false);
            form.find('button[type=submit], input[type=submit]').prop('disabled', true);
            form.find('.delete, .create_row_code, .add_row_reason').remove();
        }
    </script>
    @if($checkIsConfirm)
        <script>
            disableInput();
        </script>
    @endif
@endsection
