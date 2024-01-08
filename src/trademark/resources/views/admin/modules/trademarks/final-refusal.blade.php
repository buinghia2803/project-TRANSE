@extends('admin.layouts.app')
@section('main-content')
<!-- contents -->
<div id="contents">
    @include('compoments.messages')
    <!-- contents inner -->
    <div class="wide clearfix">
        <form id="form" action="{{ route('admin.refusal.final-refusal.post', ['id' => $trademark->id, 'maching_result_id' => $matchingResult->id]) }}" method="POST">
            @csrf
            {{-- Trademark table --}}
            @include('admin.components.includes.trademark-table', [
                'table' => $trademarkTable,
            ])

            <table class="normal_a">
                <caption>
                    {{ __('labels.a206kyo_s.caption_1') }}
                </caption>
                <tr>
                    <th>{{ __('labels.a206kyo_s.th_1') }}</th>
                    <td colspan="3">
                        {{ CommonHelper::formatTime($comparisonTrademarkResult->sending_noti_rejection_date, 'Y/m/d') }}
                        <input type="button" value="{{ __('labels.a206kyo_s.button_1') }}" class="btn_b click_pdf" data-type="1"/>
                        @foreach ($trademarkDocumentsType1 as $ele_a)
                            <a hidden href="{{ asset($ele_a) }}" class="a_type_1" target="_blank">{{ $ele_a }}</a>
                        @endforeach
                        <input type="button" value="{{ __('labels.a206kyo_s.button_2') }}" class="btn_a" onclick="window.open('{{ route('admin.refusal.eval-report.create-examine', ['id' => $comparisonTrademarkResult->id, 'type' => VIEW]) }}', '_blank');"/>
                        <input type="button" value="{{ __('labels.a206kyo_s.button_3') }}" class="btn_a" onclick="window.open('{{ route('admin.refusal.material.confirm', ['id' => $comparisonTrademarkResult->id, 'trademark_plan_id' => $trademarkPlanLast->id, 'type' => VIEW]) }}', '_blank');"/>
                        <input type="button" value="{{ __('labels.a206kyo_s.button_4') }}" class="btn_a" onclick="window.open('{{ route('admin.refusal.material.no-material', ['id' => $comparisonTrademarkResult->id, 'trademark_plan_id' => $trademarkPlanLast->id, 'type' => VIEW]) }}', '_blank');"/>
                    </td>
                </tr>
                <tr>
                    <th>{{ __('labels.a206kyo_s.th_2') }}</th>
                    <td>
                        {{ CommonHelper::formatTime($comparisonTrademarkResult->response_deadline, 'Y/m/d') }}
                    </td>
                </tr>
                <tr>
                    <th>{{ __('labels.a206kyo_s.th_3') }}</th>
                    <td colspan="3">
                        <input type="button" value="{{ __('labels.a206kyo_s.button_5') }}" class="btn_a click_pdf {{ count($trademarkDocumentsType2) ? '' : 'disabled' }}" data-type="2" {{ count($trademarkDocumentsType2) ? '' : 'disabled' }}/>
                        @foreach ($trademarkDocumentsType2 as $ele_a)
                            <a hidden href="{{ asset($ele_a) }}" class="a_type_2" target="_blank">{{ $ele_a }}</a>
                        @endforeach
                        <input type="button" value="{{ __('labels.a206kyo_s.button_6') }}" class="btn_a click_pdf {{ count($trademarkDocumentsType3) ? '' : 'disabled' }}" data-type="3" {{ count($trademarkDocumentsType3) ? '' : 'disabled' }}/>
                        @foreach ($trademarkDocumentsType3 as $ele_a)
                            <a hidden href="{{ asset($ele_a) }}" class="a_type_3" target="_blank">{{ $ele_a }}</a>
                        @endforeach
                    </td>
                </tr>
            </table>

            <br />
            <br />

            <h3>{{ __('labels.a206kyo_s.h3') }}</h3>

            <dl class="w16em clearfix">
                <dt>{{ __('labels.a206kyo_s.dt') }}</dt>
                <dd>{{ CommonHelper::formatTime($comparisonTrademarkResult->sending_noti_rejection_date) }}</dd>
            </dl>

            <table class="normal_b eol">
                <caption>
                    {{ __('labels.a206kyo_s.caption_2') }}
                </caption>
                <tr>
                    <th class="em08">{{ __('labels.a206kyo_s.distinction') }}</th>
                    <th class="em30">{{ __('labels.a206kyo_s.product') }}</th>
                </tr>
                @if (count($productsGroupedByDistinction) > 0)
                    @foreach ($productsGroupedByDistinction as $distinction => $products)
                        @php
                            $count = $products->count();
                        @endphp
                        @foreach ($products as $item)
                            <tr>
                                @if($loop->first)
                                    <td rowspan="{{ $count > 0 ? $count : '' }}">
                                        {{ __('labels.a206kyo_s.count_distinction', [
                                            'attr' => $distinction
                                        ]) }}<br />
                                        {{ __('labels.a206kyo_s.count_product', [
                                            'attr' => $count
                                        ]) }}
                                    </td>
                                @endif
                                <td>
                                    {{ $item['product']->name ?? null }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                    <tr>
                        <td colspan="2" class="right">
                            {{ __('labels.a206kyo_s.total', [
                                'attr1' => count($productsGroupedByDistinction),
                                'attr2' => $countProducts,
                            ]) }}<br />
                        </td>
                    </tr>
                @else
                    <tr>
                        <th colspan="2" class="bg_white">
                            {{ __('messages.general.Common_E032') }}
                        </th>
                    </tr>
                @endif
            </table>

            <h5>{{ __('labels.a206kyo_s.h5') }}</h5>
            <p>
                <textarea class="normal" name="comment_refusal">{{ $trademark->comment_refusal }}</textarea>
            </p>

            <table class="normal_b eol">
                <caption>
                    {{ __('labels.a206kyo_s.caption_3') }}
                    <br />
                    {{ __('labels.a206kyo_s.caption_4') }}
                </caption>
                <tr>
                    <th class="em08">{{ __('labels.a206kyo_s.distinction') }}</th>
                    <th class="em30">{{ __('labels.a206kyo_s.product') }}</th>
                </tr>
                @if (count($productsRankAGroupedByDistinction) > 0)
                    @foreach ($productsRankAGroupedByDistinction as $distinction => $products)
                        @php
                            $count = $products->count();
                        @endphp
                        @foreach ($products as $item)
                            <tr>
                                @if($loop->first)
                                    <td rowspan="{{ $count > 0 ? $count : '' }}">
                                        {{ __('labels.a206kyo_s.count_distinction', [
                                            'attr' => $distinction
                                        ]) }}<br />
                                        {{ __('labels.a206kyo_s.count_product', [
                                            'attr' => $count
                                        ]) }}
                                    </td>
                                @endif
                                <td>
                                    {{ $item->mProduct->name ?? null }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                    <tr>
                        <td colspan="2" class="right">
                            {{ __('labels.a206kyo_s.total', [
                                'attr1' => count($productsRankAGroupedByDistinction),
                                'attr2' => $countProductRankA,
                            ]) }}<br />
                        </td>
                    </tr>
                @else
                    <tr>
                        <th colspan="2" class="bg_white">
                            {{ __('messages.general.No_A_Judgment') }}
                        </th>
                    </tr>
                @endif
            </table>

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="{{ __('labels.a206kyo_s.submit') }}" class="btn_b" /></li>
                <li><input type="button" value="{{ __('labels.a206kyo_s.back') }}" class="btn_a" onclick="window.location = '{{ route('admin.home') }}'"/></li>
            </ul>
        </form>
    </div>
    <!-- /contents inner -->
</div>
<!-- /contents -->
@endsection
@section('footerSection')
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script>
        const Common_E026 = '{{ __('messages.general.Common_E026') }}';
        validation('#form', {
            'comment_refusal': {
                maxlength: 1000,
            },
        }, {
            'comment_refusal': {
                maxlength: Common_E026,
            },
        });

        $('.click_pdf').click(function () {
            const type = $(this).data('type')
            $(`.a_type_${type}`).each(function (idx, item) {
                item.click();
            })
        })
    </script>
    @if($checkIsRefusal)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ROLE_SUPERVISOR] ])
@endsection
