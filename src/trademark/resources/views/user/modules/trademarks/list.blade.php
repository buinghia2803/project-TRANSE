@extends('user.layouts.app')

@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">

        <h3>{{ __('labels.user_application_list.title') }}</h3>

        <h3>{{ __('labels.user_application_list.trademark_table.title') }}</h3>
        <div class="js-scrollable eol">
            <table class="normal_b">
                <tr>
                    <th style="width:95px;min-width:95px;">
                        {{ __('labels.user_application_list.trademark_table.response_deadline') }}
                        <a href="{{ route('user.application-list', [
                            'orderTrademarkField' => 'soft_response_deadline',
                            'orderTrademarkType' => SORT_TYPE_DESC,
                        ]) }}">▼</a>
                        <a href="{{ route('user.application-list', [
                            'orderTrademarkField' => 'soft_response_deadline',
                            'orderTrademarkType' => SORT_TYPE_ASC,
                        ]) }}">▲</a>
                    </th>
                    <th style="width:115px;min-width:115px;">
                        {{ __('labels.user_application_list.trademark_table.trademark_number') }}
                        <a href="{{ route('user.application-list', [
                            'orderTrademarkField' => 'soft_trademark_number',
                            'orderTrademarkType' => SORT_TYPE_DESC,
                        ]) }}">▼</a>
                        <a href="{{ route('user.application-list', [
                            'orderTrademarkField' => 'soft_trademark_number',
                            'orderTrademarkType' => SORT_TYPE_ASC,
                        ]) }}">▲</a>
                    </th>
                    <th style="width:165px;min-width:165px;">{{ __('labels.user_application_list.trademark_table.reference_number') }}</th>
                    <th style="width:200px;min-width:200px;">{{ __('labels.user_application_list.trademark_table.name_trademark') }}</th>
                    <th style="width:170px;min-width:170px;">{{ __('labels.user_application_list.trademark_table.trademark_info_name') }}</th>
                    <th style="width:115px;min-width:115px;">
                        {{ __('labels.user_application_list.trademark_table.register_number') }}
                        <a href="{{ route('user.application-list', [
                            'orderTrademarkField' => 'soft_register_number',
                            'orderTrademarkType' => SORT_TYPE_DESC,
                        ]) }}">▼</a>
                        <a href="{{ route('user.application-list', [
                            'orderTrademarkField' => 'soft_register_number',
                            'orderTrademarkType' => SORT_TYPE_ASC,
                        ]) }}">▲</a>
                    </th>
                    <th style="min-width:24em;">{{ __('labels.user_application_list.trademark_table.content') }}</th>
                    <th style="width:130px;min-width:130px;">
                        {{ __('labels.user_application_list.trademark_table.notice_detail_updated_at') }}
                        <a href="{{ route('user.application-list', [
                            'orderTrademarkField' => 'soft_created_at',
                            'orderTrademarkType' => SORT_TYPE_DESC,
                        ]) }}">▼</a>
                        <a href="{{ route('user.application-list', [
                            'orderTrademarkField' => 'soft_created_at',
                            'orderTrademarkType' => SORT_TYPE_ASC,
                        ]) }}">▲</a>
                    </th>
                </tr>
                @forelse($trademarks as $item)
                    <tr>
                        <td>
                            @if(!empty($item->notice_detail))
                                {{ $item->notice_detail->response_deadline_format ?? '' }}
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('user.application-detail.index', [
                                'id' => $item->id,
                                'from' => FROM_U000_TOP,
                            ]) }}">{{ $item->trademark_number ?? '' }}</a>
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="center">{{ __('messages.general.Common_E032') }}</td>
                    </tr>
                @endforelse
            </table>
        </div>

        <h3>{{ __('labels.user_application_list.register_trademark_table.title') }}</h3>
        <div class="js-scrollable eol">
            <table class="normal_b">
                <tr>
                    <th style="width:95px;min-width:95px;">
                        {{ __('labels.user_application_list.register_trademark_table.deadline_update') }}
                        <a href="{{ route('user.application-list', [
                            'orderRegisField' => 'deadline_update',
                            'orderRegisType' => SORT_TYPE_DESC,
                        ]) }}">▼</a>
                        <a href="{{ route('user.application-list', [
                            'orderRegisField' => 'deadline_update',
                            'orderRegisType' => SORT_TYPE_ASC,
                        ]) }}">▲</a>
                    </th>
                    <th style="width:115px;min-width:115px;">
                        {{ __('labels.user_application_list.register_trademark_table.trademark_number') }}
                        <a href="{{ route('user.application-list', [
                            'orderRegisField' => 'trademark_number',
                            'orderRegisType' => SORT_TYPE_DESC,
                        ]) }}">▼</a>
                        <a href="{{ route('user.application-list', [
                            'orderRegisField' => 'trademark_number',
                            'orderRegisType' => SORT_TYPE_ASC,
                        ]) }}">▲</a>
                    </th>
                    <th style="width:165px;min-width:165px;">{{ __('labels.user_application_list.register_trademark_table.reference_number') }}</th>
                    <th style="width:200px;min-width:200px;">{{ __('labels.user_application_list.register_trademark_table.name_trademark') }}</th>
                    <th style="width:170px;min-width:170px;">{{ __('labels.user_application_list.register_trademark_table.trademark_info_name') }}</th>
                    <th style="width:115px;min-width:115px;">
                        {{ __('labels.user_application_list.register_trademark_table.register_number') }}
                        <a href="{{ route('user.application-list', [
                            'orderRegisField' => 'register_number',
                            'orderRegisType' => SORT_TYPE_DESC,
                        ]) }}">▼</a>
                        <a href="{{ route('user.application-list', [
                            'orderRegisField' => 'register_number',
                            'orderRegisType' => SORT_TYPE_ASC,
                        ]) }}">▲</a>
                    </th>
                    <th style="min-width:24em;">{{ __('labels.user_application_list.register_trademark_table.content') }}</th>
                    <th style="width:130px;min-width:130px;">
                        {{ __('labels.user_application_list.register_trademark_table.created_at') }}
                        <a href="{{ route('user.application-list', [
                            'orderRegisField' => 'created_at',
                            'orderRegisType' => SORT_TYPE_DESC,
                        ]) }}">▼</a>
                        <a href="{{ route('user.application-list', [
                            'orderRegisField' => 'created_at',
                            'orderRegisType' => SORT_TYPE_ASC,
                        ]) }}">▲</a>
                    </th>
                </tr>
                @forelse($registerTrademarks as $item)
                    <tr>
                        <td>{{ $item->deadline_update_before_1_day ?? '' }}</td>
                        <td>
                            <a href="{{ route('user.application-detail.index', [
                                'id' => $item->trademark->id,
                                'from' => FROM_U000_TOP,
                            ]) }}">{{ $item->trademark->trademark_number ?? '' }}</a>
                        </td>
                        <td>{{ $item->trademark->reference_number ?? '' }}</td>
                        <td>
                            @if($item->trademark->isTrademarkLetter())
                                {{ $item->trademark->name_trademark ?? '' }}
                            @else
                                <img src="{{ $item->trademark->getImageTradeMark() ?? '' }}" height="80" alt="">
                            @endif
                        </td>
                        <td>{{ $item->trademark_info_name ?? '' }}</td>
                        <td>{{ $item->register_number ?? '' }}</td>
                        <td>{{ __('labels.user_application_list.register_trademark_table.complete') }}</td>
                        <td>{{ $item->created_at->format('Y/m/d') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="center">{{ __('messages.general.Common_E032') }}</td>
                    </tr>
                @endforelse
            </table>
        </div>

        <p class="fs12 eol">
            <a href="{{ $backUrl ?? route('user.top') }}" class="btn_a">{{ __('labels.back') }}</a>
        </p>
    </div><!-- /contents -->
@endsection
