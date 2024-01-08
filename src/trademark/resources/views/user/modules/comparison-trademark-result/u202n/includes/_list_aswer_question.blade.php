<div class="item-answer-ques">
    @foreach ($reasonQuestionDetails as $k => $item)
        @php
            $index = count($reasonQuestionDetailsOld) + 1 + $k;
        @endphp
        <h3>【{{ __('labels.u202.aswer') }}-{{ $index }}】</h3>

        <p class="eol" style="white-space: pre-line;"> {{ $item->question }}</p>

        <h3>【{{ __('labels.u202.reply') }}{{ $index }}】</h3>

        <input type="hidden" name="data[{{ $k }}][id]" value="{{ $item->id }}"/>
        <div class="eol">
            @if(Route::is('user.refusal.pre-question.re-reply'))
                <textarea class="wide textarea-answer"
                          name="data[{{ $k }}][answer]">{{ old("data.$k.answer", $item->answer) }}</textarea><br/>
                @error("data.$k.answer") <span class="red">{{ $message }}</span> @enderror
            @else
                <div style="white-space: pre-line;">{{ $item->answer }}</div>

                <input type="hidden" name="data[{{ $k }}][answer]" value="{{ $item->answer }}" />
            @endif
            @if(Route::is('user.refusal.pre-question.re-reply'))
                @error("data.$k.answer") <span class="notice">{{ $message }}</span> @enderror
                <div>
                    <input type="button" value="{{ __('labels.u202.btn_upload_files') }}" class="btn_b button-input-file" data-key="{{ $k }}" />
                    <div class="files-select" id="files-select-{{ $k }}"></div>
                </div>
            @endif
            <!-- upload files -->
            @if ($item->attachment)
                <div class="attachment_{{ $k }}">
                    @php
                        $attachments = json_decode($item->attachment, true);
                    @endphp
                    @foreach ($attachments as $attachment)
                        @php
                            if ($attachment) {
                                $convertItem = explode('/', $attachment);
                            }
                        @endphp
                        @if($attachment)
                            <div class="item-file">
                                @if(Route::is('user.refusal.pre-question.reply'))
                                    <img src="{{ asset('common/images/icons8-trash.svg') }}" class="delete-file-icon" data-url="{{ $attachment ?? '' }}" data-id="{{ $item->id }}" />
                                @endif
                                <a href="{{ $attachment ? asset($attachment) : '' }}"
                                target="_blank">{{ isset($attachment) ? $convertItem[count($convertItem) - 1] : '' }}</a>
                                <br>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
            @error("data.$k.attachment") <br><span class="notice">{{ $message }}</span> @enderror
            <div>
                <input hidden type="file" name="data[{{ $k }}][attachment][]" class="input-files input-files-{{ $k }}" id="input-files-{{ $k }}" data-key="{{ $k }}" multiple />
            </div>
        </div>
    @endforeach
</div>
