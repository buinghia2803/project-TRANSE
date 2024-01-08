@extends('admin.layouts.app')

@section('main-content')
    <div id="contents">
        <div class="normal clearfix">
            <form>
                <h3>{{ __('labels.application_list.title') }}</h3>

                <table class="normal_b column1">
                    <tbody>
                        <tr>
                            <th>
                                {{ __('labels.application_list.trademark_number') }}
                                <a href="{{ route('admin.search.application-list', [
                                    'filter' => request()->filter ?? 0,
                                    'orderField' => 'trademark_number',
                                    'orderType' => SORT_TYPE_DESC,
                                ]) }}">▼</a>
                                <a href="{{ route('admin.search.application-list', [
                                    'filter' => request()->filter ?? 0,
                                    'orderField' => 'trademark_number',
                                    'orderType' => SORT_TYPE_ASC,
                                ]) }}">▲</a>
                            </th>
                            <th>{{ __('labels.application_list.user_info_name') }}</th>
                            <th>{!! __('labels.application_list.trademark_info_name') !!}</th>
                            <th>{{ __('labels.application_list.name_trademark') }}</th>
                            <th>{{ __('labels.application_list.application_number') }}</th>
                            <th>{{ __('labels.application_list.application_date') }}</th>
                            <th>
                                {{ __('labels.application_list.register_number') }}
                                <a href="{{ route('admin.search.application-list', [
                                    'filter' => request()->filter ?? 0,
                                    'orderField' => 'register_number',
                                    'orderType' => SORT_TYPE_DESC,
                                ]) }}">▼</a>
                                <a href="{{ route('admin.search.application-list', [
                                    'filter' => request()->filter ?? 0,
                                    'orderField' => 'register_number',
                                    'orderType' => SORT_TYPE_ASC,
                                ]) }}">▲</a>
                            </th>
                            <th>{{ __('labels.application_list.date_register') }}</th>
                            <th>{{ __('labels.application_list.notice_detail_content') }}</th>
                            <th>
                                {{ __('labels.application_list.notices_updated_at') }}
                                <a href="{{ route('admin.search.application-list', [
                                    'filter' => request()->filter ?? 0,
                                    'orderField' => 'notice_updated_at',
                                    'orderType' => SORT_TYPE_DESC,
                                ]) }}">▼</a>
                                <a href="{{ route('admin.search.application-list', [
                                    'filter' => request()->filter ?? 0,
                                    'orderField' => 'notice_updated_at',
                                    'orderType' => SORT_TYPE_ASC,
                                ]) }}">▲</a>
                            </th>
                        </tr>
                        @forelse($appTrademarks as $item)
                            @php
                                $class = '';
                                if ($item->is_app_cancel == true) {
                                    $class = 'bg_pink';
                                } elseif($item->is_coming_deadline == true) {
                                    $class = 'bg_green';
                                }
                            @endphp
                            <tr>
                                <td class="{{ $class ?? '' }}">
                                    <a href="{{ route('admin.application-detail.index', $item->trademark->id ?? 0) }}">
                                        {{ $item->trademark->trademark_number ?? '' }}
                                    </a>
                                </td>
                                <td class="{{ $class ?? '' }}">
                                    {{ $item->user_info_name ?? '' }}
                                </td>
                                <td class="{{ $class ?? '' }}">
                                    {{ $item->trademark_info_name ?? '' }}
                                </td>
                                <td class="{{ $class ?? '' }}">
                                    @if(!empty($item->trademark))
                                        @if($item->trademark->isTrademarkLetter())
                                            {{ $item->trademark->name_trademark ?? '' }}
                                        @else
                                            <img src="{{ $item->trademark->getImageTradeMark() ?? '' }}" height="80" alt="">
                                        @endif
                                    @endif
                                </td>
                                <td class="{{ $class ?? '' }}">
                                    {{ $item->trademark->application_number ?? '' }}
                                </td>
                                <td class="{{ $class ?? '' }}">
                                    {{ CommonHelper::formatTime($item->trademark->application_date ?? '', 'Y/m/d') }}
                                </td>
                                <td class="{{ $class ?? '' }}">
                                    {{ $item->register_trademark->register_number ?? '' }}
                                </td>
                                <td class="{{ $class ?? '' }}">
                                    {{ CommonHelper::formatTime($item->register_trademark->date_register ?? '', 'Y/m/d') }}
                                </td>
                                <td class="{{ $class ?? '' }}">
                                    @if(!empty($item->notice_detail->redirect_page) && $item->notice_detail->isAdminOwner())
                                        <a href="{{ $item->notice_detail->redirect_page ?? '' }}">{{ $item->notice_detail->content ?? '' }}</a>
                                    @else
                                        {{ $item->notice_detail->content ?? '' }}
                                    @endif
                                </td>
                                <td class="{{ $class ?? '' }}">
                                    @if(!empty($item->notice_detail))
                                        {{ $item->notice_detail->updated_at->format('Y/m/d') }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="center">{{ __('labels.no_data') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </form>
        </div>
    </div>
@endsection
