@extends('user.layouts.app')
@section('main-content')
<!-- contents -->
<div id="contents" class="normal">
    @include('compoments.messages')
    <form id="form" action="{{ route('user.withdraw.application-list-post') }}" method="POST">
        @csrf

        <h3>{{ __('labels.u000list_taikai.h3_1') }}</h3>

        <h3>{{ __('labels.u000list_taikai.h3_2') }}</h3>

        <div class="js-scrollable eol">
            <table class="normal_b">
                <tr>
                    <th>
                        {{ __('labels.u000list_taikai.th_1') }}
                        <a href="{{ route('user.withdraw.application-list', [
                            'orderTrademarkField' => 'soft_response_deadline',
                            'orderTrademarkType' => SORT_TYPE_DESC,
                        ]) }}">▼</a>
                        <a href="{{ route('user.withdraw.application-list', [
                            'orderTrademarkField' => 'soft_response_deadline',
                            'orderTrademarkType' => SORT_TYPE_ASC,
                        ]) }}">▲</a>
                    </th>
                    <th>
                        {{ __('labels.u000list_taikai.th_2') }}
                        <a href="{{ route('user.withdraw.application-list', [
                            'orderTrademarkField' => 'soft_trademark_number',
                            'orderTrademarkType' => SORT_TYPE_DESC,
                        ]) }}">▼</a>
                        <a href="{{ route('user.withdraw.application-list', [
                            'orderTrademarkField' => 'soft_trademark_number',
                            'orderTrademarkType' => SORT_TYPE_ASC,
                        ]) }}">▲</a>
                    </th>
                    <th>{{ __('labels.u000list_taikai.th_3') }}</th>
                    <th>{{ __('labels.u000list_taikai.th_4') }}</th>
                    <th>{{ __('labels.u000list_taikai.th_5') }}</th>
                    <th>
                        {{ __('labels.u000list_taikai.th_6') }}
                        <a href="#">▼</a>
                        <a href="#">▲</a>
                    </th>
                    <th>{{ __('labels.u000list_taikai.th_7') }}</th>
                    <th>
                        {{ __('labels.u000list_taikai.th_8') }}
                        <a href="{{ route('user.withdraw.application-list', [
                            'orderTrademarkField' => 'soft_created_at',
                            'orderTrademarkType' => SORT_TYPE_DESC,
                        ]) }}">▼</a>
                        <a href="{{ route('user.withdraw.application-list', [
                            'orderTrademarkField' => 'soft_created_at',
                            'orderTrademarkType' => SORT_TYPE_ASC,
                        ]) }}">▲</a>
                    </th>
                    <th>{{ __('labels.u000list_taikai.th_9') }}</th>
                </tr>
                @forelse($trademarks as $item)
                    <tr style="background-color: {{ $item->is_status_management ? '#dfdfdf' : '' }} !important; background-color: {{ $item->is_cancel ? '#f1b5d0' : '' }} !important">
                        <td>
                            @if(!empty($item->notice_detail))
                                {{ $item->notice_detail->response_deadline_format ?? '' }}
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('user.application-detail.index', ['id' => $item->id]) }}">
                                {{ $item->trademark_number ?? '' }}
                            </a>
                        </td>
                        <td>{{ $item->reference_number ?? '' }}</td>
                        <td>
                            @if($item->isTrademarkLetter())
                                {{ $item->name_trademark ?? '' }}
                            @else
                                <img src="{{ $item->getImageTradeMark() ?? '' }}" height="80" alt="">
                            @endif
                        </td>
                        <td>{{ $item->trademark_info_name ?? '' }}</td>
                        <td></td>
                        <td>
                            @if(!empty($item->notice_detail->redirect_page))
                                <a href="{{ $item->notice_detail->redirect_page ?? '' }}">{{ $item->notice_detail->content ?? '' }}</a>
                            @else
                                {{ $item->notice_detail->content ?? '' }}
                            @endif
                        </td>
                        <td>
                            @if(!empty($item->notice_detail))
                                {{ $item->notice_detail->created_at->format('Y/m/d') }}
                            @endif
                        </td>
                        <td class="center">
                            <input type="checkbox" name="data[{{ $item->id }}][status_management]" class="status_management" value="0" {{ $item->status_management ? '' : 'checked' }}/>
                            <input hidden type="text" name="data[{{ $item->id }}][trademark_id]" value="{{ $item->id }}"/>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="center">{{ __('messages.general.Common_E032') }}</td>
                    </tr>
                @endforelse
            </table>
        </div>
        <!-- /scroll wrap -->

        <h3>{{ __('labels.u000list_taikai.h3_3') }}</h3>

        <div class="js-scrollable eol">
            <table class="normal_b">
                <tr>
                    <th>
                        {{ __('labels.u000list_taikai.th_1') }}
                        <a href="{{ route('user.withdraw.application-list', [
                            'orderRegisField' => 'register_deadline_update',
                            'orderRegisType' => SORT_TYPE_DESC,
                        ]) }}">▼</a>
                        <a href="{{ route('user.withdraw.application-list', [
                            'orderRegisField' => 'register_deadline_update',
                            'orderRegisType' => SORT_TYPE_ASC,
                        ]) }}">▲</a>
                    </th>
                    <th>
                        {{ __('labels.u000list_taikai.th_2') }}
                        <a href="{{ route('user.withdraw.application-list', [
                            'orderRegisField' => 'trademark_number',
                            'orderRegisType' => SORT_TYPE_DESC,
                        ]) }}">▼</a>
                        <a href="{{ route('user.withdraw.application-list', [
                            'orderRegisField' => 'trademark_number',
                            'orderRegisType' => SORT_TYPE_ASC,
                        ]) }}">▲</a>
                    </th>
                    <th>{{ __('labels.u000list_taikai.th_3') }}</th>
                    <th>{{ __('labels.u000list_taikai.th_4') }}</th>
                    <th>{{ __('labels.u000list_taikai.th_5') }}</th>
                    <th>
                        {{ __('labels.u000list_taikai.th_6') }}
                        <a href="{{ route('user.withdraw.application-list', [
                            'orderRegisField' => 'register_number',
                            'orderRegisType' => SORT_TYPE_DESC,
                        ]) }}">▼</a>
                        <a href="{{ route('user.withdraw.application-list', [
                            'orderRegisField' => 'register_number',
                            'orderRegisType' => SORT_TYPE_ASC,
                        ]) }}">▲</a>
                    </th>
                    <th>{{ __('labels.u000list_taikai.th_7') }}</th>
                    <th>
                        {{ __('labels.u000list_taikai.th_8') }}
                        <a href="{{ route('user.withdraw.application-list', [
                            'orderRegisField' => 'register_updated_at',
                            'orderRegisType' => SORT_TYPE_DESC,
                        ]) }}">▼</a>
                        <a href="{{ route('user.withdraw.application-list', [
                            'orderRegisField' => 'register_updated_at',
                            'orderRegisType' => SORT_TYPE_ASC,
                        ]) }}">▲</a>
                    </th>
                    <th>{{ __('labels.u000list_taikai.th_9') }}</th>
                </tr>
                @forelse($registerTrademarks as $item)
                    <tr style="background-color: {{ $item->is_status_management ? '#dfdfdf' : '' }} !important; background-color: {{ $item->is_cancel ? '#f1b5d0' : '' }} !important">
                        <td>{{ $item->deadline_update_format ?? '' }}</td>
                        <td>
                            <a href="{{ route('user.application-detail.index', ['id' => $item->id]) }}">
                                {{ $item->trademark_number ?? '' }}
                            </a>
                        </td>
                        <td>{{ $item->reference_number ?? '' }}</td>
                        <td>
                            @if($item->isTrademarkLetter())
                                {{ $item->name_trademark ?? '' }}
                            @else
                                <img src="{{ $item->getImageTradeMark() ?? '' }}" height="80" alt="">
                            @endif
                        </td>
                        <td>
                            @if(!empty($item->register_trademark))
                                {{ $item->register_trademark->trademark_info_name ?? '' }}
                            @endif
                        </td>
                        <td>
                            @if(!empty($item->register_trademark))
                                {{ $item->register_trademark->register_number ?? '' }}
                            @endif
                        </td>
                        <td>{{ __('labels.u000list_taikai.td') }}</td>
                        <td>
                            @if(!empty($item->register_trademark))
                                {{ $item->register_trademark->updated_at->format('Y/m/d') }}
                            @endif
                        </td>
                        <td class="center">
                            <input type="checkbox" name="data[{{ $item->id }}][status_management]" class="status_management" value="0" {{ $item->status_management ? '' : 'checked' }}/>
                            <input hidden type="text" name="data[{{ $item->id }}][trademark_id]" value="{{ $item->id }}"/>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="center">{{ __('messages.general.Common_E032') }}</td>
                    </tr>
                @endforelse
            </table>

            {{ $registerTrademarks->appends(request()->all())->links() }}
        </div>
        <!-- /scroll wrap -->

        <ul class="footerBtn clearfix">
            <li>
                <input type="button" name="submit" value="{{ __('labels.u000list_taikai.submit') }}" class="btn_d disabled" disabled/>
                <input type="submit" id="submit_form" hidden/>
            </li>
        </ul>

        <p class="fs12 eol">
            <input type="button" value="{{ __('labels.back') }}" class="btn_a" onclick="window.location='{{ $backUrl ?? route('user.top') }}'"/>
        </p>

    </form>
</div>
<!-- /contents -->
@endsection
@section('script')
    <script>
        $('.status_management').change(function () {
            let flug = false
            $('.status_management').each(function (idx, item) {
                const isCheck = $(item).is(':checked');
                if (isCheck) {
                    flug = true
                }
            })

            if (flug) {
                $('input[name=submit]').prop('disabled', false).removeClass('disabled')
            } else {
                $('input[name=submit]').prop('disabled', true).addClass('disabled')
            }
        }).change()

        $('input[name=submit]').click(function () {
            let self = this
            $.confirm({
                title: '',
                content: '本当に管理不要ですか？',
                buttons: {
                    confirm: {
                        text: '承認',
                        btnClass: 'bg_gray',
                        action: function(){
                            $('#submit_form').click()
                        }
                    },
                    cancel: {
                        text: 'キャンセル',
                    }
                }
            });
        })
    </script>
@endsection
