<table class="normal_a eol">
    <caption>{{ __('labels.common_a204.reasons-refusal.caption') }}</caption>
    <tbody>
    <tr>
        <th>{{ __('labels.common_a204.reasons-refusal.th_1') }}</th>
        <td>
            {{ CommonHelper::formatTime($comparisonTrademarkResult->sending_noti_rejection_date ?? null, 'Y/m/d') }}

            <input type="button" value="{{ __('labels.common_a204.reasons-refusal.btn_1') }}" class="btn_b no_disabled" id="click_file_pdf">
            @php
                $trademarkDocuments = $comparisonTrademarkResult->trademark->trademarkDocuments ?? collect([]);
                $trademarkDocuments = $trademarkDocuments->where('type', \App\Models\TrademarkDocument::TYPE_1);
                $trademarkDocuments = $trademarkDocuments->load('noticeDetailBtn.noticeDetail.notice')
                    ->where('noticeDetailBtn.noticeDetail.notice.flow', \App\Models\Notice::FLOW_RESPONSE_REASON)
                    ->where('noticeDetailBtn.noticeDetail.notice.step', \App\Models\Notice::STEP_1);
            @endphp
            @foreach ($trademarkDocuments as $trademarkDocument)
                @if(!empty($trademarkDocument->url))
                    <a href="{{ \App\Helpers\FileHelper::getFileUrl($trademarkDocument->url) }}" class="click_ele_a" target="_blank" hidden></a>
                @endif
            @endforeach

            <input type="button" value="{{ __('labels.common_a204.reasons-refusal.btn_2') }}" class="btn_a no_disabled"
               onclick="window.open('{{ route('admin.refusal.eval-report.create-examine', ['id' => $comparisonTrademarkResult->id, 'type' => VIEW]) }}')"
            >

            <input type="button" value="{{ __('labels.common_a204.reasons-refusal.btn_3') }}" class="btn_a no_disabled"
                onclick="window.open('{{ route('admin.refusal.material.no-material', [
                    'id' => $comparisonTrademarkResult->id,
                    'trademark_plan_id' => $trademarkPlan->id,
                    'type' => VIEW
                ]) }}')"
            >
        </td>
    </tr>
    <tr>
        <th>{{ __('labels.common_a204.reasons-refusal.th_2') }}</th>
        <td>{{ CommonHelper::formatTime($comparisonTrademarkResult->response_deadline ?? '', 'Y/m/d') }}</td>
    </tr>
    </tbody>
</table>
<script>
    $('body').on('click', '#click_file_pdf', function () {
        const tagA = $('.click_ele_a')
        for (const object of tagA) {
            object.click()
        }
    })
</script>
