@extends('user.layouts.app')
@section('main-content')
    <div id="contents" class="normal">
        <p>{{ __('messages.user_common_payment.thank_you_v2') }}</p>
        <a href="{{ route('user.top') }}">{{ __('messages.user_common_payment.back') }} &gt;&gt;</a></p>
        <a href="{{ route('user.invoice', ['id' => $paymentId]) }}">{{ __('messages.user_common_payment.invoice_issue') }} &gt;&gt;</a></p>
        <a href="{{ route('user.receipt', ['id' => $paymentId]) }}">{{ __('messages.user_common_payment.receipt_issue') }} &gt;&gt;</a></p>
    </div>
@endsection
