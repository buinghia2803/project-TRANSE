@extends('user.layouts.app')

@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.suggest_ai.title') }}</h2>
        <form>
            @include('compoments.messages')

            <div class="clearfix">
                @if ($referer == FROM_SEARCH_AI || $referer == FROM_U031B)
                    <div class="col col-md-6 col-lg-4 px-1">
                        <h3>{{ __('labels.suggest_ai.match_table.title') }}</h3>
                        <div class="if560" style="margin-bottom:100px;">
                            <p class="eol">{!! __('labels.suggest_ai.match_table.desc') !!}</p>

                            @foreach ($keywordDataMatch as $data)
                                <p class="mb00">
                                    {{ __('labels.suggest_ai.match_table.keyword') }}：{{ $data['keyword'] ?? '' }}</p>
                                <table class="normal_b eol match-table" style="width: 100%;">
                                    <tr>
                                        <th class="w-100px">{{ __('labels.suggest_ai.action') }}</th>
                                        <th class="w-50px">{{ __('labels.suggest_ai.distinction') }}</th>
                                        <th>{{ __('labels.suggest_ai.prod_name') }}</th>
                                    </tr>
                                    @foreach ($data['products'] as $product)
                                        <tr data-prod_id="{{ $product->id }}">
                                            <td class="center">
                                                <input type="button" value="{{ __('labels.suggest_ai.move') }}"
                                                    class="small btn_b" data-add_product />
                                                <input type="button" value="{{ __('labels.suggest_ai.selected') }}"
                                                    class="small btn_b" data-added_product
                                                    style="background-color: green; color: #fff; display: none;" />
                                            </td>
                                            <td><span class="distinction">{{ $product->mDistinction->name ?? '' }}</span>
                                            </td>
                                            <td><span class="prod_name">{{ $product->name ?? '' }}</span></td>
                                        </tr>
                                    @endforeach
                                </table>
                            @endforeach

                            @if (count($keywordDataNotMatch) > 0)
                                <p class="red">{{ __('labels.suggest_ai.match_table.no_keyword_match') }}</p>
                                <p class="mb00">{{ __('labels.suggest_ai.match_table.keyword') }}：</p>
                                <ul class="eol">
                                    @foreach ($keywordDataNotMatch as $data)
                                        <li>{{ $data['keyword'] ?? '' }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            <ul class="footerBtn clearfix">
                                <li>
                                    <input type="button" value="{{ __('labels.suggest_ai.match_table.re_search') }}"
                                        class="btn_a" onclick="window.location = '{{ route('user.search-ai', ['keyword' => $keywords]) }}'" />
                                </li>
                            </ul>

                            <p class="center">
                                <a href="javascript:;"
                                   onclick="setSession('{{ SESSION_MPRODUCT_NAME }}', JSON.parse('{{ json_encode($searchAiData['keyword'] ?? []) }}'), function() {
                                        window.open('{{ route('user.sft.index') }}', '_blank');
                                   })"
                                >{{ __('labels.suggest_ai.match_table.link_select_product') }}</a>
                                <br />
                                {!! __('labels.suggest_ai.match_table.link_select_product_note') !!}
                            </p>
                        </div><!-- /frame -->
                    </div><!-- /left -->
                @endif

                <div class="col col-md-6 col-lg-4 center px-1">
                    <h3 style="text-align:left;">{{ __('labels.suggest_ai.my_list.title') }}</h3>
                    <div class="if560 ivory" style="margin-bottom:100px;">

                        <p>商標名：{{ $nameTrademark ?? '' }}</p>

                        @if ($referer == FROM_SEARCH_AI || $referer == FROM_U031B)
                            <table class="normal_b eol" id="additional-list" style="width:100%;">
                                <caption>
                                    {{ __('labels.suggest_ai.my_list.additional_prod') }}<br />
                                    {{ __('labels.suggest_ai.my_list.additional_prod_desc') }}
                                </caption>
                                <tr>
                                    <th class="w-50px">{{ __('labels.suggest_ai.distinction') }}</th>
                                    <th>{{ __('labels.suggest_ai.prod_name') }}</th>
                                    <th class="w-50px">{{ __('labels.delete') }}</th>
                                </tr>

                                @foreach($additionProducts as $product)
                                    <tr data-prod_id="{{ $product->id ?? '' }}">
                                        <td class="distinction">{{ $product->mDistinction->name ?? '' }}</td>
                                        <td class="prod_name">{{ $product->name ?? '' }}</td>
                                        <td class="center"><input type="button" value="{{ __('labels.delete') }}" data-remove_product class="small btn_d"/></td>
                                    </tr>
                                @endforeach
                            </table>
                        @endif

                        @if (isset($suggestProduct) && count($suggestProduct) > 0)
                            <table class="normal_b mb10" id="list-suggest-product" style="width:100%;">
                                <caption>
                                    {{ __('labels.suggest_ai.my_list.suggest_prod') }}<br />
                                    {{ __('labels.suggest_ai.my_list.suggest_prod_desc') }}
                                </caption>
                                <tr>
                                    <th class="w-50px">{{ __('labels.suggest_ai.distinction') }}</th>
                                    <th>{{ __('labels.suggest_ai.prod_name') }}</th>
                                </tr>
                                @foreach ($suggestProduct as $product)
                                    <tr data-prod_id="{{ $product->id ?? '' }}" data-name="{{ $product->name ?? '' }}">
                                        <td class="distinction">{{ $product->mDistinction->name ?? '' }}</td>
                                        <td class="prod_name">{{ $product->name ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </table>
                        @endif
                    </div>
                </div>

                <div class="col col-md-6 col-lg-4 px-1">
                    <div class="float_btn" style="padding:0 1%; z-index:100;">
                        <input type="button" value="{{ __('labels.suggest_ai.suggest_list.ai_btn') }}" id="suggest-ai"
                            class="btn_a" />
                    </div>

                    <h3>{{ __('labels.suggest_ai.suggest_list.title') }}</h3>
                    <div class="hidden" id="suggest-ai-box">
                        <div class="if560" style="position:relative;margin-bottom:100px;">
                            @if ($referer == FROM_SEARCH_AI || $referer == FROM_U031B)
                                <p style="padding-top:50px;">{{ __('labels.suggest_ai.suggest_list.desc') }}</p>
                                <p class="mb00">{{ __('labels.suggest_ai.suggest_list.additional_prod') }}</p>
                                <div id="addition-product-box">
                                    {{-- <p class="mb00">商品・サービス名：密封容器</p> --}}
                                    {{-- <table class="normal_b mb20"> --}}
                                    {{--    <tr> --}}
                                    {{--        <th>追加リストへ</th> --}}
                                    {{--        <th>区分</th> --}}
                                    {{--        <th>商品・サービス名</th> --}}
                                    {{--    </tr> --}}
                                    {{--    <tr> --}}
                                    {{--        <td class="center"><input type="submit" value="追加" class="small btn_b"/></td> --}}
                                    {{--        <td>9</td> --}}
                                    {{--        <td>ガラス製又は陶磁製の包装用容器　プラスチック製の包装用瓶 （１）　ガラス製又は陶磁製の包装用容器イ）飲料用容器　化粧品用容器　食品用容器　薬品用容器</td> --}}
                                    {{--    </tr> --}}
                                    {{-- </table> --}}
                                </div>
                            @endif

                            <hr size="10">

                            @if (isset($suggestProduct) && count($suggestProduct) > 0)
                                <p class="mb20">{{ __('labels.suggest_ai.suggest_list.suggest_prod_desc') }}</p>
                                <p class="mb00">{{ __('labels.suggest_ai.suggest_list.suggest_prod') }}</p>
                                <div id="suggest-product-box">
                                    {{-- <p class="mb00">商品・サービス名： 乳飲料</p> --}}
                                    {{-- <table class="normal_b mb20"> --}}
                                    {{--    <tr> --}}
                                    {{--        <th>追加リストへ</th> --}}
                                    {{--        <th>区分</th> --}}
                                    {{--        <th>商品・サービス名</th> --}}
                                    {{--    </tr> --}}
                                    {{--    <tr> --}}
                                    {{--        <td class="center"><input type="submit" value="追加" class="small btn_b"/></td> --}}
                                    {{--        <td>32</td> --}}
                                    {{--        <td>ｘｘｘｘｘｘｘｘｘｘｘｘｘｘｘ</td> --}}
                                    {{--    </tr> --}}
                                    {{-- </table> --}}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @include('user.modules.search-ai.u020c_window' , ['suggestProduct' , 'pricePackage' , 'setting'])

            <ul class="footerBtn clearfix">
                <li><input type="button" value="{{ __('labels.suggest_ai.button.quote') }}"
                        onclick="openModal('#myModal'); pricePreview()"
                        class="btn_a" /></li>
            </ul>

            <ul class="footerBtn clearfix">
                <li>
                    <input type="button" value="{{ __('labels.suggest_ai.button.return') }}" class="btn_a"
                        @if ($referer == FROM_SEARCH_AI || $referer == FROM_U031B)
                            onclick="window.location = '{{ route('user.search-ai', ['keyword' => $keywords]) }}'"
                        @endif
                        @if ($referer == FROM_SUPPORT_FIRST_TIME)
                           onclick="window.location = '{{ route('user.support.first.time.u011b', $targetID ?? ($folder->target_id ?? '')) }}'"
                        @endif
                        @if ($referer == FROM_PRECHECK)
                           onclick="window.location = '{{ route('user.precheck.application-trademark', $trademark->id) }}'"
                        @endif
                    />
                </li>
                <li><input type="submit" data-submit="{{ SEARCH_AI_PRECHECK }}" formaction="{{ route('user.search-ai.quote.post') }}"
                        value="{{ __('labels.suggest_ai.button.next') }}" class="btn_b" /></li>
            </ul>

            <ul class="list">
                <li><input type="submit" data-submit="{{ SEARCH_AI_CREATE }}"
                        value="{{ __('labels.suggest_ai.button.save_as_new') }}" class="btn_a" /></li>
                @if (!empty($folder))
                    <li><input type="submit" data-submit="{{ SEARCH_AI_EDIT }}"
                            value="{{ __('labels.suggest_ai.button.save_as_edit') }}" class="btn_a" /></li>
                @endif
                @if($isShowRegister)
                    <li>
                        <input type="submit" data-submit="{{ SEARCH_AI_REGISTER }}"
                               value="{{ __('labels.suggest_ai.button.save_as_register') }}" class="btn_b" />
                    </li>
                @endif
            </ul>
        </form>
    </div><!-- /contents -->

    <!-- form-hidden -->
    <form action="{{ route('user.search-ai.result.post') }}" id="suggest-form" method="POST">
        @csrf
        <input type="hidden" name="referer" value="{{ $referer ?? '' }}">
        <input type="hidden" name="folder_id" value="{{ $folder->id ?? '' }}">
        <input type="hidden" name="target_id" value="{{ $targetID ?? ($folder->target_id ?? '') }}">
        <input type="hidden" name="keyword" value="{{ implode(',', $searchAiData['keyword'] ?? []) }}">
        <input type="hidden" name="trademark_id" value="{{ $trademark->id ?? '' }}">
        <input type="hidden" name="type_trademark" value="{{ $typeTrademark ?? '' }}">
        <input type="hidden" name="name_trademark" value="{{ $nameTrademark ?? '' }}">
        <input type="hidden" name="image_trademark" value="{{ $imageTrademark ?? '' }}">
        <input type="hidden" name="prod_additional_ids" value="">
        <input type="hidden" name="prod_suggest_ids" value="">
        <input type="hidden" name="submit_type" value="">
    </form>
@endsection

@section('headerSection')
<style>
    @media (max-width: 1200px) {
        .float_btn {
            position: absolute;
            top: 0;
            right: 15px;
        }
    }
    @media (max-width: 668px) {
        .float_btn {
            width: auto;
        }
    }
</style>
@endsection

@section('footerSection')
    <script>
        const ErrorMessageNotFoundProd = '{{ __('messages.common.errors.Search_AI_U000_E001') }}';
        const ErrorMessageSelectProd = '{{ __('messages.general.Search_AI_select_product') }}';
        const DELETE_PRODUCT = '{{ __('labels.delete') }}';
        const PRODUCT_NAME = '{{ __('labels.suggest_ai.prod_name') }}';
        const ADD_TO_LIST = '{{ __('labels.suggest_ai.action') }}';
        const DISTINCTION_NAME = '{{ __('labels.suggest_ai.distinction') }}';
        const ADD = '{{ __('labels.suggest_ai.add') }}';
        const IS_MAX_FOLDER = '{{ count($folders) == IS_MAX_FOLDER ? 'true' : 'false' }}';
        const SEARCH_AI_AJAX_URL = '{{ route('user.ajax-suggest-ai') }}';
        const SUBMIT_EDIT = '{{ SEARCH_AI_EDIT }}';
        const SEARCH_AI_REGISTER = '{{ SEARCH_AI_REGISTER }}';
        const SEARCH_AI_PRECHECK = '{{ SEARCH_AI_PRECHECK }}';

        const IS_TRADEMARK_IMAGE = '{{ $isTrademarkImage ?? false }}';
        const errorMessageTrademarkImage = '{{ __('messages.general.support_U011_E008') }}';

        const DELETE_POPUP_TITLE = '{{ __('labels.suggest_ai.delete_message') }}';
        const YES = '{{ __('labels.suggest_ai.yes') }}';
        const NO = '{{ __('labels.suggest_ai.no') }}';
    </script>
    <script src="{{ asset('end-user/search-ai/suggest-ai.js') }}"></script>
@endsection
