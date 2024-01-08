@extends('admin.layouts.app')

@section('main-content')
    <div id="contents">
        <div class="wide clearfix">
            @include('compoments.messages')

            <form action="{{ route('admin.free-history.update', $freeHistory->id) }}" id="form" method="post"
                  enctype="multipart/form-data">
                @csrf

                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable
                ])

                <h3>{{ __('labels.edit_free_history.title') }}</h3>
                <ul class="mb10">
                    @foreach($types as $key => $type)
                        <li>
                            <label>
                                <input type="radio" name="type" value="{{ $key ?? '' }}"
                                    {{ $freeHistoryData['type'] == $key ? 'checked' : '' }}
                                > {{ $type ?? '' }}
                            </label>
                        </li>
                    @endforeach
                </ul>

                <table class="normal_b column1 mb10">
                    <tbody>
                        <tr>
                            <th style="width:6em;">{{ __('labels.edit_free_history.XML_delivery_date') }}</th>
                            <th style="width:6em;">{{ __('labels.edit_free_history.property') }}</th>
                            <th style="width:14em;">{{ __('labels.edit_free_history.status_name') }}</th>
                            <th style="width:6em;">{{ __('labels.edit_free_history.patent_response_deadline') }}</th>
                            <th style="width:8em;">{{ __('labels.edit_free_history.create_name') }}</th>
                            <th style="width:8em;">{{ __('labels.edit_free_history.attachment') }}</th>
                            <th style="width:6em;">{{ __('labels.edit_free_history.is_check_amount') }}</th>
                            <th>{{ __('labels.edit_free_history.internal_remark') }}</th>
                        </tr>
                        <tr>
                            <td>{{ CommonHelper::formatTime($freeHistoryData['XML_delivery_date'] ?? '', 'Y/m/d') }}</td>
                            <td>{{ $properties[$freeHistoryData['property'] ?? null] ?? null }}</td>
                            <td>{{ $freeHistoryData['status_name'] ?? '' }}</td>
                            <td>{{ CommonHelper::formatTime($freeHistoryData['patent_response_deadline'] ?? '', 'Y/m/d') }}</td>
                            <td>{{ $freeHistoryData['create_name'] ?? '' }}</td>
                            <td>
                                <input type="file" name="attachment[]" multiple accept="application/pdf">
                                <div class="attachment-group mt-2">
                                    @foreach($freeHistoryData['attachment'] as $item)
                                        <p class="attachment-item mb-1 d-flex">
                                            <span class="line line-1">{{ $item['filename'] ?? '' }}</span>
                                            <span class="remove-file red cursor-pointer ms-1">Ⓧ</span>
                                            <input type="hidden" name="attachment_input[]" value="{{ $item['filepath'] ?? '' }}">
                                        </p>
                                    @endforeach
                                </div>
                            </td>
                            <td nowrap="">
                                <div class="input-group show-error">
                                    <input type="checkbox" name="is_check_amount" value="{{ true }}"
                                        {{ $freeHistoryData['is_check_amount'] == true ? 'checked' : '' }}
                                    > {{ __('labels.edit_free_history.is_amount') }}（{{ $freeHistoryData['amount_display'] ?? '' }}円）
                                </div>
                            </td>
                            <td>
                                <textarea class="wide" name="internal_remark">{{ $freeHistoryData['internal_remark'] ?? '' }}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <p class="eol">
                    {{ __('labels.edit_free_history.user_response_deadline') }}：
                    <input type="date" name="user_response_deadline" value="{{ $freeHistoryData['user_response_deadline'] ?? '' }}"
                       min="{{ date('Y-m-d') }}"
                       max="{{ $freeHistoryData['patent_response_deadline'] ?? '' }}"
                    >
                </p>

                <p class="eol">
                    {{ __('labels.edit_free_history.comment') }}：<br>
                    <textarea class="large" name="comment">{{ $freeHistoryData['comment'] ?? '' }}</textarea>
                </p>

                <ul class="footerBtn clearfix">
                    <li>
                        <input type="button" value="{{ __('labels.back') }}" class="btn_a"
                            onclick="window.location = '{{ route('admin.application-detail.index', $trademark->id) }}'"
                        >
                    </li>
                    @if($freeHistory->is_confirm == false)
                        <li>
                            <input type="submit" name="{{ CREATE }}" value="{{ __('labels.save') }}" class="btn_c">
                        </li>
                        <li>
                            <input type="submit" name="{{ CONFIRM }}" value="{{ __('labels.edit_free_history.submit_confirm') }}" class="btn_b">
                        </li>
                    @endif
                </ul>
            </form>
        </div>
    </div>
@endsection

@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        const MAX_FILESIZE = 3*1024*1024;
        const MAX_FILE_UPLOAD = 20;
        const ROUTE_UPLOAD_FILE = '{{ route('admin.ajax.upload-files') }}';

        const TYPE_1 = '{{ \App\Models\FreeHistory::TYPE_1 }}';
        const TYPE_2 = '{{ \App\Models\FreeHistory::TYPE_2 }}';
        const TYPE_3 = '{{ \App\Models\FreeHistory::TYPE_3 }}';
        const TYPE_4 = '{{ \App\Models\FreeHistory::TYPE_4 }}';

        const AMOUNT_TYPE_1 = '{{ \App\Models\FreeHistory::AMOUNT_TYPE_NO_FREE }}';
        const AMOUNT_TYPE_2 = '{{ \App\Models\FreeHistory::AMOUNT_TYPE_CUSTOM }}';
        const AMOUNT_TYPE_3 = '{{ \App\Models\FreeHistory::AMOUNT_TYPE_FREE }}';

        const errorMessageRequired = '{{ __('messages.general.Common_E001') }}';
        const errorMessageMaxLength1000 = '{{ __('messages.general.Common_E055') }}';
        const errorMessageMaxLength255 = '{{ __('messages.general.Common_E029') }}';
        const errorMessageMaxAmount = '{{ __('messages.general.Freerireki_E001') }}';

        const errorMessageRequiredFile = '{{ __('messages.general.Freerireki_E003') }}';
        const errorMessageMax20File = '{{ __('messages.general.Import_A000_E001') }}';
        const errorMessageMaxFilesize = '{{ __('messages.general.Common_E028') }}';
        const errorMessageFileExtension = '{{ __('messages.general.Common_E037') }}';

        const errorMessageRequiredIsCheckAmount = '{{ __('messages.general.Common_E025') }}';
        const errorMessageIsValidResponseDeadline = '{{ __('messages.general.Common_E039') }}';
    </script>
    <script src="{{ asset('admin_assets/pages/free_histories/edit.js') }}"></script>
    @if ($freeHistory->is_confirm == IS_CONFIRM_HISTORY || isset(request()->type) && request()->type == VIEW)
        <script>
            disabledScreen();
            $('#form').find('.remove-file').css('pointer-events', 'none');
        </script>
    @endif
    @include('compoments.readonly', [
        'only' => [ROLE_SUPERVISOR],
        'script' => "<script>$('#form').find('.remove-file').css('pointer-events', 'none');</script>",
    ])
@endsection
