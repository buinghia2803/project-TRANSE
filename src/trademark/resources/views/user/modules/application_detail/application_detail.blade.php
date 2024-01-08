@extends('user.layouts.app')

@section('headerSection')
    <link rel="stylesheet" href="{{ asset('common/css/custom-css.css') }}">
@endsection

@section('main-content')
    @php
        $isStatusManagement = App\Models\Trademark::TRADEMARK_STATUS_MANAGEMENT;
    @endphp
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.application_detail_user.title') }}</h2>
        @include('admin.components.includes.messages')
        <div class="info">
            <h3>{{ __('labels.application_detail_user.header.text_1') }}</h3>
            {{-- Trademark table --}}
            @include('user.components.trademark-table', [
                'table' => $trademarkTable,
            ])

            @if (isset($trademarkNotice->comparisonTrademarkResult) &&
                $trademarkNotice->comparisonTrademarkResult->getResponseDeadline())
                <p class="right notice mb10">{{ __('labels.application_detail_user.header.text_2') }}</p>
                {{-- to do --}}
                <p class="right mb10"><a href="{{ route('user.refusal.extension-period.alert', ['id' => $trademarkId]) }}"
                        class="btn_b">{{ __('labels.application_detail_user.header.text_3') }}</a>
                </p>
            @endif
        </div>
        <!-- /info -->
        <h3>{{ __('labels.application_detail_user.header.text_4') }}</h3>
        {{-- Click record first --}}
        <p class="mb10">
            <input type="button" value="{{ __('labels.application_detail_user.header.btn_first_item') }}"
                id="btn_to_first_table" class="btn_a" />
        </p>

        <div class="js-scrollable mb10">
            <table id="notification-list" class="normal_b" style="border-left: none">
                <thead>
                    <tr>
                        <th style="width: 7%">
                            {{ __('labels.application_detail_user.table.text_1') }}
                            <a
                                href="{{ route('user.application-detail.index', [
                                    'id' => $trademarkId,
                                    'orderType' => SORT_TYPE_DESC,
                                    'from' => Request::get('from'),
                                ]) }}">▼</a>
                            <a
                                href="{{ route('user.application-detail.index', [
                                    'id' => $trademarkId,
                                    'orderType' => SORT_TYPE_ASC,
                                    'from' => Request::get('from'),
                                ]) }}">▲</a>
                        </th>
                        <th style="width:40%;">{{ __('labels.application_detail_user.table.text_2') }}</th>
                        <th style="width: 100px; min-width: 100px;">{{ __('labels.application_detail_user.table.text_3') }}
                        </th>
                        <th>{{ __('labels.application_detail_user.table.text_4') }}</th>
                    </tr>
                </thead>
                @if ($listNotice->count() > 0)
                    <tbody>
                        @foreach ($listNotice as $key => $item)
                            @php
                                $notice = $item->notice;
                            @endphp
                            <tr>
                                <td class="{{ $loop->first ? $item->getClassColorTop() : '' }}">
                                    {{ $notice->getCreatedAt() ?? ' ' }}
                                </td>
                                <td class="{{ $loop->first ? $item->getClassColorTop() : '' }}">
                                    @if (isset($item->redirect_page) && $item->redirect_page)
                                    <a href="{{ $item->redirect_page }}">
                                        {{ $item->content ?? '' }}
                                    </a>
                                    @else
                                    <div>
                                        {{ $item->content ?? '' }}
                                    </div>
                                    @endif
                                </td>
                                <td class="{{ $loop->first ? $item->getClassColorTop() : '' }}">
                                    {{ $item->response_deadline_ams ?? '' }}</td>
                                <td class="{{ $loop->first ? $item->getClassColorTop() : '' }}">
                                    @if((!empty($item->payment_status) ||  $item->payment_status == PAYMENT_STATUS_0) && !empty($item->payment_id))
                                        @if($item->payment_status == PAYMENT_STATUS_0)
                                            <a href="{{route('user.quote', $item->payment_id)}}">{{__('labels.quote.title')}}</a>
                                        @elseif($item->payment_status == PAYMENT_STATUS_1)
                                            <a href="{{route('user.invoice', $item->payment_id)}}">{{__('labels.invoice.title')}}</a>
                                        @elseif($item->payment_status == PAYMENT_STATUS_2)
                                            <a href="{{route('user.invoice', $item->payment_id)}}">{{__('labels.invoice.title')}}</a>
                                            <span>&nbsp;</span>
                                            <a href="{{route('user.receipt', $item->payment_id)}}">{{__('labels.receipt.title_credit_card')}}</a>
                                       @endif
                                    @endif
                                    @foreach ($item->noticeDetailBtns as $itemDetailBtn)
                                        @foreach ($itemDetailBtn->trademarkDocuments as $trademarkDocument)
                                            <a href="{{ isset($trademarkDocument) ? asset($trademarkDocument->url) : '#' }}">
                                                {{ isset($trademarkDocument) ? $trademarkDocument->name : '' }}
                                            </a><br />
                                        @endforeach
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                @else
                    <tbody>
                        <tr>
                            <td colspan="4" style="border:none; text-align: center">
                                <h3> {{ __('messages.general.Common_E032') }}</h3>
                            </td>
                        </tr>
                    </tbody>
                @endif
            </table>
        </div>
        <!-- /scroll wrap -->
        <ul class="footerBtn clearfix">
            <li>
                <input type="button" onclick="history.back()" value="{{ __('labels.qa.admin.btn_back') }}"
                    class="btn_a" />
            </li>
        </ul>
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
    <script type="text/javascript">
        $('#btn_to_first_table').on('click', function() {
            let link = $('body').find('table#notification-list > tbody > tr:first a:first')
            if (link.length) {
                window.location.href = link.attr('href');
            }
        })
    </script>
@endsection
