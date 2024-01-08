<div class="wide clearfix">
    <form>
        <div class="highlight clearfix pt20 mb20">
            <div class="table_block col-lg-6 p-2 m-0">
                <table class="normal_a w-100">
                    <caption>{{ __('labels.modal_admin.caption') }}</caption>
                    <tr>
                        <th rowspan="2" class="em04">{{ __('labels.modal_admin.type') }}</th>
                        <th rowspan="2" class="bg_green">{{ __('labels.modal_admin.product_name') }}</th>
                        <th>{{ __('labels.modal_admin.simple') }}</th>
                        <th colspan="3">{{ __('labels.modal_admin.detailed') }}</th>
                    </tr>
                    <tr>
                        <th class="em04 bg_whitesmoke">{{ __('labels.modal_admin.identical') }}</th>
                        <th class="em07">{{ __('labels.modal_admin.discernment') }}</th>
                        <th class="em07">{{ __('labels.modal_admin.same_similar') }}</th>
                        <th class="em05 bg_whitesmoke">{{ __('labels.modal_admin.result') }}</th>
                    </tr>
                    @if(count($dataResult) > 0)
                        @foreach ($dataResult as $key => $data)
                            <tr>
                                <td class="bg_blue inv_blue" @if(count($data['product']) > 1) rowspan="{{count($data['product'])}}" @endif style="width: 70px">
                                    {{__('labels.support_first_times.No')}}
                                    {{ $data['codeDistriction'] }}
                                    {{__('labels.support_first_times.kind')}}
                                </td>
                                @foreach ($data['product'] as $keyProduct => $item)
                                    @if ($keyProduct != 0)
                                        <tr>
                                    @endif
                                    <td class="bg_green">{{ $item['name'] ?? '' }}</td>
                                    <td class="bg_green center">
                                        @if(isset($item['simple']) && count($item['simple']) >= 1 && !empty($item['simple'][0]))
                                            @if ($item['simple'][0]['result_similar_simple'] == 1)
                                                {{ __('labels.modal_admin.result_similar_simple_1') }}
                                            @elseif($item['simple'][0]['result_similar_simple']== 2)
                                                {{ __('labels.modal_admin.result_similar_simple_2') }}
                                            @else
                                                {{ __('labels.modal_admin.default') }}
                                            @endif
                                        @else
                                            －
                                        @endif
                                    </td>
                                    <td class="center bg_gray">
                                        @if(isset($item['detailPresent'][0]) && count($item['detailPresent']) >= 1  && isset($item['detailPresent'][0]->result_identification_detail))
                                            @if ($item['detailPresent'][0]->result_identification_detail == LIKELY_TO_BE_REGISTERED)
                                                {{ __('labels.modal_admin.result_detail_1') }}
                                            @elseif($item['detailPresent'][0]->result_identification_detail == LOOK_FORWARD_TO_REGISTERING)
                                                {{ __('labels.modal_admin.result_detail_2') }}
                                            @elseif($item['detailPresent'][0]->result_identification_detail == LESS_LIKELY_TO_BE_REGISTERED)
                                                {{ __('labels.modal_admin.result_detail_3') }}
                                            @elseif($item['detailPresent'][0]->result_identification_detail == DIFFICULT_TO_REGISTER)
                                                {{ __('labels.modal_admin.result_detail_4') }}
                                            @else
                                                －
                                            @endif
                                        @else
                                             －
                                        @endif
                                    </td>
                                    <td class="center bg_gray">
                                        @if(isset($item['detailPresent'][0]) && count($item['detailPresent']) >= 1  && isset($item['detailPresent'][0]->result_similar_detail))
                                            @if ($item['detailPresent'][0]->result_similar_detail ==  LIKELY_TO_BE_REGISTERED)
                                                {{ __('labels.modal_admin.result_detail_1') }}
                                            @elseif($item['detailPresent'][0]->result_similar_detail ==  LOOK_FORWARD_TO_REGISTERING)
                                                {{ __('labels.modal_admin.result_detail_2') }}
                                            @elseif($item['detailPresent'][0]->result_similar_detail ==  LESS_LIKELY_TO_BE_REGISTERED)
                                                {{ __('labels.modal_admin.result_detail_3') }}
                                            @elseif($item['detailPresent'][0]->result_similar_detail ==  DIFFICULT_TO_REGISTER)
                                                {{ __('labels.modal_admin.result_detail_4') }}
                                            @else
                                                －
                                            @endif
                                        @else
                                            －
                                        @endif
                                    </td>
                                    <td class="center">
                                       @if(isset($item['detailPresent']) && count($item['detailPresent']) >= 1  && isset($item['detailPresent'][0]->result_final))
                                            @if ($item['detailPresent'][0]->result_final == LIKELY_TO_BE_REGISTERED)
                                                {{ __('labels.modal_admin.result_A') }}
                                            @elseif ($item['detailPresent'][0]->result_final == LOOK_FORWARD_TO_REGISTERING)
                                                {{ __('labels.modal_admin.result_B') }}
                                            @elseif ($item['detailPresent'][0]->result_final == LESS_LIKELY_TO_BE_REGISTERED)
                                                {{ __('labels.modal_admin.result_C') }}
                                            @elseif ($item['detailPresent'][0]->result_final == DIFFICULT_TO_REGISTER)
                                                  {{ __('labels.modal_admin.result_D') }}
                                            @else
                                            －
                                            @endif
                                        @else
                                        －
                                        @endif
                                    </td>

                                    @if($keyProduct != 0)
                                        </tr>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    @else
                        <tr><td colspan="6" style="text-align: center">{{__('messages.general.Common_E032')}}</td></tr>
                    @endif
                </table>
            </div>

            <div class="table_block col-lg-6 p-2 m-0">
                <table class="normal_a w-10">
                    <caption>{{ __('labels.modal_admin.caption1') }}</caption>
                    <tr>
                        <th rowspan="2" class="em04">{{ __('labels.modal_admin.type') }}</th>
                        <th rowspan="2" class="bg_green">{{ __('labels.modal_admin.product_name') }}</th>
                        <th>{{ __('labels.modal_admin.simple') }}</th>
                        <th colspan="3">{{ __('labels.modal_admin.detailed') }}</th>
                    </tr>
                    <tr>
                        <th class="em04 bg_whitesmoke">{{ __('labels.modal_admin.identical') }}</th>
                        <th class="em07" style="width: 100px;">{{ __('labels.modal_admin.discernment') }}</th>
                        <th class="em07">{{ __('labels.modal_admin.same_similar') }}</th>
                        <th class="em05 bg_whitesmoke">{{ __('labels.modal_admin.result') }}</th>
                    </tr>
                    @if(count($dataResultPerious) > 0)
                        @foreach ($dataResultPerious as $key => $data)
                        <tr>
                            <td class="bg_blue inv_blue" @if(count($data['product']) > 1) rowspan="{{count($data['product'])}}" @endif style="width: 70px">
                                {{__('labels.support_first_times.No')}}
                                {{ $data['codeDistriction'] }}
                                {{__('labels.support_first_times.kind')}}
                            </td>
                            @foreach ($data['product'] as $keyProduct => $item)
                                @if ($keyProduct != 0)
                                    <tr>
                                @endif
                                <td class="bg_green">{{ $item->name ?? '' }}</td>
                                <td class="bg_green center">
                                    @if(isset($item->simple[0]))
                                        @if ($item->simple[0]->result_similar_simple == 1)
                                            {{ __('labels.modal_admin.result_similar_simple_1') }}
                                        @elseif($item->simple[0]->result_similar_simple == 2)
                                            {{ __('labels.modal_admin.result_similar_simple_2') }}
                                        @else
                                            {{ __('labels.modal_admin.default') }}
                                        @endif
                                    @elseif(isset($item->simple[1]))
                                        @if ($item->simple[1]->result_similar_simple == 1)
                                            {{ __('labels.modal_admin.result_similar_simple_1') }}
                                        @elseif($item->simple[1]->result_similar_simple == 2)
                                            {{ __('labels.modal_admin.result_similar_simple_2') }}
                                        @else
                                            {{ __('labels.modal_admin.default') }}
                                        @endif
                                    @else
                                        －
                                    @endif
                                </td>
                                <td class="center bg_gray">
                                    @if(isset($item->detail[0]))
                                        {{$item->detail[0]->getResultIdentificationDetail()}}
                                    @elseif(isset($item->detail[1]))
                                        {{$item->detail[1]->getResultIdentificationDetail()}}
                                    @else
                                         －
                                    @endif
                                </td>
                                <td class="center bg_gray">
                                    @if(isset($item->detail[0]))
                                        {{$item->detail[0]->getResultSimilarDetail()}}
                                    @elseif(isset($item->detail[1]))
                                        {{$item->detail[1]->getResultSimilarDetail()}}
                                    @else
                                         －
                                    @endif
                                </td>
                                <td class="center">
                                    @php
                                    if(count($item->detail) >= 1 && isset($item->detail[0])) {
                                        $result =$item->detail[0]->result_identification_detail;
                                        if ($result < $item->detail[0]->result_identification_detail) {
                                            $result = $item->detail[0]->result_identification_detail;
                                        } else {
                                            $result = '－';
                                        }
                                    }else {
                                        $result = '－';
                                    }
                                @endphp
                                    @if(isset($item->detail[0]))
                                        @if ($item->detail[0]->result_final == LIKELY_TO_BE_REGISTERED)
                                            {{ __('labels.modal_admin.result_A') }}
                                        @elseif ($item->detail[0]->result_final == LOOK_FORWARD_TO_REGISTERING)
                                            {{ __('labels.modal_admin.result_B') }}
                                        @elseif ($item->detail[0]->result_final == LESS_LIKELY_TO_BE_REGISTERED)
                                            {{ __('labels.modal_admin.result_C') }}
                                        @elseif ($item->detail[0]->result_final == DIFFICULT_TO_REGISTER)
                                            {{ __('labels.modal_admin.result_D') }}
                                        @else
                                            －
                                        @endif
                                    @elseif(isset($item->detail[1]))
                                        @if ($item->detail[1]->result_final == LIKELY_TO_BE_REGISTERED)
                                            {{ __('labels.modal_admin.result_A') }}
                                        @elseif ($item->detail[1]->result_final == LOOK_FORWARD_TO_REGISTERING)
                                            {{ __('labels.modal_admin.result_B') }}
                                        @elseif ($item->detail[1]->result_final == LESS_LIKELY_TO_BE_REGISTERED)
                                            {{ __('labels.modal_admin.result_C') }}
                                        @elseif ($item->detail[1]->result_final == DIFFICULT_TO_REGISTER)
                                            {{ __('labels.modal_admin.result_D') }}
                                        @else
                                            －
                                        @endif
                                    @else
                                         －
                                    @endif
                                </td>

                                @if($keyProduct != 0)
                                    </tr>
                                @endif
                            @endforeach
                        </tr>
                        @endforeach
                    @else
                        <tr><td colspan="6" style="text-align: center">{{__('messages.general.Common_E032')}}</td></tr>
                    @endif
                </table>
            </div>
        </div>
    </form>
</div>



