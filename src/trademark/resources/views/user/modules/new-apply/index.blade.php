@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.new_apply.text_1') }}</h2>
        <p>{{ __('labels.new_apply.text_2') }}</p>
        <ul class="footerBtn">
            <li><input type="submit" name="" data-submit="{{ REDIRECT_TO_SUPPORT_FIRST_TIME }}" value="{{ __('labels.new_apply.button_1') }}"
                    class="btn_b" /></li>
        </ul>

        <ul class="footerBtn">
            <li><input type="button" name="" value="{{ __('labels.new_apply.button_2') }}"
                    class="btn_b" /></li>
        </ul>

        <ul class="footerBtn">
            <li><input type="submit" name="" data-submit="{{ REDIRECT_TO_SEARCH_AI }}" value="{{ __('labels.new_apply.button_3') }}"
                    class="btn_b" /></li>
        </ul>
        <ul class="footerBtn">
            <li><input type="submit" name="" data-submit="{{ REDIRECT_TO_REGISTER_APPLY_TRADEMARK }}" value="{{ __('labels.new_apply.button_4') }}"
                    class="btn_b" /></li>
        </ul>
        <hr />
        <ul class="footerBtn">
            <li><input type="submit" name="" data-submit="{{ REDIRECT_TO_MEMBER_REGISTER_PRE }}" value="{{ __('labels.new_apply.button_5') }}"
                    class="btn_e" />
            </li>
        </ul>
        <!-- form-hidden -->
        <form action="{{ route('user.redirect.menu-new-apply') }}" id="form" method="POST">
            @csrf
            <input type="hidden" name="submit_type" value="">
        </form>
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
    <script>
        // Click Submit
        $('body').on('click', 'input[type=submit]', function(e) {
            e.preventDefault();
            let form = $('#form');
            form.find('input[name=submit_type]').val($(this).data('submit'));
            form.submit();
        });
    </script>
@endsection
