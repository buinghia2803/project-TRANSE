@extends('admin.layouts.app')
@section('main-content')
    <div id="contents">
        <br>
        <div>
            {{ __('labels.import_02.import_completed') }}
        </div>
        <br>
        <a style="border:none;" href="{{ route('admin.home')}}">
            <button type="button" class="btn_a">
                {{ __('labels.import_02.back_top') }}
            </button></li>
        </a>
    </div>
@endsection
