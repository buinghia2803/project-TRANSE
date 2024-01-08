@extends('admin.layouts.app')

@section('main-content')
    <div id="contents">
        <div class="wide clearfix">
            @include('compoments.messages')

            <form action="{{ route('admin.free-history.post-re-confirm', $freeHistory->id) }}" id="form" method="post">
                @csrf

                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable
                ])
                <h3>{{ __('labels.a000free02.title_page') }}</h3>
                <p class="eol">{{ __('labels.a000free02.title_1') }}：{{ CommonHelper::formatTime($freeHistory->user_response_deadline ?? '', 'Y/m/d') }}</p>
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
                            @foreach($freeHistoryData['attachment'] as $attachment)
                            <a href="{{asset($attachment['filepath'])}}" target="_blank">{{$attachment['filename']}}</a><br>
                            @endforeach
                        </td>
                        <td nowrap="">
                            <div class="input-group show-error">
                                {{ $freeHistoryData['amount'] ? $freeHistoryData['amount'].'円' : '' }}
                            </div>
                        </td>
                        <td>
                            <textarea class="wide" name="comment_free02">{{ $freeHistoryData['comment_free02'] ?? '' }}</textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <br>
                <p class="eol">{{ __('labels.create_support_first_time.comment_AMS') }}：<br>{{$freeHistoryData['comment']}}</p>
                <p class="eol">{{ __('labels.qa.qa03_kaito_list.text_1') }}：<br>
                    {{$freeHistoryData['content_answer']}}</p>

                <ul class="footerBtn clearfix">
                    <li>
                        <input type="button" value="{{ __('labels.back') }}" class="btn_a"
                               onclick="window.location = '{{ route('admin.application-detail.index', $trademark->id) }}'"
                        >
                    </li>
                        <li>
                            <input type="submit" name="{{ CREATE }}" value="{{ __('labels.save') }}" class="btn_c">
                        </li>
                        <li>
                            <input type="submit" name="{{ CONFIRM }}" value="{{ __('labels.a000free02.submit_confirm') }}" class="btn_b">
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
        const errorMessageMaxLength1000 = '{{ __('messages.general.Common_E055') }}';
    </script>
    <script src="{{ asset('admin_assets/pages/free_histories/re-confirm.js') }}"></script>
    @if ($freeHistory->is_completed == IS_COMPLETED_TRUE)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', ['only' => [ ROLE_OFFICE_MANAGER ]])
@endsection
