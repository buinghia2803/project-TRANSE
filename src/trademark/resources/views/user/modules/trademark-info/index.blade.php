@extends('user.layouts.app')

@section('main-content')
    <div id="contents" class="normal">

        <h2 id="applicant">{{ __('labels.user_change_address.title') }}</h2>
        <p>
            {{ __('labels.user_change_address.subtitle_1') }}<br>
            {{ __('labels.user_change_address.subtitle_2') }}
        </p>

        <h3>{{ __('labels.user_change_address.trademark_table.title') }}</h3>
        <div class="js-scrollable eol scroll-hint" style="position: relative; overflow: auto;">
            <table class="normal_b">
                <tbody>
                    <tr>
                        <th style="width:115px;min-width:115px;">
                            {{ __('labels.user_change_address.trademark_table.trademark_number') }}
                            <a href="{{ route('user.application-list.change-address', [
                                'orderTrademarkField' => 'soft_trademark_number',
                                'orderTrademarkType' => SORT_TYPE_DESC,
                            ]) }}">▼</a>
                                <a href="{{ route('user.application-list.change-address', [
                                'orderTrademarkField' => 'soft_trademark_number',
                                'orderTrademarkType' => SORT_TYPE_ASC,
                            ]) }}">▲</a>
                        </th>
                        <th style="width:165px;min-width:165px;">{{ __('labels.user_change_address.trademark_table.reference_number') }}</th>
                        <th style="width:200px;min-width:200px;">{{ __('labels.user_change_address.trademark_table.name_trademark') }}</th>
                        <th style="width:170px;min-width:170px;">{{ __('labels.user_change_address.trademark_table.trademark_info_name') }}</th>
                        <th style="width:115px;min-width:115px;">
                            {{ __('labels.user_change_address.trademark_table.application_number') }}
                            <a href="{{ route('user.application-list.change-address', [
                                'orderTrademarkField' => 'application_number',
                                'orderTrademarkType' => SORT_TYPE_DESC,
                            ]) }}">▼</a>
                            <a href="{{ route('user.application-list.change-address', [
                                'orderTrademarkField' => 'application_number',
                                'orderTrademarkType' => SORT_TYPE_ASC,
                            ]) }}">▲</a>
                        </th>
                        <th style="min-width:24em;">{{ __('labels.user_change_address.trademark_table.content') }}</th>
                        <th style="width:130px;min-width:130px;">
                            {{ __('labels.user_change_address.trademark_table.notice_detail_updated_at') }}
                            <a href="{{ route('user.application-list.change-address', [
                                'orderTrademarkField' => 'soft_created_at',
                                'orderTrademarkType' => SORT_TYPE_DESC,
                            ]) }}">▼</a>
                                <a href="{{ route('user.application-list.change-address', [
                                'orderTrademarkField' => 'soft_created_at',
                                'orderTrademarkType' => SORT_TYPE_ASC,
                            ]) }}">▲</a>
                        </th>
                        <th style="width:110px;min-width:110px;">{{ __('labels.user_change_address.trademark_table.change') }}</th>
                    </tr>
                    @forelse($trademarks as $item)
                        <tr>
                            <td>
                                <a href="{{ route('user.application-detail.index', [
                                    'id' => $item->id,
                                    'from' => FROM_U000_TOP,
                                ]) }}">
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
                            <td>{{ $item->application_number ?? '' }}</td>
                            <td>
                                @if(!empty($item->notice_detail->redirect_page))
                                    <a href="{{ $item->notice_detail->redirect_page ?? '' }}">
                                        {{ $item->notice_detail->content ?? '' }}
                                    </a>
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
                                <a href="{{ route('user.application-list.change-address.applicant', [
                                    'id' => $item->id
                                ]) }}" class="btn_a">{{ __('labels.change') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100%" class="center">{{ __('messages.general.Common_E032') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $trademarks->appends(request()->all())->links() }}
        </div>

        <h3 id="registered">{{ __('labels.user_change_address.register_trademark_table.title') }}</h3>
        <div class="js-scrollable eol scroll-hint" style="position: relative; overflow: auto;">
            <table class="normal_b">
                <tbody>
                    <tr>
                        <th style="width:115px;min-width:115px;">
                            {{ __('labels.user_change_address.register_trademark_table.trademark_number') }}
                            <a href="{{ route('user.application-list.change-address', [
                                'orderRegisField' => 'trademark_number',
                                'orderRegisType' => SORT_TYPE_DESC,
                            ]) }}">▼</a>
                                <a href="{{ route('user.application-list.change-address', [
                                'orderRegisField' => 'trademark_number',
                                'orderRegisType' => SORT_TYPE_ASC,
                            ]) }}">▲</a>
                        </th>
                        <th style="width:165px;min-width:165px;">{{ __('labels.user_change_address.register_trademark_table.reference_number') }}</th>
                        <th style="width:200px;min-width:200px;">{{ __('labels.user_change_address.register_trademark_table.name_trademark') }}</th>
                        <th style="width:170px;min-width:170px;">{{ __('labels.user_change_address.register_trademark_table.trademark_info_name') }}</th>
                        <th style="width:115px;min-width:115px;">
                            {{ __('labels.user_change_address.register_trademark_table.register_number') }}
                            <a href="{{ route('user.application-list.change-address', [
                                'orderRegisField' => 'register_number',
                                'orderRegisType' => SORT_TYPE_DESC,
                            ]) }}">▼</a>
                            <a href="{{ route('user.application-list.change-address', [
                                'orderRegisField' => 'register_number',
                                'orderRegisType' => SORT_TYPE_ASC,
                            ]) }}">▲</a>
                        </th>
                        <th style="min-width:24em;">{{ __('labels.user_change_address.register_trademark_table.content') }}</th>
                        <th style="width:130px;min-width:130px;">
                            {{ __('labels.user_change_address.register_trademark_table.created_at') }}
                            <a href="{{ route('user.application-list.change-address', [
                                'orderRegisField' => 'created_at',
                                'orderRegisType' => SORT_TYPE_DESC,
                            ]) }}">▼</a>
                            <a href="{{ route('user.application-list.change-address', [
                                'orderRegisField' => 'created_at',
                                'orderRegisType' => SORT_TYPE_ASC,
                            ]) }}">▲</a>
                        </th>
                        <th style="width:110px;min-width:110px;">{{ __('labels.user_change_address.register_trademark_table.change') }}</th>
                    </tr>
                    @forelse($registerTrademarks as $item)
                        <tr>
                            <td>
                                <a href="{{ route('user.application-detail.index', [
                                    'id' => $item->trademark->id,
                                    'from' => FROM_U000_TOP,
                                ]) }}">
                                    {{ $item->trademark->trademark_number ?? '' }}
                                </a>
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
                            <td>{{ __('labels.user_change_address.register_trademark_table.complete') }}</td>
                            <td>{{ $item->created_at->format('Y/m/d') }}</td>
                            <td class="center">
                                <a href="{{ route('user.application-list.change-address.registered', [
                                    'id' => $item->trademark->id,
                                ]) }}" class="btn_a">{{ __('labels.change') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="100%" class="center">{{ __('messages.general.Common_E032') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $registerTrademarkPaginate->appends(request()->all())->links() }}
        </div>

        <p class="fs12 eol">
            <a href="{{ $backUrl ?? route('user.top') }}" class="btn_a">{{ __('labels.back') }}</a>
        </p>
    </div>
@endsection
@section('footerSection')
<script>
    // scroll click li in menu
    var url_string = window.location.href;
    var url = new URL(url_string);
    var c = url.searchParams.get("scroll");

    if (c != null) {
        document.querySelector('#registered').scrollIntoView({
            behavior: 'smooth'
        });
    }
</script>
@endsection
