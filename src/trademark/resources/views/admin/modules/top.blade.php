@extends('admin.layouts.app')

@section('main-content')

    <div id="contents">
        <h2>{{ __('labels.admin_top.title') }}</h2>

        <!-- contents inner -->
        <div class="wide clearfix">
            @include('admin.components.includes.messages')

            <h2>{{ __('labels.admin_top.todo.title') }}</h2>
            <div class="eol">
                <p class="mb-1">{{ __('labels.admin_top.todo.desc') }}</p>

                <div class="overflow-auto" style="max-height: 500px;">
                    <table class="normal_b" id="todo-list">
                        <tr>
                            <th style="width:12em;">{{ __('labels.admin_top.todo.created_at') }}</th>
                            <th style="width:24em;">{{ __('labels.admin_top.todo.redirect_page') }}</th>
                            <th style="width:8em;">{{ __('labels.admin_top.todo.username') }}</th>
                            <th style="width:8em;">{{ __('labels.admin_top.todo.trademark_number') }}</th>
                            <th style="width:2em;">{{ __('labels.admin_top.todo.notice_response_deadline') }}</th>
                            <th style="width:2em;">{{ __('labels.admin_top.todo.response_deadline') }}</th>
                        </tr>

                        @forelse($todoLists as $item)
                            <tr class="{{ ($loop->index >= PAGE_LIMIT_10) ? 'hidden' : '' }}">
                                <td>{{ $item->created_at->format('Y/m/d H:i') }}</td>
                                <td>
                                    @if(!empty($item->redirect_page))
                                        <a href="{{ $item->redirect_page ?? '' }}">{{ $item->content ?? '' }}</a>
                                    @else
                                        {{ $item->content ?? '' }}
                                    @endif
                                </td>
                                <td>{{ $item->username ?? '' }}</td>
                                <td>
                                    @if(!empty($item->trademark))
                                        <a href="{{ route('admin.application-detail.index', $item->trademark->id) }}">{{ $item->trademark->trademark_number ?? '' }}</a>
                                    @endif
                                </td>
                                <td>{{ CommonHelper::formatTime($item->response_deadline ?? '', 'Y/m/d')  }}</td>
                                <td>{{ $item->comparison_response_deadline ?? '' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="center">{{ __('messages.general.Common_E032') }}</td>
                            </tr>
                        @endforelse
                    </table>
                    <!-- /更新一覧 -->
                </div>
                @if($todoLists->count() > PAGE_LIMIT_10)
                    <a href="#" data-show_table="#todo-list">+ {{ __('labels.admin_top.see_more') }}</a>
                @endif
            </div>

            <h2>{{ __('labels.admin_top.notice_user.title') }}</h2>

            <div class="eol">
                <div class="overflow-auto" style="max-height: 500px;">
                    <table class="normal_b" id="notice-user-list">
                        <tr>
                            <th style="width:12em;">{{ __('labels.admin_top.notice_user.created_at') }}<br />{{ __('labels.admin_top.notice_user.created_at_desc') }}</th>
                            <th style="width:24em;">{{ __('labels.admin_top.notice_user.content') }}</th>
                            <th style="width:14em;">{{ __('labels.admin_top.notice_user.username') }}</th>
                            <th style="width:2em;">{{ __('labels.admin_top.notice_user.is_open') }}</th>
                            <th style="width:2em;">{{ __('labels.admin_top.notice_user.trademark_number') }}</th>
                            <th style="width:2em;">{{ __('labels.admin_top.notice_user.notice_response_deadline') }}</th>
                            <th style="width:2em;">{{ __('labels.admin_top.notice_user.response_deadline') }}</th>
                        </tr>
                        @forelse($noticeUserList as $item)
                            <tr class="{{ ($loop->index >= PAGE_LIMIT_10) ? 'hidden' : '' }}">
                                <td>{{ $item->created_at->format('Y/m/d H:i') }}</td>
                                <td>{{ $item->content ?? '' }}</td>
                                <td>{{ $item->username ?? '' }}</td>
                                <td class="center">{{ $item->isOpen() ? '◎' : '' }}</td>
                                <td class="center">
                                    @if(!empty($item->trademark))
                                        <a href="{{ route('admin.application-detail.index', $item->trademark->id) }}">{{ $item->trademark->trademark_number ?? '' }}</a>
                                    @endif
                                </td>
                                <td class="center">{{ CommonHelper::formatTime($item->response_deadline ?? '', 'Y/m/d')  }}</td>
                                <td class="center">{{ $item->comparison_response_deadline ?? '' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="100%" class="center">{{ __('messages.general.Common_E032') }}</td>
                            </tr>
                        @endforelse
                    </table>
                </div>
                @if($noticeUserList->count() > PAGE_LIMIT_10)
                    <a href="#" data-show_table="#notice-user-list">+ {{ __('labels.admin_top.see_more') }}</a>
                @endif
            </div>
            <!-- /回答のないお客様リスト -->

            @php
                $adminUser = Auth::guard('admin')->user();
            @endphp
            @if( in_array($adminUser->role, [ ROLE_OFFICE_MANAGER, ROLE_SUPERVISOR ]) )
                <h2><a href="{{ route('admin.import-doc-xml') }}">{{ __('labels.admin_top.import_history.title') }}</a></h2>
                <p class="eol">
                    <input type="button" onclick="$('#import-history').removeClass('hidden');" value="{{ __('labels.admin_top.import_history.show') }}" class="btn_a" />
                </p>
                <div class="eol">
                    <div class="overflow-auto" style="max-height: 500px;">
                        <table class="normal_b w-100 hidden" id="import-history">
                            <tr>
                                <th style="width:6em;">{{ __('labels.admin_top.import_history.XML_document_name') }}</th>
                                <th style="width:4em;">{{ __('labels.admin_top.import_history.XML_reference_number') }}</th>
                                <th style="width:4em;">{{ __('labels.admin_top.import_history.XML_application_number') }}</th>
                                <th style="width:4em;">{{ __('labels.admin_top.import_history.XML_delivery_date') }}</th>
                                <th style="width:6em;">{{ __('labels.admin_top.import_history.target_reference_number') }}</th>
                                <th style="width:6em;">{{ __('labels.admin_top.import_history.target_application_number') }}</th>
                            </tr>
                            @forelse($importHistory as $item)
                                <tr>
                                    <td>{{ $item->pi_document_name ?? '' }}</td>
                                    <td>{{ $item->pi_ar_reference_id ?? '' }}</td>
                                    <td>{{ $item->pi_ar_application_number ?? '' }}</td>
                                    <td>{{ !empty($item->pi_dd_date) ? CommonHelper::formatTime($item->pi_dd_date, 'Y/m/d H:i') : '' }}</td>
                                    <td>
                                        @if($item->trademark)
                                            <a href="{{ route('admin.application-detail.index', $item->trademark_id) }}" target="_blank">
                                                {{ $item->pi_ar_reference_id ?? '' }}
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        @if($item->trademark)
                                            <a href="{{ route('admin.application-detail.index', $item->trademark_id) }}" target="_blank">
                                                {{ $item->pi_ar_application_number ?? '' }}
                                            </a>
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
                </div>
            @endif

            <h2 class="eol">
                <a href="{{ route('admin.payment-check.bank-transfer') }}">{{ __('labels.admin_top.payment.title') }}</a>
            </h2>

            <h2>{{ __('labels.admin_top.search.title') }}</h2>
            <p>{{ __('labels.admin_top.search.desc') }}</p>

            <p>
                <label>
                    <input type="checkbox" name="has_close"> {{ __('labels.admin_top.search.has_close') }}
                </label>
            </p>

            <ul class="r_c mb10 clearfix">
                <li><label><input type="radio" name="type_search" value="and" checked />{{ __('labels.admin_top.search.type_search.and') }}</label></li>
                <li><label><input type="radio" name="type_search" value="or" />{{ __('labels.admin_top.search.type_search.or') }}</label></li>
            </ul>

            <form id="form-search">
                <table class="normal_a mb10">
                    @for($i = 0; $i < 3; $i++)
                        <tr class="search-item">
                            <td>
                                <select class="search_field w-100"></select>
                            </td>
                            <td>
                                <input type="text" class="search_value w-100" />
                            </td>
                            <td>
                                <select class="search_condition w-100"></select>
                            </td>
                        </tr>
                    @endfor
                </table>

                <ul class="r_c eol clearfix">
                    <li><input type="reset" value="{{ __('labels.clear') }}" class="btn_a" /></li>
                    <li><input type="button" id="submit-search" value="{{ __('labels.search') }}" class="btn_b" /></li>
                </ul>
            </form>

            @if( in_array($adminUser->role, [ ROLE_OFFICE_MANAGER, ROLE_SUPERVISOR ]) )
                <h2 class="eol">
                    <a href="{{ route('admin.agent.index') }}">{{ __('labels.admin_top.agent.title') }}</a>
                </h2>
            @endif

            <h2 class="eol">
                <a href="{{ route('admin.notify.index') }}">{{ __('labels.a000_news_edit.title') }}</a>
            </h2>

            <h2 class="eol">
                <a href="{{ route('admin.goods-master-search') }}">{{ __('labels.admin_top.goods_master_search.title') }}</a>
            </h2>
        </div>
    </div>
@endsection

@section('footerSection')
    <script>
        const URL_LIST_ANKEN = '{{ route('admin.search.application-list', [ 'filter' => 1 ]) }}';
        const SESSION_SEARCH_TOP = '{{ SESSION_SEARCH_TOP }}';
        const errorMessageFormatDatetime = '{{ __('messages.general.Common_E006') }}';
        const errorMessageMaxLength = '{{ __('messages.general.Common_E031') }}';

        const searchFields = {
            trademark_number: {
                title: '申込番号',
            },
            created_at: {
                title: '申込日',
                typing: 'date',
            },
            name_trademark: {
                title: '商標名',
            },
            application_date: {
                title: '出願日',
                typing: 'date',
            },
            application_number: {
                title: '出願番号',
            },
            date_register: {
                title: '登録日',
                typing: 'date',
            },
            register_number: {
                title: '登録番号',
            },
            notice_content: {
                title: '作業内容',
            },
            user_info_name: {
                title: '会員名',
            },
            trademark_info_name: {
                title: '出願人名',
            },
            register_trademark_info_name: {
                title: '登録名義人名',
            }
        };

        const conditions = {
            equal: '等しい',
            start_from: 'から始まる',
            consists_of: 'を含む',
        }

        const conditionDate = {
            equal: '等しい',
            is_greater_than: 'が...以上',
            is_less_than: 'が...以下',
        }
    </script>
    <script src="{{ asset('admin_assets/pages/top/index.js') }}"></script>
@endsection
