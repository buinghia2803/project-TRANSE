<dl class="w10em eol clearfix change_datepicker">
    <dt>{{ __('labels.refusal.eval_report_create_reason.time_reply_response_notice_of_customer') }}</dt>
    <dd class="b-user_response_deadline">
        <input type="text" name="user_response_deadline" id="datepicker" />
    </dd>
</dl>

<table class="normal_b mb05" id="createQuestionTbl">
    <thead>
    <tr>
        <th class="th_no">{{ __('labels.create_support_first_time.index') }}</th>
        <th class="th_question">{{ __('labels.a202n_s.question') }}</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($reasonQuestionDetails as $k => $item)
        @php
           $index = $k + 1;
           if ($reasonQuestionDetailsOld && count($reasonQuestionDetailsOld) > 0) {
              $index += count($reasonQuestionDetailsOld);
           }
         @endphp
        <tr class="tr_question">
            <td class="center"><span class="index-data">{{ $index }}</span>.<br>
                <input type="button" value="{{ __('labels.create_support_first_time.delete') }}" class="small btn_d btn_delete_question_old_data reson-question-{{ $k }}">
                <input type="hidden" name="data[{{ $k }}][reason_question_detail_id]" value="{{ $item->id }}"/>
                <input type="checkbox" name="data[{{ $k }}][delete_status]" class="delete_status_old_data" style="display: none"/>
            </td>
            <td><textarea name="data[{{ $k }}][question]" class="middle_b input_question">{{ $item->question }}</textarea></td>
        </tr>
    @endforeach
    </tbody>
</table>
<p class="eol"><a href="javascript:void(0)" id="add_new_question">{{ __('labels.a202n_s.add_new') }}</a></p>

<br><br>

<p class="eol">
    {{ __('labels.support_first_times.comment') }}<br>
    <textarea class="middle_c" name="content" id="content">{{ $reasonCommentOld ? $reasonCommentOld->content : '' }}</textarea>
</p>
