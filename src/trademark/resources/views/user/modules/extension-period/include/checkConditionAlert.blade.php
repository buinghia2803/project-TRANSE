<h3>{{ __('labels.encho.text_3') }}</h3>
<div class="info mb10">
    {{-- Trademark table --}}
    @include('user.components.trademark-table', [
        'table' => $trademarkTable,
    ])
</div>
<!-- /info -->
<dl class="w20em clearfix middle">
    <dt>{{ __('labels.u210.noti_reject') }}{{ $comparisonTrademarkResult->parseSendingNotiRejecttionDate() }}
    </dt>
    <dd>
        <input type="button" value="{{ __('labels.u210.btn_1') }}" class="btn_b" id="openAllFileAttach" />
    </dd>
</dl>
<p>{{ __('labels.u210.response_deadline') }}{{ $comparisonTrademarkResult->parseResponseDeadline() }}</p>
