<table class="normal_a eol">
    @php
        $comparisonTrademarkResult->load('planCorrespondence.reasonNos');
        $reasonNo = null;
        if($comparisonTrademarkResult->planCorrespondence &&  $comparisonTrademarkResult->planCorrespondence->reasonNos) {
            $reasonNo = $comparisonTrademarkResult->planCorrespondence->reasonNos->sortByDesc('id')->first();
        }
    @endphp
    <caption> {{ __('labels.a205_common.reply_reason_refusal.title') }}</caption>
    <tr>
        <th>{{ __('labels.a205_common.reply_reason_refusal.sending_noti_rejecttion_date') }}</th>
        <td>{{ CommonHelper::formatTime($comparisonTrademarkResult->sending_noti_rejection_date ?? '', 'Y/m/d') }}
            <a style="cursor: pointer" class="btn_b mb-2"
                id="openAllFileAttach">{{ __('labels.a205_common.reply_reason_refusal.btn.open_file') }}</a>
            <a class="btn_a mb-2"
                href="{{ route('admin.refusal.eval-report.create-examine', [
                    'id' => $comparisonTrademarkResult->id,
                    'type' => VIEW,
                    'reason_no_id' => $reasonNo ? $reasonNo->id : '',
                ]) }}"
                target="_blank">{{ __('labels.a205_common.reply_reason_refusal.btn.redirect_1') }}</a>
            <a href="{{ route('admin.refusal.material.confirm', [
                'id' => $comparisonTrademarkResult->id,
                'trademark_plan_id' => Request::get('trademark_plan_id'),
                'type' => VIEW,
            ]) }}"
                class="btn_a mb-2" target="_blank">{{ __('labels.a205_common.reply_reason_refusal.btn.redirect_2') }}</a>
            <a href="{{ route('admin.refusal.material.no-material', [
                'id' => $comparisonTrademarkResult->id,
                'trademark_plan_id' => Request::get('trademark_plan_id'),
                'type' => 'view',
            ]) }}"
                class="btn_a mb-2"
                target="_blank">{{ __('labels.a205_common.reply_reason_refusal.btn.redirect_3') }}</a>
        </td>
    </tr>
    <tr>
        <th>{{ __('labels.a205_common.reply_reason_refusal.response_deadline') }}</th>
        <td>{{ CommonHelper::formatTime($comparisonTrademarkResult->response_deadline ?? '', 'Y/m/d') }}
        </td>
    </tr>
</table>
@section('footerSection')
    <script>
        const trademarkDocument = @json($comparisonTrademarkResult->trademark->trademarkDocuments
            ->load('noticeDetailBtn.noticeDetail.notice')
            ->where('noticeDetailBtn.noticeDetail.notice.flow', \App\Models\Notice::FLOW_RESPONSE_REASON)
            ->where('noticeDetailBtn.noticeDetail.notice.step', \App\Models\Notice::STEP_1));

        openAllFileAttach(trademarkDocument);
    </script>
@endsection
