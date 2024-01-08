@extends('user.layouts.app')
@section('css')
<style>
    .alert-danger {
        color: #f00;
    }
</style>
@endsection
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        @if ($isBlockScreen)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close-alert" data-dismiss="alert" aria-label="close">&times;</button>
                {{ __('labels.apply_trademark.message_status') }}
            </div>
        @endif
        @if ($appTradeMark->is_cancel == IS_CANCEL_TRUE)
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close-alert" data-dismiss="alert" aria-label="close">&times;</button>
                {{ __('labels.apply_trademark.message_is_cancel') }}
            </div>
        @endif
        <h2>{{ __('labels.apply_trademark.confirm.title') }}</h2>
        <form action="{{ route('user.apply-trademark.update.confirm', ['id' => $appTradeMark->trademark_id]) }}"
            method="post" id="form">
            @csrf
            <p class="eol">{{ __('labels.apply_trademark.confirm.text_1') }}</p>
            <div class="info mb10">
                {{-- Trademark table --}}
                @include('user.components.trademark-table', [
                    'table' => $trademarkTable,
                ])
            </div>
            <!-- /info -->
            <dl class="w12em eol clearfix">
                <dt>
                    <h3><strong>{{ __('labels.apply_trademark.confirm.text_2') }}</strong></h3>
                </dt>
                <dd>
                    <h3>
                        <strong>{{ \Carbon\Carbon::parse(isset($appTradeMark->cancellation_deadline) ? $appTradeMark->cancellation_deadline : '')->format('Y年m月d日 H:i') }}</strong>
                    </h3>
                </dd>
            </dl>
            <hr />
            <ul class="footerBtn clearfix">
                <li><input type="submit" value="{{ __('labels.apply_trademark.confirm.text_5') }}"
                        {{ $appTradeMark->checkCancellationDeadline() || $appTradeMark->status == STATUS_ADMIN_CONFIRM ? 'disabled' : '' }}
                        class="btn_e big" data-submit="{{ SUBMIT }}" /></li>
            </ul>
            <br />
            <br />
            <ul class="footerBtn clearfix">
                <li>
                    <input type="submit" class="btn_a" data-submit="{{ BACK_URL }}"
                        value="{{ __('labels.apply_trademark.confirm.text_3') }}">
                </li>
                <li>
                    <input type="submit" class="btn_d" data-submit="{{ CANCEL }}"
                        value="{{ __('labels.apply_trademark.confirm.text_4') }}">
                </li>
            </ul>
            <input type="hidden" name="submit_type">
        </form>
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
    @if ($appTradeMark->is_cancel == IS_CANCEL_TRUE || $isBlockScreen)
        <script>
            const form = $('#form').not('#form-logout');
            form.find('input, textarea, select , button').prop('disabled', true);
            form.find('input, textarea, select , button').addClass('disabled')
            form.find('a').css('pointer-events', 'none');
            form.find('a').addClass('disabled');
        </script>
    @endif
    <script type="text/javascript">
        $('#contents').on('click', 'input[type=submit]', function(e) {
            e.preventDefault();
            let form = $('#form');
            let a = form.find('input[name=submit_type]').val($(this).data('submit'));
            form.submit();
        });
    </script>
@endsection
