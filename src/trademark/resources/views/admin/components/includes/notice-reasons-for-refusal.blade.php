<table class="normal_a eol">
    <caption>{{ __('labels.maching_results.caption') }}</caption>
    <tr>
        <th>{{ __('labels.pre_question.index.th_1') }}</th>
        <td colspan="3">{{ $comparisonTrademarkResult->sending_noti_rejection_date ? \Carbon\Carbon::parse($comparisonTrademarkResult->sending_noti_rejection_date)->format('Y/m/d') : '' }}
            <input type="button" value="{{ __('labels.comparison_trademark_result.index.btn') }}"
                   id="click_file_pdf" class="btn_b"/>
            @foreach ($trademarkDocuments as $ele_a)
                <a hidden href="{{ asset($ele_a) }}" class="click_ele_a"
                   target="_blank">{{ $ele_a }}</a>
            @endforeach

            <a href="{{ route('admin.refusal.eval-report.create-examine', ['id' => $comparisonTrademarkResult->id, 'type' => 'view'])}}" target="_blank">
                <input type="button" value="{{ __('labels.pre_question.index.btn_2') }}" class="btn_a">
            </a>
    </tr>
    <tr>
        <th>{{ __('labels.refusal.eval_report_create_reason.time_limit_reply_notice') }}</th>
        <td colspan="3">{{ $comparisonTrademarkResult->response_deadline ? Carbon\Carbon::parse($comparisonTrademarkResult->response_deadline)->format('Y/m/d') : '' }}</td>
    </tr>
</table>

<script>
    $('#click_file_pdf').click(function () {
        const a = $('.click_ele_a')
        for (const object of a) {
            object.click()
        }
    })
</script>
