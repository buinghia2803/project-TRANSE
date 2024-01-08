@extends('admin.layouts.app')

@section('main-content')
<div id="contents">
    <div class="wide clearfix">
        @include('compoments.messages')

        <form action="{{ route('admin.registration.document.modification.skip.post', [
            'id' => $machingResult->id,
            'register_trademark_id' => $registerTrademark->id,
        ]) }}" method="post" id="form">
            @csrf
            <input type="hidden" name="from_page" value="{{ A302_HOSEI02_SKIP }}">

            {{-- Trademark table --}}
            @include('admin.components.includes.trademark-table', [
                'table' => $trademarkTable,
            ])

            <h3>{{ __('labels.a302hosei02skip.h3') }}</h3>

            <h4 class="eol">{{ __('labels.a302hosei02skip.h4') }}</h4>

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="{{ __('labels.a302hosei02skip.button') }}" class="btn_b"/></li>
            </ul>
        </form>
    </div>
</div>
@endsection

@section('footerSection')
    @if($hasSubmit)
        <script>
            disabledScreen();
        </script>
    @endif
    @include('compoments.readonly', [
        'only' => [ROLE_OFFICE_MANAGER],
        'hasRemoveSubmit' => false,
    ])
@endsection
