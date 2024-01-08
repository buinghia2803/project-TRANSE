@extends('user.layouts.app')
@section('main-content')
    <div id="contents" class="normal">
        @include('compoments.messages')

        <h2>{{ __('labels.search-ai.report.title') }}</h2>
        <form id="search-ai-report" action="{{ route('user.search-ai.quote.send-data') }}" method="POST">
            @csrf
            <input type="hidden" name="submit_type" value="">

            <h3>{{ __('labels.search-ai.report.text_1') }}</h3>
            <div class="clearfix">
                <div class="column3">
                    <table class="normal_b">
                        <caption>
                            <strong>
                                <label>
                                    <input type="radio" name="type" value="3" checked />{{ __('labels.search-ai.report.pack_c') }}
                                </label>
                                <br />
                                {{ __('labels.search-ai.report.text_3') }}<br />
                                {{ __('labels.search-ai.report.text_4') }}{{ CommonHelper::formatPrice($packCPrice['price_one_block'] ?? 0, '円') }}{{ __('labels.search-ai.report.text_5') }}
                            </strong>
                        </caption>
                        <tr>
                            <td style="width:12em;">{{ __('labels.search-ai.report.price_pack') }}</td>
                            <td class="right">
                                {{ CommonHelper::formatPrice($packCPrice['base_price'] ?? 0, '円') }}
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('labels.search-ai.report.product_add', [
                                'prod_add_total' => $packCPrice['prod_add_total'] ?? 0
                            ]) }}</td>
                            <td class="right">
                                {{ CommonHelper::formatPrice($packCPrice['prod_add'] ?? 0, '円') }}
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('labels.search-ai.report.price_tax') }}</td>
                            <td class="right">xxx円</td>
                        </tr>
                        <tr>
                            <th class="right">{{ __('labels.search-ai.report.total') }}：</th>
                            <th class="right" nowrap><strong
                                    class="fs12">{{ CommonHelper::formatPrice($packCPrice['total'] ?? 0, '円') }}</strong>
                            </th>
                        </tr>
                    </table>
                </div>

                <!-- /columne3 -->
                <div class="column3">
                    <table class="normal_b">
                        <caption>
                            <strong>
                                <label>
                                    <input type="radio" name="type" value="2" />{{ __('labels.search-ai.report.pack_b') }}
                                </label>
                                <br />
                                {{ __('labels.search-ai.report.text_11') }}<br />
                                {{ __('labels.search-ai.report.text_4') }}{{ CommonHelper::formatPrice($packBPrice['price_one_block'] ?? 0, '円') }}{{ __('labels.search-ai.report.text_5') }}
                            </strong>
                        </caption>
                        <tr>
                            <td style="width:12em;">{{ __('labels.search-ai.report.price_pack') }}</td>
                            <td class="right">
                                {{ CommonHelper::formatPrice($packBPrice['base_price'] ?? 0, '円') }}
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('labels.search-ai.report.product_add', [
                                'prod_add_total' => $packBPrice['prod_add_total'] ?? 0
                            ]) }}</td>
                            <td class="right">
                                {{ CommonHelper::formatPrice($packBPrice['prod_add'] ?? 0, '円') }}
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('labels.search-ai.report.price_tax') }}</td>
                            <td class="right">xxx円</td>
                        </tr>
                        <tr>
                            <th class="right">{{ __('labels.search-ai.report.total') }}：</th>
                            <th class="right" nowrap><strong
                                    class="fs12">{{ CommonHelper::formatPrice($packBPrice['total'] ?? 0, '円') }}</strong>
                            </th>
                        </tr>
                    </table>
                </div>

                <!-- /columne3 -->
                <div class="column3">
                    <table class="normal_b">
                        <caption>
                            <strong>
                                <label>
                                    <input type="radio" name="type" value="1" />{{ __('labels.search-ai.report.pack_a') }}
                                </label>
                                <br />
                                {{ __('labels.search-ai.report.text_2') }}<br />
                                {{ __('labels.search-ai.report.text_4') }}{{ CommonHelper::formatPrice($packAPrice['price_one_block'] ?? 0, '円') }}{{ __('labels.search-ai.report.text_5') }}
                            </strong>
                        </caption>
                        <tr>
                            <td style="width:12em;">{{ __('labels.search-ai.report.price_pack') }}</td>
                            <td class="right">
                                {{ CommonHelper::formatPrice($packAPrice['base_price'] ?? 0, '円') }}
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('labels.search-ai.report.product_add', [
                                'prod_add_total' => $packAPrice['prod_add_total'] ?? 0
                            ]) }}</td>
                            <td class="right">
                                {{ CommonHelper::formatPrice($packAPrice['prod_add'] ?? 0, '円') }}
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('labels.search-ai.report.price_tax') }}</td>
                            <td class="right">xxx円</td>
                        </tr>
                        <tr>
                            <th class="right">{{ __('labels.search-ai.report.total') }}：</th>
                            <th class="right" nowrap><strong
                                    class="fs12">{{ CommonHelper::formatPrice($packAPrice['total'] ?? 0, '円') }}</strong>
                            </th>
                        </tr>
                    </table>
                </div>
                <!-- /columne3 -->
            </div>
            <!-- /clearfix -->
            <ul class="footerBtn clearfix">
                <li><input type="submit" data-submit="{{ SEARCH_AI_REGISTER }}" value="出願申込へ進む" class="btn_e" /></li>
            </ul>
            @if($isShowRegister)
                <ul class="footerBtn clearfix">
                    <li><input type="submit" data-submit="{{ SEARCH_AI_PRECHECK }}" value="プレチェックレポートが必要な方"
                               class="btn_b" /></li>
                </ul>
            @endif
            <ul class="footerBtn clearfix">
                <li><input type="submit" data-submit="{{ SEARCH_AI_CREATE }}" value="マイリストを保存" class="btn_a" /></li>
                @if (!empty($listProduct['folder_id']))
                    <li><input type="submit" data-submit="{{ SEARCH_AI_EDIT }}" value="マイリストを上書き保存" class="btn_a" /></li>
                @endif
            </ul>
        </form>
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
    <script>
        const IS_MAX_FOLDER = @json($folders);
        const lengthIsMaxFolder = IS_MAX_FOLDER.length;
        const DELETE_POPUP_TITLE = '{{ __('labels.suggest_ai.delete_message') }}';
        const YES = '{{ __('labels.suggest_ai.yes') }}';
        const NO = '{{ __('labels.suggest_ai.no') }}';

        const SEARCH_AI_PRECHECK = '{{ SEARCH_AI_PRECHECK }}';
        const IS_TRADEMARK_IMAGE = '{{ $isTrademarkImage ?? false }}';
        const errorMessageTrademarkImage = '{{ __('messages.general.support_U011_E008') }}';

        // Click Submit
        $('body').on('click', 'input[data-submit]', function (e) {
            e.preventDefault();
            let form = $('#search-ai-report');
            let submitType = $(this).data('submit');
            form.find('input[name=submit_type]').val(submitType);

            if(submitType == SEARCH_AI_PRECHECK && IS_TRADEMARK_IMAGE == '1') {
                $(this).parent().find('.red').remove();
                $(this).after(`<div class="red">${errorMessageTrademarkImage}</div>`);

                return false;
            } else if (lengthIsMaxFolder >= 5 && submitType == '{{ SEARCH_AI_CREATE }}') {
                $.confirm({
                    title: '',
                    content: DELETE_POPUP_TITLE,
                    buttons: {
                        cancel: {
                            text: NO,
                            btnClass: 'btn-default',
                            action: function() {}
                        },
                        ok: {
                            text: YES,
                            btnClass: 'btn-blue',
                            action: function() {
                                loadingBox('open');
                                form.submit();
                            }
                        }
                    }
                });
            } else {
                setTimeout(function () {
                    loadingBox('open');
                    form.submit();
                }, 100);
            }
        });
    </script>
@endsection
