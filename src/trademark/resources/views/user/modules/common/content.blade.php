@php
    if (isset($trademark->block_by)) {
        switch ($trademark->block_by) {
            case ALERT_01:
                $route = route('user.refusal.extension-period.alert', ['id' => $trademark->id]);
                $message = __('labels.content.message_alert_01');
                $btn = __('labels.content.btn_1');
                break;
            case OVER_03:
                $route = route('user.refusal.extension-period.alert', ['id' => $trademark->id]);
                $message = __('labels.content.message_over_03');
                $btn = __('labels.content.btn_2');
                break;
            case OVER_04:
                $route = route('user.refusal.extension-period.over', ['id' => $trademark->id]);
                $message = __('labels.content.message_over_04');
                $btn = __('labels.content.btn_2');
                break;
            case OVER_04B:
                $route = route('user.refusal.extension-period.over', ['id' => $trademark->id]);
                $message = __('labels.content.message_over_04b');
                $btn = __('labels.content.btn_2');
                break;
            case OVER_05:
                $route = route('user.apply-trademark-register');
                $message = __('labels.content.message_over_05');
                $btn = __('labels.content.btn_3');
                break;
            default:
                $route = '';
                $message = '';
                $btn = '';
                break;
        }
    }
@endphp
@if (isset($trademark->block_by))
    <p class="alertBox">
        <strong>
            {!! $message !!}
        </strong><br />
        <a href="{{ $route }}" class="btn_b no_disabled" id="btn_content"> {{ $btn }}</a>
    </p>
@endif
@if (isset($trademark->block_by) && in_array($trademark->block_by, [OVER_04, OVER_04B, OVER_05]))
    <style>
        .customer_b {
            color: #777 !important;
        }
    </style>
    @section('script')
        <script>disabledScreen()</script>
    @endsection
@endif
