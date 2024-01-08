<!-- contents inner -->
<div class="wide clearfix">
        <h2>{{ __('labels.trademark_enduser.title') }}</h2>

        <h5 class="membertitle">{{ __('labels.trademark_enduser.sup_title') }}<a href="a001kaiin.html"
                target="_blank">{{ __('labels.trademark_enduser.btn_detail_user') }}</a></h5>
        <ul class="memberinfo">
            <li>{{ $trademark->user->info_member_id ?? '' }}</li>
            <li>{{ $trademark->user->info_name ?? '' }}</li>
            <li><a class="btn_b" href="a000qa_from_ams.html">{{ __('labels.trademark_enduser.QandA') }}</a></li>
        </ul>

        <div class="info mb20">
            <table class="info_table">
                <caption>
                    {{ __('labels.trademark_enduser.sup_title_2') }}
                </caption>
                <tr>
                    <th style="width: 10em;">{{ __('labels.trademark_enduser.trademark_number') }}</th>
                    <td>
                        {{ $trademark->trademark_number ?? '' }} <a href="a000anken_top.html"
                            target="_blank">{{ __('labels.trademark_enduser.trademark_number_2') }}</a>
                    </td>
                    <th style="width: 10em;">{{ __('labels.trademark_enduser.trademark_created_at') }}</th>
                    <td>{{ \CommonHelper::formatTime($trademark->created_at ?? '', 'Y年m月d日') ?? '' }}</td>
                </tr>
                <tr>
                    <th style="width: 10em;">{{ __('labels.trademark_enduser.appTrademark_pack') }}</th>
                    <td colspan="3">
                        @if ($trademark->appTrademark->pack == 1)
                            {{ __('labels.trademark_enduser.packA') }}
                        @elseif ($trademark->appTrademark->pack == 2)
                            {{ __('labels.trademark_enduser.packA') }}
                        @elseif ($trademark->appTrademark->pack == 3)
                            {{ __('labels.trademark_enduser.packA') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th style="width: 10em;">{{ __('labels.trademark_enduser.default_type') }}</th>
                    <td colspan="3">
                        @if ($trademark->registerTrademark != null)
                            @if ($trademark->registerTrademark->info_type_acc == 1)
                                {{ __('labels.trademark_enduser.info_type_acc_1') }}
                            @elseif ($trademark->registerTrademark->info_type_acc == 2)
                                {{ __('labels.trademark_enduser.info_type_acc_2') }}
                            @endif
                        @else
                            @if ($trademark->appTrademark->trademarkInfo[0]->type_acc == 1)
                                {{ __('labels.trademark_enduser.info_type_acc_1') }}
                            @elseif ($trademark->appTrademark->trademarkInfo[0]->type_acc == 2)
                                {{ __('labels.trademark_enduser.info_type_acc_2') }}
                            @endif
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>{{ __('labels.trademark_enduser.trademark_info_name') }}</th>
                    <td colspan="3">
                        @if ($trademark->registerTrademark != null)
                            {{ $trademark->registerTrademark->trademark_info_name }}
                        @else
                            {{ $trademark->appTrademark->trademarkInfo[0]->name }}
                        @endif
                        <input type="submit" value="{{ __('labels.trademark_enduser.value_info_name') }}"
                            class="btn_a" />
                    </td>
                </tr>
                <tr>
                    <th>{{ __('labels.trademark_enduser.type_trademark') }}</th>
                    <td colspan="3">
                        @if ($trademark->type_trademark == 1)
                            {{ __('labels.trademark_enduser.type_trademark_1') }}
                        @elseif ($trademark->type_trademark == 2)
                            {{ __('labels.trademark_enduser.type_trademark_2') }}
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>{{ __('labels.trademark_enduser.name_trademark') }}</th>
                    <td colspan="3">{{ $trademark->name_trademark ?? '' }}</td>
                </tr>
                <tr>
                    <th>{{ __('labels.trademark_enduser.image_trademark') }}</th>
                    <td colspan="3">
                        @if ($trademark->image_trademark)
                            <img src="images/{{ $trademark->image_trademark }}" width="100px" /><br />
                        @endif
                    </td>
                </tr>
                <tr>
                    <th style="width: 10em;">{{ __('labels.trademark_enduser.trademark_number') }}</th>
                    <td style="width: 20em;">{{ $trademark->trademark_number ?? '' }}</td>
                    <th style="width: 10em;">{{ __('labels.trademark_enduser.trademark_created_at') }}</th>
                    <td style="width: 20em;">
                        {{ \CommonHelper::formatTime($trademark->created_at ?? '', 'Y/m/d') }}</td>
                </tr>
                <tr>
                    <th>{{ __('labels.trademark_enduser.register_number') }}</th>
                    <td>{{ $trademark->registerTrademark->register_number ?? '' }}</td>
                    <th>{{ __('labels.trademark_enduser.registerTrademark_created') }}</th>
                    <td>{{ \CommonHelper::formatTime($trademark->registerTrademark->created_at ?? '', 'Y/m/d') }}
                    </td>
                </tr>
                <tr>
                    <th>{{ __('labels.trademark_enduser.deadline_update') }}</th>
                    <td colspan="4">
                        {{ \CommonHelper::formatTime($trademark->registerTrademark->deadline_update ?? '', 'Y/m/d') }}
                    </td>
                </tr>
                <tr>
                    <th>{{ __('labels.trademark_enduser.distinction_name') }}</th>
                    <td colspan="4">
                        <span style="margin-right: 20px">
                            @if ($trademark->registerTrademark && $trademark->registerTrademark->registerTrademarkProds->count())
                                {{ $trademark->registerTrademark->registerTrademarkProds->count() }}
                                @foreach ($trademark->registerTrademark->registerTrademarkProds as $item)
                                    ({{ $item->distinction_name }},)
                                @endforeach
                            @else
                                {{ $trademark->appTrademark->appTrademarkProd->count() }}
                                @foreach ($trademark->appTrademark->appTrademarkProd as $item)
                                    ({{ $item->distinction_name }},)
                                @endforeach
                            @endif
                        </span>
                        <input type="submit" value="{{ __('labels.trademark_enduser.submit1') }}" class="btn_a" />
                        <a href="a003.html">→</a>
                    </td>
                </tr>
            </table>
        </div>
        <!-- 【出願人情報】テーブル -->

        <ul class="btn_left eol">
            <li>
            <a href="{{route('admin.support-first-time.create', $trademark->id)}}" class="btn_a" >{{ __('labels.trademark_enduser.submit2') }}</a>
            </li>
            <li><input type="submit" value="{{ __('labels.trademark_enduser.submit3') }}" class="btn_a" /></li>
            <li><input type="submit" value="{{ __('labels.trademark_enduser.submit4') }}" class="btn_a" /></li>
        </ul>
</div>
<!-- /contents inner -->

<!-- /contents -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @include('admin.components.includes.messages')
            </div>
        </div>
    </div>
</div>
