@extends('admin.layouts.app')
@php
$user = auth()->user();
@endphp
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            <form action="{{ route('admin.product.redirect_page', ['id' => $id, 's' => $key]) }}" method="POST"
                id="form">
                @csrf
                <div class="info eol">
                    {{-- Trademark table --}}
                    @include('admin.components.includes.trademark-table', [
                        'table' => $trademarkTable,
                    ])
                </div>
                <h3>{{ __('labels.document_modification.text_1') }}</h3>
                <iframe src="{{ route('admin.list_product', ['s' => $key]) }}" width="70%" height="400"></iframe>
                <ul class="footerBtn clearfix mt-2" style="width: 70%;">
                    <li style="float: left;"><input type="submit" value="{{ __('labels.document_modification.btn_submit') }}" class="btn_b" @if($user->role == ROLE_SUPERVISOR) disabled @endif/>
                    </li>
                    <li style="float: right;">
                        <a style="color: white" href="{{ route('admin.list_product', ['s' => $key]) }}" target="_blank" @if($user->role == ROLE_SUPERVISOR) disabled @endif
                            class="btn_b custom_btn">{{ __('labels.document_modification.view_product') }}</a>
                    </li>
                </ul>
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}" >
                <input type="hidden" name="register_trademark_id" value="{{ $registerTrademark->id ?? '' }}" >
            </form>
        </div>
        <!-- /contents inner -->
    </div>
    <!-- /contents -->
    @include('compoments.readonly', [
        'only' => [ROLE_OFFICE_MANAGER],
        'hasRemoveSubmit' => false,
        ])
    <style>
        .custom_btn {
            padding: 5px 2rem !important;
        }
    </style>
@endsection
