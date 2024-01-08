@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.comparison_trademark_result.title') }}</h2>
        @if ($message = Session::get('message'))
            <div id="message_modal" class="modal fade show" role="dialog">
                <div class="modal-dialog" style="min-width: 80%;">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-body">
                            <button type="button" class="close-alert" data-dismiss="modal">&times;</button>
                            <div class="content loaded">
                                <span class="close">&times;</span>
                                <p>{{ $message }}</p>
                                <div class="d-flex justify-content-center">
                                    <button id="btn_ok"> <a href="{{ route('user.top') }}"> {{ __('labels.back') }} </a>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <form action="{{ route('user.refusal.notification.cancel.update', ['id' => $comparisonTrademarkResult->id]) }}"
            method="post">
            @csrf
            <h3 class="eol">{{ __('labels.comparison_trademark_result.text_1') }}<br />
                {{ __('labels.comparison_trademark_result.text_2') }}<br />
                {{ __('labels.comparison_trademark_result.text_3') }}<br />
                <br />
                {{ __('labels.comparison_trademark_result.text_4') }}
            </h3>
            <ul class="footerBtn clearfix">
                @if ($comparisonTrademarkResult->checkResponseDeadline() ||
                    $comparisonTrademarkResult->is_cancel == IS_CANCEL_TRUE)
                    <li>{{ __('labels.comparison_trademark_result.text_5') }}</li>
                @else
                    <li><input type="button" onclick="history.back()" value="{{ __('labels.back') }}" class="btn_a"/></li>
                    <li><input type="submit" value="{{ __('labels.confirm') }}" class="btn_b" /></li>
                @endif
            </ul>
        </form>
    </div>
    <!-- /contents -->
@endsection
@section('script')
    <script>
        const messageServer = @json($message);
        const comparisonTrademarkResult = @json($comparisonTrademarkResult);
        const contentModal = '{{ __('labels.modal_notice_cancel.content') }}';
        const textBtn = '{{ __('labels.modal_notice_cancel.btn') }}';
        if (messageServer) {
            $.confirm({
                title: '',
                content: contentModal,
                buttons: {
                    ok: {
                        text: textBtn,
                        btnClass: 'btn-blue',
                        action: function() {
                            loadingBox('open');
                            window.location.href = '{{ route('user.top') }}';
                        }
                    }
                }
            });
        }
    </script>
@endsection
