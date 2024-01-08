@extends('admin.layouts.app')

@section('main-content')
    <div id="contents">
        <div class="wide clearfix">
            @include('compoments.messages')

            <form action="{{ route('admin.free-history.store', $trademark->id) }}" id="form" method="post"
                enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="free_history_id" value="{{ $freeHistoryData['free_history_id'] ?? null }}">
                <input type="hidden" name="maching_result_id" value="{{ $freeHistoryData['maching_result_id'] ?? null }}">

                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable,
                ])

                <h3>{{ __('labels.create_free_history.title') }}</h3>

                <ul class="mb10">
                    @foreach ($types as $key => $type)
                        <li>
                            <label>
                                <input type="radio" name="type" value="{{ $key ?? '' }}"
                                    {{ $freeHistoryData['type'] == $key ? 'checked' : '' }}> {{ $type ?? '' }}
                            </label>
                        </li>
                    @endforeach
                </ul>
                <p class="eol">
                    {{ __('labels.create_free_history.user_response_deadline') }}：
                    <input type="date" name="user_response_deadline"
                        value="{{ $freeHistoryData['user_response_deadline'] ?? '' }}" min="{{ date('Y-m-d') }}">
                </p>

                <table class="normal_b column1 mb10">
                    <tbody>
                        <tr>
                            <th style="width:6em;">{{ __('labels.create_free_history.XML_delivery_date') }}</th>
                            <th style="width:6em;">{{ __('labels.create_free_history.property') }}</th>
                            <th style="width:10em;">{{ __('labels.create_free_history.status_name') }}</th>
                            <th style="width:6em;">{{ __('labels.create_free_history.patent_response_deadline') }}</th>
                            <th style="width:8em;">{{ __('labels.create_free_history.admin_create_name') }}</th>
                            <th style="width:8em;">{{ __('labels.create_free_history.attachment') }}</th>
                            <th style="width:6em;">{{ __('labels.create_free_history.amount_type') }}</th>
                            <th>{{ __('labels.create_free_history.internal_remark') }}</th>
                        </tr>
                        <tr>
                            <td>
                                {{ CommonHelper::formatTime($freeHistoryData['XML_delivery_date'] ?? '', 'Y/m/d') }}
                                <input type="hidden" name="XML_delivery_date"
                                    value="{{ CommonHelper::formatTime($freeHistoryData['XML_delivery_date'] ?? '', 'Y-m-d') }}">
                            </td>
                            <td>
                                <select name="property"
                                    {{ !empty($freeHistoryData['maching_result_id']) ? 'disabled' : '' }}>
                                    <option value="">-----{{ __('labels.option_default') }}-----</option>
                                    @foreach ($properties as $key => $property)
                                        <option value="{{ $key ?? '' }}"
                                            {{ $freeHistoryData['property'] == $key ? 'selected' : '' }}>
                                            {{ $property ?? '' }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <input type="text" class="em10" name="status_name"
                                    value="{{ $freeHistoryData['status_name'] ?? '' }}"
                                    {{ !empty($freeHistoryData['maching_result_id']) ? 'disabled' : '' }}>
                            </td>
                            <td>
                                <input type="date" class="em10" name="patent_response_deadline"
                                    value="{{ $freeHistoryData['patent_response_deadline'] ?? '' }}"
                                    min="{{ date('Y-m-d') }}">
                            </td>
                            <td>{{ $freeHistoryData['create_name'] ?? '' }}</td>
                            <td>
                                <input type="file" name="attachment[]" multiple accept="application/pdf">
                                <div class="attachment-group mt-2">
                                    @foreach ($freeHistoryData['attachment'] as $item)
                                        <p class="attachment-item mb-1 d-flex">
                                            <span class="line line-1">{{ $item['filename'] ?? '' }}</span>
                                            <span class="remove-file red cursor-pointer ms-1">Ⓧ</span>
                                            <input type="hidden" name="attachment_input[]"
                                                value="{{ $item['filepath'] ?? '' }}">
                                        </p>
                                    @endforeach
                                </div>
                            </td>
                            <td nowrap="">
                                <div class="input-group show-error">
                                    <input type="radio" name="amount_type"
                                        value="{{ \App\Models\FreeHistory::AMOUNT_TYPE_NO_FREE }}"
                                        {{ \App\Models\FreeHistory::AMOUNT_TYPE_NO_FREE == $freeHistoryData['amount_type'] ? 'checked' : '' }}>
                                    {{ __('labels.create_free_history.amount_type_no_free') }}<br>
                                    <input type="radio" name="amount_type"
                                        value="{{ \App\Models\FreeHistory::AMOUNT_TYPE_CUSTOM }}"
                                        {{ \App\Models\FreeHistory::AMOUNT_TYPE_CUSTOM == $freeHistoryData['amount_type'] ? 'checked' : '' }}>
                                    {{ __('labels.create_free_history.amount_type_custom') }} <input type="number"
                                        class="em10" name="amount" value="{{ $freeHistoryData['amount'] ?? '' }}"> 円<br>
                                    <input type="radio" name="amount_type"
                                        value="{{ \App\Models\FreeHistory::AMOUNT_TYPE_FREE }}"
                                        {{ \App\Models\FreeHistory::AMOUNT_TYPE_FREE == $freeHistoryData['amount_type'] ? 'checked' : '' }}>
                                    {{ __('labels.create_free_history.amount_type_free') }}
                                </div>
                            </td>
                            <td>
                                <textarea class="wide" name="internal_remark">{{ $freeHistoryData['internal_remark'] ?? '' }}</textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="eol">
                    <p class="mb-0">{{ __('labels.create_free_history.comment') }}：</p>
                    <textarea class="large" name="comment">{{ $freeHistoryData['comment'] ?? '' }}</textarea>
                </div>

                <ul class="footerBtn clearfix">
                    <li>
                        <input type="button" value="{{ __('labels.back') }}" class="btn_a"
                            onclick="window.location = '{{ route('admin.application-detail.index', $trademark->id) }}'">
                    </li>
                    <li>
                        <input type="submit" {{ $isSubmit == false || $isMaxData == true ? 'disabled' : '' }}
                            name="{{ CREATE }}" value="{{ __('labels.save') }}" class="btn_c">
                    </li>
                    <li>
                        <input type="submit" {{ $isSubmit == false || $isMaxData == true ? 'disabled' : '' }}
                            name="{{ CONFIRM }}" value="{{ __('labels.btn_confirm') }}" class="btn_b">
                    </li>
                </ul>
            </form>
        </div>
    </div>
@endsection

@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script>
        const MAX_FILESIZE = 3 * 1024 * 1024;
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

        const errorMessageIsValidProperty = '{{ __('messages.general.Freerireki_E005') }}';
        const errorMessageRequiredUserResponseDeadline = '{{ __('messages.general.Freerireki_E004') }}';
        const errorMessageMinCurrentDate = '{{ __('messages.general.Common_S042') }}';
        const errorMessageMaxUserResponseDeadline = '{{ __('messages.general.Common_E057') }}';

        const errorMessageRequiredFile = '{{ __('messages.general.Freerireki_E003') }}';
        const errorMessageMax20File = '{{ __('messages.general.Import_A000_E001') }}';
        const errorMessageMaxFilesize = '{{ __('messages.general.Common_E028') }}';
        const errorMessageFileExtension = '{{ __('messages.general.Common_E037') }}';

        const USER_RESPONSE_DEADLINE = '{{ $freeHistoryData['user_response_deadline'] ?? '' }}';
    </script>
    <script src="{{ asset('admin_assets/pages/free_histories/create.js') }}"></script>
    @if ($isSubmit == false)
        <script>
            disabledScreen();
            $('#form').find('.remove-file').css('pointer-events', 'none');
        </script>
    @endif
    @include('compoments.readonly', [
        'only' => [ROLE_SUPERVISOR, ROLE_OFFICE_MANAGER],
        'script' => "<script>$('#form').find('.remove-file').css('pointer-events', 'none');</script>",
    ])
@endsection
