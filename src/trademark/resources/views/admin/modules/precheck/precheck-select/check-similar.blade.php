@extends('admin.layouts.app')
@section('headSection')
    <link rel="stylesheet" href="{{ asset('common/css/simple-modal.css') }}">
    <style>
        .a-hover:hover {
            text-decoration: underline;
            cursor: pointer;
        }
        .jconfirm.jconfirm-white .jconfirm-box .jconfirm-buttons, .jconfirm.jconfirm-light .jconfirm-box .jconfirm-buttons {
            float: none!important;
            text-align:center;
        }
    </style>
@endsection
@section('main-content')
    <!-- contents -->
    <div id="contents">

        <!-- contents inner -->
        <div class="wide clearfix">

            <form id="form" method="POST" action="{{route('admin.precheck_select.create-prrecheck-result-similar')}}">
                @csrf
                <input type="hidden" name="precheck_id" value="{{ $precheckPresentId }}">

                @include('admin.components.includes.messages')
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable,
                ])
                <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->


                <h3>担当者　プレチェックサービス：レポート　類似調査</h3>

                <table class="normal_b product-list">
                    <tr>
                        <th rowspan="2" style="width:8em" class="middle">{{__('labels.precheck.similar_group_code')}}</th>
                        <th rowspan="2" style="width:3em" class="middle">{{__('labels.precheck.distribute')}}</th>
                        <th rowspan="2" style="width:20em" class="middle">{{__('labels.precheck.product_service_name')}}</th>
                        <th colspan="4">{{__('labels.precheck.simple_survey')}}</th>
                        <th colspan="4">{{__('labels.precheck.detailed_investigation')}}</th>
                    </tr>
                    <tr>
                        <th style="width:7em">{{__('labels.precheck.passed')}}</th>
                        <th style="width:7em">{{__('labels.precheck.most_recently_displayed_results')}}</th>
                        <th style="width:7em">{{__('labels.precheck.apply_this_time')}}</th>
                        <th style="width:7em">{{__('labels.precheck.this_time')}}</th>
                        <th style="width:7em">{{__('labels.precheck.passed')}}</th>
                        <th style="width:7em">{{__('labels.precheck.most_recently_displayed_results')}}</th>
                        <th style="width:7em">{{__('labels.precheck.apply_this_time')}}</th>
                        <th style="width:7em">{{__('labels.precheck.this_time')}}</th>
                    </tr>
                    @if(count($datas) > 0)
                    @foreach($datas as $data)
                    @php
                        $totalProduct = count($data['product']);
                        $codeName = $data['codeName'];
                    @endphp
                    <tr>
                        <td
                            @if($totalProduct > 1) rowspan="{{ $totalProduct }}" @endif
                            class="@if($totalProduct == 1) {{ $data['product'][0]->getClassColorByTypeProduct() }} @endif center code_{{ $codeName }}"
                        >{{ $codeName }}</td>

                        @foreach($data['product'] as $key => $item)
                            @php
                                $index = uniqid();
                            @endphp
                            @if($key != 0)
                                <tr>
                            @endif
                                <td class="{{ $item->getClassColorByTypeProduct() }} center">
                                    {{$item->mDistinction->name}}

                                    <input type="hidden" name="result[{{ $index }}][code_id]" value="{{ $item->m_code_id }}">
                                    <input type="hidden" name="result[{{ $index }}][code_name]" value="{{ $codeName }}">
                                    <input type="hidden" name="result[{{ $index }}][precheck_product_id]"
                                           value="{{ $item->precheckProduct[count($item->precheckProduct) -1]->id }}">
                                    <input type="hidden" name="result[{{ $index }}][is_register_product]"
                                           value="{{ $item->precheckProduct[count($item->precheckProduct) -1]->is_register_product }}">
                                </td>
                                <td class="{{ $item->getClassColorByTypeProduct() }}">{{$item->name}}</td>
                                @if(count($item->simple) >= 2 && isset($item->simple[1]))
                                   <td class="{{ $item->getClassColorByTypeProduct() }} center">
                                        {{$item->simple[1]->getResultSimilarSimple()}}
                                        <br/> {{$item->simple[1]->updated_at_convert }}</td>
                                @else
                                   <td class="{{ $item->getClassColorByTypeProduct() }} center">－</td>
                                @endif

                                @if(count($item->simple) >= 1 && isset($item->simple[0]))
                                   <td class="{{ $item->getClassColorByTypeProduct() }} center">
                                       {{$item->simple[0]->getResultSimilarSimple()}}
                                       <br/> {{$item->simple[0]->updated_at_convert }}</td>
                                @else
                                   <td class="{{ $item->getClassColorByTypeProduct() }} center">－</td>
                                @endif

                                <td class="{{ $item->getClassColorByTypeProduct() }} center"></td>
                                <td class="{{ $item->getClassColorByTypeProduct() }} center">－</td>
                                <td class="{{ $item->getClassColorByTypeProduct() }} center">
                                    @if(count($item->detail) >= 2  && isset($item->detail[1]) && !empty($item->detail[1]->result_similar_detail))
                                        {{$item->detail[1]->getResultSimilarDetail()}}
                                        <br>{{$item->detail[1]->updated_at_convert}}
                                    @else
                                         －
                                    @endif
                                </td>
                                <td class="{{ $item->getClassColorByTypeProduct() }} center">
                                    @if(count($item->detail) >= 1  && isset($item->detail[0]) && !empty($item->detail[0]->result_similar_detail))
                                        {{$item->detail[0]->getResultSimilarDetail()}}
                                        <br>{{$item->detail[0]->updated_at_convert}}
                                    @else
                                        －
                                    @endif
                                </td>

                                <td class="{{ $item->getClassColorByTypeProduct() }} center">
                                    @if($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == 1)
                                        ✔
                                    @else

                                    @endif
                                </td>

                                @if($key == 0)
                                    <td class="@if($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == 1) bg_purple @endif center"
                                        @if($totalProduct > 1) rowspan="{{ $totalProduct }}" @endif
                                    >
                                        @if($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == 1)
                                            <select class="result_similar_detail" id="result_similar_detail{{ $item->id }}-{{ $item->m_code_id }}" name="result_similar_detail[{{ $codeName }}]">
                                                <option value="" @if(count($item->detailPresent) >= 1  && isset($item->detailPresent[0]) && $item->detailPresent[0]->result_similar_detail == null) selected @endif></option>
                                                <option value="1" @if(count($item->detailPresent) >= 1  && isset($item->detailPresent[0]) && $item->detailPresent[0]->result_similar_detail == 1) selected @endif>○</option>
                                                <option value="2" @if(count($item->detailPresent) >= 1  && isset($item->detailPresent[0]) && $item->detailPresent[0]->result_similar_detail == 2) selected @endif>△</option>
                                                <option value="3" @if(count($item->detailPresent) >= 1  && isset($item->detailPresent[0]) && $item->detailPresent[0]->result_similar_detail == 3) selected @endif>▲</option>
                                                <option value="4" @if(count($item->detailPresent) >= 1  && isset($item->detailPresent[0]) && $item->detailPresent[0]->result_similar_detail == 4) selected @endif>×</option>
                                            </select>
                                        @else
                                            －
                                        @endif
                                    </td>
                                @endif
                           @if($key != 0)
                              </tr>
                           @endif
                        @endforeach
                    </tr>
                    @endforeach
                     @else
                         <tr><td colspan="11" style="text-align: center">{{__('messages.general.Common_E032')}}</td></tr>
                     @endif
                </table>

                <div class="mb20"></div>

                <p class="eol">
                    {{__('labels.precheck.precheck_select.high_subscription_potential_2')}}<br />
                    {{__('labels.precheck.precheck_select.have_the_expectation_to_sign_up_2')}}<br />
                    {{__('labels.precheck.precheck_select.low_chance_of_registration_2')}}<br />
                    {{__('labels.precheck.precheck_select.difficult_to_register_2')}}</p>

                <h5>【コピー用】</h5>
                <p class="mb10">
                    @foreach($dataRegister as $key => $value)
                        {{$value['code_name']}}
                    @endforeach
                </p>

                    <h3>{{__('labels.precheck.precheck_select.check_unique.title2')}}</h3>

                <table class="normal_b mb40">
                    <tr>
                        <th style="width:3em">{{__('labels.precheck.similar_group_code')}}</th>
                        <th style="width:3em" class="middle">{{__('labels.precheck.distribute')}}</th>
                        <th style="width:40em">{{__('labels.precheck.product_service_name')}}</th>
                    </tr>
                    @if(count($dataNotRegister) > 0)
                    @foreach($dataNotRegister as $key => $item)
                        <tr>
                            <td @if(count($item) > 1)  rowspan="{{count($item)}}" @endif  class="center">{{$key}}</td>
                            @foreach($item as $keyProduct => $product)
                                @if($keyProduct == 0)
                                    <td>{{$product['distinction_name']}}</td>
                                    <td>{{$product['product_name']}}</td>
                        @else
                            <tr>
                                <td>{{$product['distinction_name']}}</td>
                                <td>{{$product['product_name']}}</td>
                            </tr>
                            @endif
                            @endforeach
                            </tr>
                            @endforeach
                            @else
                                <tr><td colspan="3" style="text-align: center">{{__('messages.general.Common_E032')}}</td></tr>
                            @endif
                </table>

                <p class="mb10 white-space-pre-line">{{__('labels.precheck.title_comment_check_unique')}}<br />
                    {!! $precheckCommentUnique ? $precheckCommentUnique->content : '' !!}<br /></p>

                <h5>{{__('labels.precheck.title_comment_check_similar')}}</h5>
                <p class="eol">
                    <textarea class="normal" name="content1" placeholder="{{__('labels.precheck.comment_customers_can_see')}}">{!!$precheckCommentSimilar ? $precheckCommentSimilar->content : ''!!}</textarea>
                </p>

                <h5>{{__('labels.precheck.comment_internal')}}</h5>
                <p class="eol">
                    <textarea class="normal" name="content2" placeholder="{{__('labels.precheck.comment_customers_cannot_see')}}">{!! $precheckCommentInternal ? $precheckCommentInternal->content : ''!!}</textarea>
                </p>
                <p class="eol" style="text-decoration: underline;"><a class="a-hover open-modal">お客様と同じ画面</a></p>

                <ul class="footerBtn clearfix">
                    <li>
                        <a class="btn_custom" href="{{route('admin.precheck.check-precheck-result',
                          [
                            'id' => $id,
                            'precheck_id' => $precheckPresentId
                            ])}}">{{__('labels.back')}}</a>
                    </li>
                    <li>
                        <button type="submit" name="submit" value="{{CREATE}}" class="btn_b"
                                style="font-size: 1.3em;">{{__('labels.save')}}</button>
                    </li>
                    <li>
                        <button type="submit" name="submit" value="{{CONFIRM}}" class="btn_c"
                                style="font-size: 1.3em;">{{__('labels.qa.admin.btn_confirm')}}</button>
                    </li>
                </ul>
            </form>
        </div><!-- /contents inner -->

    </div><!-- /contents -->
@endsection
@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script>
        const errorMessageContentMaxLength = '{{ __('messages.common.errors.Common_E026') }}';
        const errorMessageRequiredResultSimilarDetail = '{{ __('messages.common.errors.Common_E025') }}';
        const openModalUrl = '{{ route('admin.precheck.open-modal') }}';
        const id = @JSON($id);
        let dataIdentification = @JSON($dataPrecheckProductSelects);
        let datas = @JSON($datas);
        let datasSetColorByCodeGroup = @JSON($datas);
        let urlBackToTop = '{{ route('admin.home') }}';
        const precheckPresentId = @JSON($precheckPresentId);
        const CREATE = '{{ CREATE }}';
        const CONFIRM = '{{ CONFIRM }}';
    </script>
    <script src="{{ asset('admin_assets/precheck/check-similar/validate.js') }}"></script>
    <script src="{{ asset('admin_assets/precheck/open_modal.js') }}"></script>
    <script src="{{ asset('admin_assets/precheck/set_color_by_code_group.js') }}"></script>
    <script src="{{ asset('admin_assets/precheck/check-similar/action.js') }}"></script>
    @if($precheckPresent->flag_role == FLAG_ROLE)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ ROLE_MANAGER ] ])
@endsection
