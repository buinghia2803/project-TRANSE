@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        @include('compoments.messages')
        <form>
            <ul class="btn_left mb20">
                <li class="mt-2">
                    @php
                        $dataSearchAI = [
                            'type_trademark' =>  null,
                            'name_trademark' => null,
                            'image_trademark' =>  null,
                            'route_back' => url()->full(),
                            'keyword' => [],
                        ];

                        // Set session search_ai
                        Session::put(SESSION_SEARCH_AI, $dataSearchAI);
                    @endphp
                    <a href="{{ route('user.search-ai') }}">
                        <button type="button" value="" class="btn_b">
                            {{ __('labels.u000top.ai_search') }}
                        </button>
                    </a>
                </li>
                <li class="mt-2">
                    <a href="{{ route('user.apply-trademark-register') }}">
                        <button type="button" class="btn_b" >
                            {{ __('labels.u000top.application') }}
                        </button>
                    </a>
                </li>
                <li class="mt-2">
                    <a href="{{ route('user.sft.index') }}">
                        <button type="button" value="" class="btn_b">
                            {{ __('labels.u000top.sft') }}
                        </button>
                    </a>
                </li>
            </ul>

            <h3>{{ __('labels.u000top.title') }}</h3>
{{--            <ul class="eol">--}}
                <p class="white-space-pre-line">{!! $setting ? $setting->value : '' !!}</p>
{{--                <li>2017/02/01　3月1日01:00～3:00の間、システムにメンテナンスのため、ご利用になれません。--}}
{{--                <li>2017/02/10　4月1日より、出願料が上がります。お手続きがまだのお客様はお急ぎください。</li>--}}
{{--            </ul>--}}

            <h3> </h3>
            <p>{{ __('labels.u000top.explain_1') }}<br />
                {{ __('labels.u000top.explain_2') }}
            </p>

            <div class="js-scrollable eol tbl-data">
                <table class="normal_b" id="tblToDoList">
                    <thead>
                        <tr>
                            <th style="min-width:9em;">{{ __('labels.u000top.deadline_response_ams') }}</th>
                            <th>{{ __('labels.u000top.application_number') }}</th>
                            <th>{{ __('labels.u000top.customer_refer_number') }}</th>
                            <th style="min-width:12em;">{{ __('labels.u000top.trademark_name') }}</th>
                            <th style="min-width:24em;">{{ __('labels.u000top.content') }}</th>
                            <th>{{ __('labels.u000top.announcement_date') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($toDoList->items()))
                            @foreach ($toDoList->items() as $todo)
                                @php
                                    $trademark = null;
                                    if(isset($todo->notice)) {
                                        $trademark = $todo->notice->trademark;
                                        $notice = $todo->notice;
                                    }
                                    $backgroundTd = $todo->getClassColorTop();
                                @endphp
                                <tr>
                                    <td class="{{ $backgroundTd }}">
                                        {{ isset($todo->response_deadline_ams) && $todo->response_deadline_ams  ? Carbon\Carbon::parse($todo->response_deadline_ams)->format('Y/m/d'): '' }}
                                    </td>
                                    <td class="{{ $backgroundTd }}">
                                        <a href="{{ $trademark ? route('user.application-detail.index', ['id' =>  $trademark->id ?? '', 'from' =>  FROM_U000_TOP ]) : '#!' }}">
                                            {{ $trademark->trademark_number ?? '' }}
                                        </a>
                                    </td>
                                    <td class="{{ $backgroundTd }}">{{ $trademark->reference_number ?? '' }}</td>
                                    <td class="{{ $backgroundTd }}">
                                        @if ($trademark && $trademark->type_trademark == App\Models\Trademark::TRADEMARK_TYPE_LETTER)
                                            <span class="trademark_name">{{  $trademark->name_trademark ?? '' }}</span>
                                        @else
                                            <img width="100px" src="{{ $trademark->image_trademark ?? '' }}" alt="" srcset="">
                                        @endif
                                    </td>
                                    <td class="{{ $backgroundTd }}">
                                        @if (isset($todo->redirect_page) && $todo->redirect_page)
                                            <a data-redirect="{{ $todo->redirect_page }}" data-notice-detail-id="{{ $todo->id }}" class="btn-todo-redirect" href="javascript:void(0)">
                                                {{ $todo->content }}
                                            </a>
                                        @else
                                            <span>{{ $todo->content }}</span>
                                        @endif
                                    </td>
                                    <td class="{{ $backgroundTd }}">{{ isset($todo->created_at) ? Carbon\Carbon::parse($todo->created_at)->format('Y/m/d'): '' }}</td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td  colspan="6" style="text-align:center">
                                    {{ __('messages.general.Common_E032')}}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div><!-- /scroll wrap -->
            @if($toDoList->total() > PAGE_LIMIT_10)
            <div class="eol">
                <a id="showAllToDoList" href="javascript:void(0)">+ {{ __('labels.u000top.full_display') }}</a>
            </div>
            @endif

            <h3>{{ __('labels.u000top.title_under_consideration') }}</h3>

            <div class="js-scrollable eol tbl-data">
                <table class="normal_b" id="tblAppTrademarkApply">
                    <thead>
                        <tr>
                            <th style="min-width:10em;">{{ __('labels.u000top.save_day') }}</th>
                            <th style="min-width:10em;">{{ __('labels.u000top.application_number') }}</th>
                            <th style="min-width:10em;">{{ __('labels.u000top.quotation_number') }}</th>
                            <th style="min-width:7em;">{{ __('labels.u000top.reference_number') }}</th>
                            <th style="min-width:16em;">{{ __('labels.u000top.trademark_name') }}</th>
                            <th class="th_distinction-product-cls">{{ __('labels.u000top.product_name') }}</th>
                            <th style="min-width:8em;">{{ __('labels.u000top.content') }}</th>
                            <th style="min-width:8em;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($appTrademarksApply->items()))
                            @foreach ($appTrademarksApply->items() as $appTrademark)
                                <tr>
                                    @php
                                        $trademark = $appTrademark->trademark;
                                    @endphp
                                    <td>
                                        {{ Carbon\Carbon::parse($appTrademark->created_at ?? ($prechecks->created_at ?? $sft->created_at))->format('Y/m/d') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('user.application-detail.index', ['id' =>  $trademark->id, 'from' =>  U000TOP ]) }}">
                                        {{ $trademark->trademark_number }}
                                        </a>
                                    </td>
                                    <td>
                                        @if(isset($trademark) && isset($trademark->payment) && (int)$trademark->payment->payment_status != App\Models\Payment::STATUS_PAID)
                                            <span class="quote_number">{{ $trademark->payment->quote_number }}</span>
                                            @php
                                                $secret = Str::random(11);
                                                Session::put($secret, [
                                                    'payment_id' => $trademark->payment->id,
                                                    'payment_type' => $trademark->payment->payerInfo->payment_type ?? 0,
                                                    'from_page' => $trademark->payment->from_page,
                                                    'route_back' => url()->full()
                                                ]);
                                            @endphp
                                            <a class="btn_a" href="{{ route('user.payment.index', ['s' => $secret]) }}">お申込み</a>
                                        @endif
                                    </td>
                                    <td>{{ $trademark->reference_number }}</td>
                                    <td>
                                        @if ($trademark->type_trademark == App\Models\Trademark::TRADEMARK_TYPE_LETTER)
                                            <span class="trademark_name">{{  $trademark->name_trademark }}</span>
                                        @else
                                            <img width="100px" src="{{ $trademark->image_trademark }}" alt="" srcset="">
                                        @endif
                                    </td>
                                    <td class="td_distinction-product-cls">
                                            @php
                                                $productsCount = 0;
                                                $products = $appTrademark->getProducts();
                                                if(count($products) > 3) {
                                                    $takeProducts = array_slice($products,0, 3);
                                                }else {
                                                    $takeProducts = $products;
                                                }
                                                $productsCount = count($takeProducts);
                                            @endphp
                                            @foreach ($takeProducts as $key => $text)
                                                <div>
                                                    {{ $text }}
                                                    @if (count($products) > 3 && $key == count($takeProducts) - 1 )
                                                        <span>
                                                            <a href="javascript:void(0)" data-app-trademark-id="{{ $appTrademark->id }}" class="showAllProductAppTrademark">[+]</a>
                                                        </span>
                                                    @endif
                                                </div>
                                            @endforeach
                                    </td>
                                    <td class="td-btn-redirect-u021b center">
                                        <button class="btn_b btn-type-redirect" data-trademark-id="{{ $trademark->id }}" data-status="{{ $appTrademark->status }}" data-type-page="{{ $appTrademark->type_page }}" type="button">
                                            {{ __('labels.u000top.express') }}
                                        </button>
                                    </td>
                                    <td class="td-btn-delete center">
                                        <button class="btn_d delete-anken" data-trademark-id="{{ $trademark->id }}" type="button">
                                            {{ __('labels.u000top.delete') }}
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td  colspan="8" style="text-align:center">
                                    {{ __('messages.general.Common_E032')}}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div><!-- /scroll wrap -->
            @if($appTrademarksApply->total() > PAGE_LIMIT_10)
            <div class="eol">
                <a id="showAllATMApply" href="javascript:void(0)">+ {{ __('labels.u000top.full_display') }}</a>
            </div>
            @endif
            <h3>{{ __('labels.u000top.notification_list') }}</h3>
            {{-- Top 10 notice detail is new arrival --}}
            <dl class="w08em clearfix">
                <div id="noticeList">
                    @if ($top10NoticeDetails->total())
                        @foreach ($top10NoticeDetails->items() as $noticeDetail)
                            <div style="margin-bottom: 10px">
                                <span style="margin-right: 40px">{{ isset($noticeDetail->created_at) ? Carbon\Carbon::parse($noticeDetail->created_at)->format('Y/m/d') : '' }}</span>
                                <span style="margin-right: 40px">{{ $noticeDetail->trademark_number ?? '' }}</span>
                                <span style="margin-right: 40px">{{ $noticeDetail->content ?? '' }}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="eol">{{ __('messages.general.Common_E032')}}</div>
                    @endif
                </div>
            </dl>
            @if ($top10NoticeDetails->total() > PAGE_LIMIT_10)
                <p class="eol"><a href="javascript:void(0)" id="showAllNotice">{{ __('labels.u000top.show_all_notice') }}</a></p>
            @endif
            <div>
                <div id="modalNoticeAll" class="modal fade" role="dialog">
                    <div class="modal-dialog" style="min-width: 80%; max-height: 400px">
                        <!-- Modal content-->
                        <div class="modal-content" style="margin-top: 20%;">
                            <div class="modal-body">
                                <button type="button" class="close-alert" data-dismiss="modal">&times;</button>
                                <div class="content loaded">
                                    <dl class="w08em clearfix" style="margin-left: 40px;">
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <h3>{{ __('labels.u000top.my_folder') }}</h3>
            <p>{{ __('labels.u000top.sub_title_my_folder') }}</p>

            <div class="js-scrollable tbl-data">
                <table class="normal_b" id="myFolderTbl">
                    <thead>
                        <tr>
                            <th>{{ __('labels.u000top.save_day') }}</th>
                            <th>{{ __('labels.u000top.save_number') }}</th>
                            <th>{{ __('labels.u000top.reference_number') }}</th>
                            <th style="min-width:12em;">{{ __('labels.u000top.trademark_name') }}</th>
                            <th style="min-width:24em;">{{ __('labels.u000top.product_name') }}</th>
                            <th style="min-width:12em;">{{ __('labels.u000top.content') }}</th>
                            <th style="min-width:7em;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($myFolders->items()))
                            @foreach ($myFolders->items() as $folder)
                                <tr>
                                    @php
                                        $trademark = null;
                                        if($folder->type != App\Models\MyFolder::TYPE_OTHER && isset($folder->relationTrademark)) {
                                            $trademark = $folder->relationTrademark->trademark;
                                        }
                                    @endphp
                                    <td class="center">{{ Carbon\Carbon::parse($folder->created_at)->format('Y/m/d') ?? ''}}</td>
                                    <td>{{ $folder->folder_number ?? ''}}</td>
                                    <td>
                                    {{ $trademark->reference_number ?? '' }}
                                    </td>
                                    <td>
                                        @if (isset($trademark->type_trademark) && $trademark->type_trademark == App\Models\Trademark::TRADEMARK_TYPE_LETTER)
                                            {{ $trademark->name_trademark }}
                                        @elseif(isset($trademark->type_trademark) && $trademark->type_trademark == App\Models\Trademark::TRADEMARK_TYPE_OTHER)
                                            @if($folder->image_trademark)
                                                <img width="100px" src="{{ $trademark->image_trademark }}" alt="">
                                            @endif
                                        @else
                                            @if ($folder->type_trademark  == App\Models\Trademark::TRADEMARK_TYPE_LETTER)
                                                {{ $folder->name_trademark ?? '' }}
                                            @else
                                                @if($folder->image_trademark)
                                                    <img width="100px" src="{{ $folder->image_trademark }}" alt="">
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                    <td class="td-product-name">
                                        @if (isset($folder->myFolderProduct) && $folder->myFolderProduct)
                                           @php
                                               $total = count($folder->myFolderProduct);
                                           @endphp
                                            @foreach ($folder->myFolderProduct as $key => $mfProd)
                                                {{ (string)$mfProd->getNameProd() . ($key < $total - 1 ? '、' : '') }}
                                            @endforeach
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('user.search-ai.goto-result', ['folder_id' => $folder->id]) }}">
                                            <button type="button" class="btn_e redirect-to-u020b">{{ __('labels.u000top.my_folder_list') }}</button>
                                        </a>
                                    </td>
                                    <td>
                                        <button type="button" class="btn_d delete-my-folder" data-folder-id="{{ $folder->id }}" >{{ __('labels.u000top.delete') }}</button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td  colspan="7" style="text-align:center">
                                    {{ __('messages.general.Common_E032')}}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div><!-- /scroll wrap -->
            @if($myFolders->total() > PAGE_LIMIT_3)
            <div class="eol">
                <a id="showAllMyFoler" href="javascript:void(0)">+ {{ __('labels.u000top.full_display') }}</a>
            </div>
            @endif
            <br />
            <h3>{{ __('labels.u000top.title_before_app') }}</h3>
            <p>{{ __('labels.u000top.explain_3') }}</p>
            <div class="js-scrollable tbl-data">
                <table class="normal_b" id="tblAppTrademarkNotApply">
                    <thead>
                        <tr>
                            <th style="min-width:7em;">{{ __('labels.u000top.save_day') }}
                                <a href="javascript:void(0)" data-sort='desc' class="btn-sort">▼</a>
                                <a href="javascript:void(0)" data-sort='asc' class="btn-sort">▲</a>
                            </th>
                            <th style="min-width:8em;">{{ __('labels.u000top.provisional_app_num') }}</th>
                            <th style="min-width:10em;">{{ __('labels.u000top.quotation_number') }}</th>
                            <th style="min-width:7em;">{{ __('labels.u000top.customer_refer_number') }}</th>
                            <th style="min-width:16em;">{{ __('labels.u000top.trademark_name') }}</th>
                            <th class="td_distinction-product-cls">{{ __('labels.u000top.product_name') }}</th>
                            <th style="min-width:8em;">{{ __('labels.u000top.service_under_consideration') }}</th>
                            <th style="min-width:8em;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @if (count($trademarksNotApply->items()))
                            @foreach ($trademarksNotApply->items() as $trademark)
                                <tr>
                                    <td>
                                        {{ $trademark->created_at ? Carbon\Carbon::parse($trademark->created_at)->format('Y/m/d'): '' }}
                                    </td>
                                    <td>{{  $trademark->trademark_number ?? '' }}</td>
                                    <td>
                                        @if(isset($trademark) && isset($trademark->payment) && !$trademark->payment->payment_status)
                                            <span class="quote_number">{{ $trademark->payment->quote_number }}</span>
                                            @php
                                                $secret = Str::random(11);
                                                Session::put($secret, [
                                                    'payment_id' => $trademark->payment->id,
                                                    'payment_type' => $trademark->payment->payerInfo->payment_type ?? 0,
                                                    'from_page' => $trademark->payment->from_page,
                                                    'route_back' => url()->full()
                                                ]);
                                            @endphp
                                            <a class="btn_a" href="{{ route('user.payment.index', ['s' => $secret ]) }}">お申込み</a>
                                        @endif
                                    </td>
                                    <td>{{ $trademark->reference_number ?? '' }}</td>
                                    <td>
                                        @if ($trademark->type_trademark == App\Models\Trademark::TRADEMARK_TYPE_LETTER)
                                            <span class="trademark_name">{{  $trademark->name_trademark }}</span>
                                        @else
                                            <img width="100px" src="{{ $trademark->image_trademark }}" alt="" srcset="">
                                        @endif
                                    </td>
                                    <td class="td_distinction-product-cls">
                                        @php
                                            $products = $trademark->getProductsWithRelation();
                                            if(count($products) > 3) {
                                                $takeProducts = array_slice($products,0, 3);
                                            }else {
                                                $takeProducts = $products;
                                            }
                                        @endphp
                                        @foreach ($takeProducts as $key => $text)
                                            <div>
                                                {{ $text }}
                                                @if (count($products) > 3 && $key == count($takeProducts) - 1 )
                                                    <span>
                                                        <a href="javascript:void(0)" data-trademark-id="{{ $trademark->id }}" class="showAllProductTrademark">[+]</a>
                                                    </span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </td>
                                    <td class="td-btn-redirect-u021b center">
                                        <button
                                            class="btn_b btn-type-redirect"
                                            data-trademark-id="{{ $trademark->id }}"
                                            data-status="{{ isset($trademark->appTrademark) ? $trademark->appTrademark->status : '' }}"
                                            data-type-page="{{ isset($trademark->appTrademark) ? $trademark->appTrademark->type_page : '' }}"
                                            type="button"
                                        >
                                            表示
                                        </button>
                                    </td>
                                    <td class="td-btn-delete center">
                                        <button class="btn_d delete-anken" data-trademark-id="{{ $trademark->id }}" type="button">
                                            削除
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                        <tr>
                            <td  colspan="8" style="text-align:center">
                                {{ __('messages.general.Common_E032')}}
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div><!-- /scroll wrap -->
            <div class="eol mt-2">
                @if($trademarksNotApply->total() > PAGE_LIMIT_10)
                    <a id="showAllATMNotApply" href="javascript:void(0)">+ {{ __('labels.u000top.full_display') }}</a>
                @endif
            </div>

            <ul class="footerBtn clearfix">
                <li>
                    <a style="border:none"  href="{{ route('user.menu-new-apply') }}">
                        <button type="button" class="btn_b" style="font-size: 1em;">
                            {{ __('labels.u000top.project_list') }}
                        </button>
                    </a>
                </li>
                <li>
                    <a style="border:none" href="{{ route('user.application-list') }}">
                        <button type="button" class="btn_a" style="font-size: 1em;" >
                            {{ __('labels.u000top.my_list') }}
                        </button>
                    </a>
                </li>
            </ul>
        </form>

    </div><!-- /contents -->
@endsection
@section('script')
<script type="text/JavaScript">
    const routeAllMyFolder = '{{ route('user.top.all-my-folder') }}'
    const routeAllToDoList = '{{ route('user.top.all-to-do') }}'
    const routeAllNotApplyList = '{{ route('user.top.all-not-apply') }}'
    const routeAllApplyList = '{{ route('user.top.all-apply') }}'
    const routeAllNotice = '{{ route('user.top.all-notice') }}'
    const routeRedirectWithType = '{{ route('user.top.route.redirect.type') }}'

    const messageDeleteAnken = '{{ __('labels.u000top.delete_anken_question') }}'
    const YES = '{{ __('labels.btn_yes') }}'
    const CANCEL = '{{ __('labels.btn_cancel') }}'
</script>
<script type="text/JavaScript" src="{{ asset('end-user/top/u000top.js') }}"></script>
@endsection
