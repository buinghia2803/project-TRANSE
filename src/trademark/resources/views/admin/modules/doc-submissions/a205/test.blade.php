@extends('admin.layouts.app')
@section('main-content')
    <!-- contents inner -->
    <div class="wide clearfix" style="text-align:left;">

        <form id="form" method="POST" enctype="multipart/form-data">
            @csrf
            @include('admin.modules.doc-submissions.a205.common.a205shu02window')

            <input type="submit" value="123456" class="btn_d" />
        </form>

    </div><!-- /contents -->
@endsection
@section('footerSection')
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/JavaScript">
        let rules = {}
        let messages = {}

        if (commonA205Shu02Rules != undefined) {
            rules = {...rules, ...commonA205Shu02Rules};
        }
        if (commonA205Shu02Messages != undefined) {
            messages = {...messages, ...commonA205Shu02Messages};
        }
        new clsValidation('#form', { rules: rules, messages: messages })
    </script>
@endsection
