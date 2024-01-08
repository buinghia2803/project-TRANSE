@extends('admin.layouts.app')
@section('headSection')
    <style>
        .a-hover:hover {
            text-decoration: underline;
            cursor: pointer;
        }

        .btn_confirm {
            background: #359ce0 !important;
            color: #ffffff !important;
        }

        .btn_confirm:hover {
            background: #ceddeb !important;
            border: 1px solid #aaaaaa !important;
            color: #000000 !important;
        }
    </style>
@endsection
@section('main-content')
        <!-- contents -->
        <div id="contents">

            <!-- contents inner -->
            <div class="wide clearfix">

                <form id="form" method="POST" action="{{route('admin.precheck_select.create-precheck-result-check-unique')}}">
                    @csrf
                    @include('admin.components.includes.messages')
                    @include('admin.components.includes.trademark-table', [
                        'table' => $trademarkTable
                    ])
                    <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->

                    <input type="hidden" name="precheck_id" value="{{$precheckPresent}}">
                    <h3>{{__('labels.precheck.precheck_select.check_unique.title1')}}</h3>

                    <table class="normal_b product-list">
                        <tr>
                            <th rowspan="2" style="width:3em;">{{__('labels.precheck.distribute')}}</th>
                            <th rowspan="2" style="width:20em;">{{__('labels.precheck.product_service_name')}}</th>
                            <th rowspan="2" style="width:20em;">{{__('labels.precheck.similar_group_code')}}</th>
                            <th colspan="3">{{__('labels.precheck.discrimination_survey')}}</th>
                        </tr>
                        <tr>
                            <th style="width:8em">{{__('labels.precheck.passed')}}</th>
                            <th style="width:8em">{{__('labels.precheck.apply_this_time')}}</th>
                            <th style="width:8em">{{__('labels.precheck.this_time')}}</th>
                        </tr>
                        <input type="hidden" name="trademark_id" value="{{$id}}">
                        @if(count($datas) > 0)
                        @foreach($datas as $data)
                        <tr>
                            <td @if(count($data['product']) >= 1) rowspan="{{count($data['product'])}}" @endif class="@if(count($data['product']) == 1) {{$data['product'][0]->getClassColorByTypeProduct()}} @endif center distrinction_{{$data['codeDistriction']}}">{{$data['codeDistriction']}}</td>
                            @foreach($data['product'] as $key => $item)
                                @if($key != 0)
                                    <tr>
                                @endif
                                <td class="{{ $item->getClassColorByTypeProduct() }}">{{$item->name}}</td>
                                <td class="{{ $item->getClassColorByTypeProduct() }}">
                                    <span class="text-product-code_{{$item->id}} w-100">
                                        @if(isset($item->code) && count($item->code) >= 1)
                                            {{$item->code[0]->name}}
                                        @endif
                                        @if(isset($item->code) && count($item->code) >= 2)
                                            {{$item->code[1]->name}}
                                        @endif
                                        @if(isset($item->code) && count($item->code) >= 3)
                                            {{$item->code[2]->name}}
                                        @endif
                                     </span>
                                    @if(count($item->code) > 3)
                                        <span class="icon-add-sub add_{{$item->id}}"  style="cursor: pointer">+</span><br />
                                    @endif
                                    <input type="hidden" name="count_code" value="{{count($item->code)}}">
                                </td>

                                <td class="{{ $item->getClassColorByTypeProduct() }} center">
                                    @if(count($item->detail) >= 2 && isset($item->detail[1]) && !empty($item->detail[1]->result_identification_detail))
                                        {{$item->detail[1]->getResultIdentificationDetail()}}
                                        <br>{{$item->detail[1]->updated_at_convert}}
                                    @elseif(count($item->detail) >= 1 && isset($item->detail[0]) && !empty($item->detail[0]->result_identification_detail))
                                        {{$item->detail[0]->getResultIdentificationDetail()}}
                                        <br>{{$item->detail[0]->updated_at_convert}}
                                    @else
                                        －
                                    @endif
                                </td>
                                <td class="{{ $item->getClassColorByTypeProduct() }} center">
                                    @if($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == 1)
                                        ✔
                                    @else
                                        －
                                    @endif
                                </td>
                                <td class="@if($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == 1) bg_purple @endif center">
                                    @if($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == 1)
                                        <select name="result_identification_detail[]">
                                            <option @if(count($item->detail) >= 1  && isset($item->detail[0]) && $item->detail[0]->result_identification_detail == null) selected @endif></option>
                                            <option value="1" @if(count($item->detailPresent) >= 1  && isset($item->detailPresent[0]) && $item->detailPresent[0]->result_identification_detail == 1) selected @endif>○</option>
                                            <option value="2" @if(count($item->detailPresent) >= 1  && isset($item->detailPresent[0]) && $item->detailPresent[0]->result_identification_detail == 2) selected @endif>△</option>
                                            <option value="3" @if(count($item->detailPresent) >= 1  && isset($item->detailPresent[0]) && $item->detailPresent[0]->result_identification_detail == 3) selected @endif>▲</option>
                                            <option value="4" @if(count($item->detailPresent) >= 1  && isset($item->detailPresent[0]) && $item->detailPresent[0]->result_identification_detail == 4) selected @endif>×</option>
                                        </select>
                                        @foreach($item->code as $code)
                                            <input type="hidden" name="m_code_id[{{$item->precheckProduct[count($item->precheckProduct) -1]->id}}][]" value="{{$code->id}}">
                                        @endforeach
                                        <input type="hidden" name="precheck_product_id[]" value="{{$item->precheckProduct[count($item->precheckProduct) -1]->id}}">
                                        <input type="hidden" name="is_register_product[]" value="{{$item->precheckProduct[count($item->precheckProduct) -1]->is_register_product}}">
                                    @else
                                        －
                                    @endif
                                </td>
                                @if($key != 0)
                                     </tr>
                                @endif
                             @endforeach
                        </tr>
                        @endforeach
                        @else
                              <tr><td colspan="6" style="text-align: center">{{__('messages.general.Common_E032')}}</td></tr>
                        @endif
                    </table>

                    <div class="mb20"></div>

                    <p class="eol">
                        {{__('labels.precheck.precheck_select.high_subscription_potential')}}<br />
                        {{__('labels.precheck.precheck_select.have_the_expectation_to_sign_up')}}<br />
                        {{__('labels.precheck.precheck_select.low_chance_of_registration')}}<br />
                        {{__('labels.precheck.precheck_select.difficult_to_register')}}
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

                    <p>{{__('labels.precheck.title_comment_check_unique')}}:<br />
                        <textarea class="normal" name="content1" placeholder="{{__('labels.precheck.comment_customers_can_see')}}">{{$precheckCommentCheckUnique ? $precheckCommentCheckUnique->content : ''}}</textarea>
                    </p>

                    <h5>{{__('labels.precheck.title_comment_check_similar')}}</h5>
                    <p class="mb10 white-space-pre-line">{!! $precheckCommentCheckSimilar ? $precheckCommentCheckSimilar->content : '' !!}<br/><br/></p>

                    <h5>{{__('labels.precheck.comment_internal')}}</h5>
                    <p class="eol">
                        <textarea class="normal" name="content2"  placeholder="{{__('labels.precheck.comment_customers_cannot_see')}}">{{$precheckCommentInternal ? $precheckCommentInternal->content : ''}}</textarea>
                    </p>

                    <ul class="footerBtn clearfix">
                        <li><a class="btn_custom" style="font-size: 1.3em; height: 38px" href="{{route('admin.home')}}">{{__('labels.back')}}</a></li>
                        <li>
                            <button type="submit" name="submit" value="{{SAVE}}" class="btn_b save_unique"
                                    style="font-size: 1.3em;">{{__('labels.precheck.save_check_unique')}}</button>
                        </li>
                        <li>
                            <button type="submit" name="submit" value="{{CONFIRM}}" class="btn_b confirm_check_unique"
                                    style="font-size: 1.3em;">{{__('labels.precheck.confirm_check_unique')}}</button>
                        </li>
                        @if ($precheck->flag_role == FLAG_ROLE)
                            <li><a class="btn_custom btn_confirm" style="font-size: 1.3em; height: 38px" href="{{route('admin.precheck.check-similar', ['id' => $id, 'precheck_id' => $precheck->id])}}">{{__('labels.precheck.confirm_check_unique')}}</a></li>
                        @endif
                    </ul>

                </form>

            </div><!-- /contents inner -->

        </div><!-- /contents -->

@endsection
@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script>
        const errorMessageContentMaxLength = '{{ __('messages.common.errors.Common_E026') }}';
        const errorMessageRequiredResultIdentificationDetail = '{{ __('messages.common.errors.Common_E025') }}';
        const envAppUrl = "<?php echo env('APP_URL'); ?>";
        let datasSetColorByDistrictionGroup = @JSON($datas);
        let datas = @JSON($datas);
        let countCode = $("input[name='count_code']").val();
        let SAVE = '{{ SAVE }}';
        let CONFIRM = '{{ CONFIRM }}';
    </script>
    <script src="{{ asset('admin_assets/precheck/check-unique/validate.js') }}"></script>
    <script src="{{ asset('admin_assets/precheck/check-unique/action.js') }}"></script>
    <script src="{{ asset('admin_assets/precheck/set_color_by_distriction_group.js') }}"></script>
    @if($precheck->flag_role == FLAG_ROLE)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ ROLE_MANAGER ] ])
@endsection
