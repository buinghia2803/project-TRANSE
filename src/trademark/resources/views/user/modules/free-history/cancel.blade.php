@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
            <h2>{{__('labels.free_history.cancel.title_5')}}</h2>
            <form id="form" method="POST"  action="{{ route('user.free-history.cancel') }}">
                @csrf
                <h3 class="eol">{{__('labels.free_history.cancel.title_1')}}<br />
                    {{__('labels.free_history.cancel.title_2')}}<br />
                    {{__('labels.free_history.cancel.title_3')}}<br />
                    <br />
                    {{__('labels.free_history.cancel.title_4')}}</h3>

                <input type="hidden" name="free_history_id" value="{{$freeHistory->id}}">
                <input type="hidden" name="trademark_id" value="{{$trademark->id}}">
                <ul class="footerBtn clearfix">
                    <li><a class="btn_a" style="padding: 4px 2em !important; height: 38px"
                           href="{{route('user.free-history.show-create', $freeHistory->id)}}">{{__('labels.back')}}</a></li>
                    <li><button type="submit" class="btn_b">{{__('labels.confirm')}}</button></li>
                </ul>

            </form>
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
    @if ($freeHistory->is_cancel == IS_CANCEL_TRUE)
        <script>
            const form = $('#form').not('#form-logout');
            form.find('input, textarea, select , button ,a').css('pointer-events', 'none');
            form.find('input, textarea, select , button ,a').addClass('disabled');
        </script>
    @endif
@endsection
