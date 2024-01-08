<h3>{{ __('labels.u202n.lis_data_old_question') }}</h3>
<dl class="w16em clearfix">
    @foreach($reasonQuestionDetailsOld as $k => $item)
        <h4>{{ count($reasonQuestionDetailsOld) - $k }}</h4>
        <dt>{{ __('labels.u202.aswer') }}-{{ count($reasonQuestionDetailsOld) - $k }}：</dt>
        <dd style="white-space: pre-line;">{{ $item->question }}</dd>

        <dt><font color="blue">{{ __('labels.u202n.reply_old') }}-{{ count($reasonQuestionDetailsOld) - $k }}：</font></dt>
        <dd><font color="blue" style="white-space: pre-line;">{{ $item->answer }}</font></dd>
    @endforeach
</dl>
