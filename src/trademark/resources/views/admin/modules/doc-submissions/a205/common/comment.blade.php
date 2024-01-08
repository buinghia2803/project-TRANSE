<dl class="w14em eol clearfix">
    @if (isset($docSubmissionCmts) && $docSubmissionCmts)
        @foreach ($docSubmissionCmts as $docSubmissionCmt)
            <dt>{{  __('labels.a205hiki.title_content') }}</dt>
            <dd style="white-space: pre-line">{{ isset($docSubmissionCmt) && $docSubmissionCmt ? $docSubmissionCmt->parseCreatedAt() : '' }} {{ isset($docSubmissionCmt) && $docSubmissionCmt ? $docSubmissionCmt->content : '' }}</dd>
        @endforeach
    @else
        <dt>{{ __('labels.a205_common.comment.content') }}</dt>
    @endif
</dl>

