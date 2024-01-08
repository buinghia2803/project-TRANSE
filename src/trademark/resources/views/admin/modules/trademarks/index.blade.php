@extends('admin.layouts.app')

@section('main-content')
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            @include('compoments.messages')
            <h2>{{ __('labels.application_detail.title') }}</h2>

            {{-- Trademark table --}}
            @include('admin.components.includes.trademark-table', [
                'table' => $trademarkTable
            ])

            {{-- Free History --}}
            <h3>{{ __('labels.application_detail.free_history.title') }}</h3>
            @if(in_array($adminUser->role, [ROLE_OFFICE_MANAGER, ROLE_SUPERVISOR]))
                <p><a class="btn_b" href="{{ route('admin.free-history.create', $trademark->id) }}">{{ __('labels.application_detail.free_history.create') }}</a></p>
            @endif
            @if(!empty($cancelInfo))
                @if($cancelInfo['is_cancel'] == true && !empty($cancelInfo['type']))
                    <p><a href="#" data-cancel_type="{{ $cancelInfo['type'] ?? '' }}" data-id="{{ $cancelInfo['id'] ?? '' }}">{{ __('labels.application_detail.free_history.reload') }}</a></p>
                @endif
            @endif

            <input type="text" id="session_file" value="{{session('s')}}" name="s" hidden />

            {{-- History --}}
            <div class="eol">
                <div class="overflow-auto" style="max-height: 500px;">
                    <table class="normal_b w-100" id="history-table">
                        <tr>
                            <th style="width:6em;">
                                {{ __('labels.application_detail.notices.notice_created_at') }}
                                <a href="{{ route('admin.application-detail.index', [
                            'id' => $trademark->id,
                            'orderField' => 'notice_created_at',
                            'orderType' => SORT_TYPE_DESC,
                        ]) }}">▼</a>
                                <a href="{{ route('admin.application-detail.index', [
                            'id' => $trademark->id,
                            'orderField' => 'notice_created_at',
                            'orderType' => SORT_TYPE_ASC,
                        ]) }}">▲</a>
                            </th>
                            <th style="width:6em;">
                                {{ __('labels.application_detail.notices.attribute') }}
                            </th>
                            <th style="width:20em;">
                                {{ __('labels.application_detail.notices.content') }}
                            </th>
                            <th style="width:6em;">
                                {{ __('labels.application_detail.notices.detail_response_deadline') }}
                                <a href="{{ route('admin.application-detail.index', [
                            'id' => $trademark->id,
                            'orderField' => 'detail_response_deadline_raw',
                            'orderType' => SORT_TYPE_DESC,
                        ]) }}">▼</a>
                                <a href="{{ route('admin.application-detail.index', [
                            'id' => $trademark->id,
                            'orderField' => 'detail_response_deadline_raw',
                            'orderType' => SORT_TYPE_ASC,
                        ]) }}">▲</a>
                            </th>
                            <th style="width:6em;">
                                {{ __('labels.application_detail.notices.completion_date') }}
                                <a href="{{ route('admin.application-detail.index', [
                            'id' => $trademark->id,
                            'orderField' => 'completion_date',
                            'orderType' => SORT_TYPE_DESC,
                        ]) }}">▼</a>
                                <a href="{{ route('admin.application-detail.index', [
                            'id' => $trademark->id,
                            'orderField' => 'completion_date',
                            'orderType' => SORT_TYPE_ASC,
                        ]) }}">▲</a>
                            </th>
                            <th style="width:8em;">
                                {{ __('labels.application_detail.notices.actor') }}
                            </th>
                            <th style="width:24em;">
                                {{ __('labels.application_detail.notices.action') }}
                            </th>
                            <th>{{ __('labels.application_detail.notices.comment') }}</th>
                        </tr>

                        @forelse($notices as $item)
                            <tr
                                class="{{ ($loop->index >= PAGE_LIMIT_10) ? 'hidden' : '' }}"
                                style="{{ (isset($item->is_cancel) && $item->is_cancel == true) ? 'background: #f1b5d0;' : '' }}"
                                data-notice_id="{{ $item->id }}"
                                data-maching_result_id="{{ $item->maching_result_id }}"
                                data-response_deadline="{{ $item->response_deadline }}"
                                data-comparison_trademark_result_id="{{ $item->comparison_trademark_result_id }}"
                            >
                                <td>{{ $item->notice->created_at->format('Y/m/d') }}</td>
                                <td>{{ $item->attribute ?? '' }}</td>
                                <td>
                                    @if(!empty($item->redirect_page))
                                        @if(strpos($item->redirect_page, '/sysmanagement/application-detail/') == false)
                                            <a href="{{ $item->redirect_page ?? '' }}">{{ $item->content ?? '' }}</a>
                                        @else
                                            {{ $item->content ?? '' }}
                                        @endif
                                    @else
                                        {{ $item->content ?? '' }}
                                    @endif
                                </td>
                                <td>{{ $item->detail_response_deadline ?? '' }}</td>
                                <td>{{ \CommonHelper::formatTime($item->completion_date ?? '', 'Y/m/d', '－') }}</td>
                                <td>{{ $item->actor ?? '' }}</td>
                                <td>
                                    @include('admin.modules.trademarks.partials.notice-detail-btns', [
                                        'noticeDetailBtns' => $item->noticeDetailBtns ?? [],
                                        'isCancel' => $trademarkCancel ?? $item->is_cancel ?? false,
                                    ])
                                </td>
                                <td>
                                    <p class="mb-2" style="white-space: pre-line;"> {{ $item->comment }}</p>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="center">{{ __('messages.general.Common_E032') }}</td>
                            </tr>
                        @endforelse
                    </table>
                </div>
                @if($notices->count() > PAGE_LIMIT_10)
                    <a href="#" data-show_table="#history-table">+ {{ __('labels.admin_top.see_more') }}</a>
                @endif
            </div>

            <div class="eol">
                <p class="mb-2">{{ __('labels.application_detail.payment.title') }}</p>

                <div class="overflow-auto" style="max-height: 500px;">
                    <table class="normal_b" id="payment-list">
                        <tr>
                            <th style="width:6em;">{{ __('labels.application_detail.payment.created_at') }}</th>
                            <th style="width:20em;">{{ __('labels.application_detail.payment.content') }}</th>
                            <th style="width:10em;">{{ __('labels.application_detail.payment.payment_type') }}</th>
                        </tr>
                        @forelse($payments as $payment)
                            <tr class="{{ ($loop->index >= 10) ? 'hidden' : '' }}">
                                <td>{{ \CommonHelper::formatTime($payment->payment_date ?? '', 'Y/m/d') }}</td>
                                <td>{!! $payment->content ?? '' !!}</td>
                                <td>{{ $payment->payment_type ?? '' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="center">{{ __('messages.general.Common_E032') }}</td>
                            </tr>
                        @endforelse
                    </table>
                </div>
                @if($payments->count() > PAGE_LIMIT_10)
                    <a href="#" data-show_table="#payment-list">+ {{ __('labels.admin_top.see_more') }}</a>
                @endif
            </div>

        </div><!-- /contents inner -->
    </div><!-- /contents -->
@endsection

@section('headSection')
    <style>
        input[disabled] {
            cursor: default !important;
            opacity: 0.6;
        }
        .pdf_upload:hover.disabled {
            opacity: 0.6 !important;
            cursor: not-allowed !important;
        }
    </style>
@endsection

@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script>
        const Ankentop_A000_E001 = '{{ __('messages.general.Ankentop_A000_E001') }}';
        const Import_A000_E001 = '{{ __('messages.general.Import_A000_E001') }}';
        const OK = '{{ __('labels.btn_ok') }}';
        const CANCEL = '{{ __('labels.btn_cancel') }}';
        const updateSuccess = '{{ __('messages.update_success') }}';

        const errorMessageMax20 = '{{ __('messages.general.Import_A000_E001') }}';
        const errorMessageIsValidPDF = '{{ __('messages.general.Common_E037') }}';
        const errorMessageIsValidXML = '{{ __('messages.general.Common_E040') }}';

        const fileXMLErrMessage = @json(session('File_XML_err'));
        if (fileXMLErrMessage) {
            $.confirm({
                title: '',
                content: fileXMLErrMessage,
                buttons: {
                    cancel: {
                        text: OK,
                        btnClass: 'btn-default',
                        action: function () {}
                    },
                }
            });
        }

        const messageUploadSubmit = @json(session('message_upload_submit'));
        if (messageUploadSubmit) {
            $.confirm({
                title: '',
                content: messageUploadSubmit,
                buttons: {
                    cancel: {
                        text: OK,
                        btnClass: 'btn-default',
                        action: function () {}
                    },
                }
            });
        }

        const errorUploadMessage = @json(session('message_upload'));
        const session = @json(session('s'));
        const urlStep = @json(route('admin.notice-detail-btns.upload-xml', ['id' => session('btn_notice_id') ?? 0]));
        if (errorUploadMessage) {
            $.confirm({
                title: '',
                content: errorUploadMessage,
                buttons: {
                    cancel: {
                        text: "キャンセル",
                        btnClass: 'btn-default',
                        action: function () {
                            loadAjaxPost(urlStep, {
                                s:session,
                                cancel: true
                            }, {
                                beforeSend: function(){},
                                success:function(result){},
                                error: function (error) {}
                            }, 'loading');
                        }
                    },
                    ok: {
                        text: "はい",
                        btnClass: 'btn-primary',
                        action: function () {
                            loadAjaxPost(urlStep, {
                                s:session
                            }, {
                                beforeSend: function(){},
                                success:function(result){
                                    $.confirm({
                                        title: '',
                                        content: result.message_upload ?? updateSuccess,
                                        buttons: {
                                            cancel: {
                                                text: "OK",
                                                btnClass: 'btn-default',
                                                action: function () {
                                                    location.reload();
                                                }
                                            }
                                        }
                                    });
                                },
                                error: function (error) {
                                    if(error.responseJSON && error.responseJSON.message) {
                                        $.confirm({
                                            title: '',
                                            content: error.responseJSON.message,
                                            buttons: {
                                                cancel: {
                                                    text: "キャンセル",
                                                    btnClass: 'btn-default',
                                                    action: function () {}
                                                }
                                            }
                                        });
                                    }
                                }
                            }, 'loading');
                        }
                    }
                }
            });
        }

    </script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/pages/notice_detail_btns/action.js') }}"></script>
    <script>
        $.each($('.btn_contact_customer'), function() {
            let isClicked = $(this).data('date_click');
            if(isClicked == true) {
                $(this).closest('.button-group').find('.pdf_upload').prop('disabled', true).addClass('disabled');
            }
        });
    </script>

    <script>
        const URL_RESTORE = '{{ route('admin.application-detail.restore', $trademark->id) }}';

        const RESTORE_CONFIRM = '{{ __('labels.application_detail.restore_confirm') }}';

        $('body').on('click', '[data-cancel_type]', function (e) {
            e.preventDefault();
            let type = $(this).data('cancel_type');
            let id = $(this).data('id');

            $.confirm({
                title: '',
                content: RESTORE_CONFIRM,
                buttons: {
                    cancel: {
                        text: CANCEL,
                        btnClass: 'btn-default',
                        action: function () {}
                    },
                    ok: {
                        text: OK,
                        btnClass: 'btn-blue',
                        action: function () {
                            loadAjaxPost(URL_RESTORE, {
                                type: type,
                                id: id,
                            }, {
                                beforeSend: function(){},
                                success:function(result){
                                    window.location.reload();
                                },
                                error: function (error) {}
                            }, 'loading');
                        }
                    }
                }
            });
        });
    </script>
@endsection
