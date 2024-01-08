@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">

        <h2>{{ __('labels.u202n.title_page') }}</h2>
        @include('admin.components.includes.messages')

        <form action="{{ route('user.refusal.pre-question.re-reply.post', [
                'id' => $id,
                'reason_question_no' => $reasonQuestionNo->id
            ]) }}" method="POST" id="form">
            @csrf
            <input type="hidden" name="from_page" class="from_page" value=""/>
            <div class="info mb10">
                <h3>{{ __('labels.form_trademark_information.title') }}</h3>
                @include('user.components.trademark-table', [
               'table' => $trademarkTable,
           ])
            </div>

            <dl class="w20em clearfix middle wp-sending_noti_rejection_date">
                <dt class="dt-sending_noti_rejection_date">
                    <span>{{ __('labels.comparison_trademark_result.index.date_1') }}　{{ $comparisonTrademarkResult->sending_noti_rejection_date ? \App\Models\ComparisonTrademarkResult::showDateFormatJapanese($comparisonTrademarkResult->sending_noti_rejection_date) : '' }}</span><br>
                    <span>{{ __('labels.comparison_trademark_result.index.date_2') }}　{{ $comparisonTrademarkResult->response_deadline ? \App\Models\ComparisonTrademarkResult::showDateFormatJapanese($comparisonTrademarkResult->response_deadline) : '' }}</span>
                </dt>
                <dd class="dd-click_file_pdf"><input type="button" value="{{ __('labels.comparison_trademark_result.index.btn') }}" id="click_file_pdf" class="btn_b" /></dd>
            </dl>
            @foreach ($trademarkDocuments as $ele_a)
                <a hidden href="{{ asset($ele_a) }}" class="click_ele_a" target="_blank">{{ $ele_a }}</a>
            @endforeach

            <p class="blue">{{ __('labels.u202.des_1') }}</p>

            {!! __('labels.u202n.des_2') !!}


            <dl class="w16em eol clearfix">
                <dt><h3><strong>{{ __('labels.u202.ams') }}</strong></h3></dt>
                <dd>
                    <h3>
                        <strong>{{ ($reasonQuestionNo && $reasonQuestionNo->user_response_deadline) ? \App\Models\ComparisonTrademarkResult::showDateFormatJapanese($reasonQuestionNo->user_response_deadline) : '' }}</strong>
                    </h3>
                </dd>
            </dl>

            @include('user.modules.comparison-trademark-result.u202n.includes._list_aswer_question', ['reasonQuestionDetails' => $reasonQuestionDetails])

            <hr/>

            @include('user.modules.comparison-trademark-result.u202n.includes._list_aswer_question_old', ['reasonQuestionDetailsOld' => $reasonQuestionDetailsOld])

            <hr/>

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="{{ __('labels.back') }}" class="btn_a saveDraftU202N_Kakunin"/></li>
                <li><input type="submit" value="{{ __('labels.submit') }}" class="btn_b saveU202N_Kakunin"/></li>
            </ul>
        </form>
    </div>
@endsection
@section('css')
    <style>
        @media screen and (max-width: 600px) {
            .wp-sending_noti_rejection_date {
                display: grid !important;
            }

            .dd-click_file_pdf {
                margin: 0 !important;
                margin-bottom: 5px !important;
            }
        }
    </style>
@endsection
@section('script')
    <script>
        const errorMessageRequired = '{{ __('messages.general.Common_E001') }}';
        const errorMessageMaxLength500 = '{{ __('messages.general.Common_E046') }}';
        const u202N_KAKUNIN = @json(U202N_KAKUNIN);
        const u202N_KAKUNIN_DRAFT = @json(U202N_KAKUNIN_DRAFT);
    </script>
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('end-user/comparison_trademark_result/u202n/u202nkakunin.js') }}"></script>
    @if(!$flagEditPage)
        <script src="{{ asset('end-user/common/js/disabled_page.js') }}"></script>
    @endif
@endsection
