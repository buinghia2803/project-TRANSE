<h3>{{ __('labels.u202n.lis_data_old_question') }}</h3>
@if($reasonQuestionDetailsOld)
    <div>
        @php
            $count = count($reasonQuestionDetailsOld);
        @endphp
        @foreach ($reasonQuestionDetailsOld as $key => $questionDetail)
            <dl class="w06em clearfix">
                <h4>{{ count($reasonQuestionDetailsOld) - $key }}.</h4>
                <dt> {{ __('labels.a202n_s.question') }}-{{ count($reasonQuestionDetailsOld) - $key }}：</dt>
                <dd class="white-space-cls">{{ $questionDetail->question ?? '' }}</dd>
                <dt class="blue">{{ __('labels.u202n.reply_old') }}-{{ count($reasonQuestionDetailsOld) - $key }}</dt>
                <dd class="blue white-space-cls">{{ $questionDetail->answer ?? '' }}</dd>
                @if ($questionDetail->attachment)
                    @php
                        $attachments = json_decode($questionDetail->attachment, true);
                    @endphp
                   @if(count($attachments) > 0)
                        <div class="data-files">
                            <a href="#" class="click_open_file">
                                {{ __('labels.open_files') }}
                            </a>
                            @foreach ($attachments as $file)
                                <a hidden href="{{ asset($file) }}" class="file_data"
                                   target="_blank">{{ $file }}</a>
                            @endforeach
                        </div>
                   @endif
                @endif
            </dl>
        @endforeach
    </div>
@endif
