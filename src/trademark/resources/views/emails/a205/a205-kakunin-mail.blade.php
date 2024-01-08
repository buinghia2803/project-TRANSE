<div>
    <div>【{{ $trademark->trademark_number }}】{{ __('labels.a205kakunin.title_mail')}}</div>
    <div>{{ __('labels.a205kakunin.for_tanto')}}</div>
    <div class="list-comment">
        @include('admin.modules.doc-submissions.a205.common.comment', [
                   'docSubmissionCmts' => $docSubmission ? $docSubmission->docSubmissionCmts : collect([])
       ])
    </div>
</div>
