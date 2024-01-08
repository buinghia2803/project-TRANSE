@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">

        <!-- contents inner -->
        <div class="wide clearfix">

            <form id="form" method="POST" action="{{route('admin.precheck_select.edit-precheck-unique')}}">
                @csrf
                @include('admin.components.includes.messages')
                @include('admin.components.includes.trademark-table', [
                     'table' => $trademarkTable
                ])
                <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->


                <h3>{{__('labels.precheck.precheck_select.check_unique.title3')}}</h3>

                <table class="normal_b column1">
                    <tr>
                        <th>{{__('labels.precheck.distribute')}}</th>
                        <th style="width:20em;">{{__('labels.precheck.product_service_name')}}</th>
                        <th>{{__('labels.precheck.similar_group_code')}}</th>
                        <th>
                            {{__('labels.precheck.precheck_select.edit_all_unique')}}
                            <br />
                            <button type="button" class="btn_a copy_undisabled_all" >一括修正</button>
                            <button type="button" class="btn_b edit_copy_all" >一括決定</button>
                        </th>
                        <th>{{__('labels.precheck.precheck_select.decision_all_unique')}}
                            <br />
{{--                            <input type="button" value="一括決定" class="btn_b confirm_copy_all" />--}}
                            <button type="button" class="btn_b confirm_copy_all" >一括決定</button>

                        </th>
                        <th>{{__('labels.precheck.precheck_select.confirm')}}</th>
                        <th>{{__('labels.precheck.precheck_select.confirm_all')}} <input class="checkbox_all" type="checkbox"></th>
                    </tr>
                    <input type="hidden" name="precheck_id" value="{{$precheckPresent->id}}">
                    <input type="hidden" name="trademark_id" value="{{$id}}">
                    @if(count($datas) > 0)
                    @foreach($datas as $data)
                     <tr>
                         <td @if(count($data['product']) > 1)  rowspan="{{count($data['product'])}}" @endif class="@if(count($data['product']) == 1) {{$data['product'][0]->getClassColorByTypeProduct()}} @endif center">{{$data['codeDistriction']}}</td>
                         @foreach($data['product'] as $key => $item)
                            @if($key != 0)
                                <tr>
                            @endif
                                 <td class="{{$item->getClassColorByTypeProduct()}}">{{$item->name}}</td>
                                 <td class="{{$item->getClassColorByTypeProduct()}}" width="300px">
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
                                @if($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == IS_REGISTER_PRODUCT)
                                     <td class="center  @if($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == IS_REGISTER_PRODUCT) bg_purple @endif">
                                         @if(count($item->detailPresent) >= 1 && isset($item->detailPresent[0]) && !empty($item->detailPresent[0]->result_identification_detail) && $item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == IS_REGISTER_PRODUCT)
                                         {{$item->detailPresent[0]->getResultIdentificationDetail()}}
                                         <button type="button" value="" class="btn_a copy_undisabled copy_undisabled_{{$item->id}}_{{$item->m_distinction_id}}"
                                         @if($precheckPresent->is_confirm == 1) disabled @endif>{{__('labels.support_first_times.fix')}}</button>
                                         <button type="button" value="" class="btn_b edit_copy edit_copy_{{$item->id}}_{{$item->m_distinction_id}}" >{{__('labels.decision')}}</button>
                                         @else
                                             －
                                         @endif
                                     </td>
                                 @else
                                     <td class="center {{$item->getClassColorByTypeProduct()}}">－</td>
                                 @endif
                                    <td class="center @if($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == IS_REGISTER_PRODUCT) bg_purple @endif select_button_edit">
                                        @if(count($item->detailPresent) >= 1 && isset($item->detailPresent[0]) && !empty($item->detailPresent[0]->result_identification_detail) && $item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == IS_REGISTER_PRODUCT)
                                         <select class="select_button_edit_copy select_button_edit_copy_{{$item->id}}_{{$item->m_distinction_id}}" disabled>
                                             <option value="{{VALUE_0}}" @if(isset($item->result_identification_detail_edit) && $item->result_identification_detail_edit == 0) selected @endif></option>
                                             <option value="{{VALUE_1}}" @if(isset($item->result_identification_detail_edit) && $item->result_identification_detail_edit == LIKELY_TO_BE_REGISTERED) selected @endif>○</option>
                                             <option value="{{VALUE_2}}"  @if(isset($item->result_identification_detail_edit) && $item->result_identification_detail_edit == LOOK_FORWARD_TO_REGISTERING) selected @endif>△</option>
                                             <option value="{{VALUE_3}}"  @if(isset($item->result_identification_detail_edit) && $item->result_identification_detail_edit == LESS_LIKELY_TO_BE_REGISTERED) selected @endif>▲</option>
                                             <option value="{{VALUE_4}}"  @if(isset($item->result_identification_detail_edit) && $item->result_identification_detail_edit == DIFFICULT_TO_REGISTER) selected @endif>×</option>
                                         </select>
                                         <input type="hidden" name="result_identification_detail_edit[]" class="result_identification_detail_edit result_identification_detail_edit_{{$item->id}}_{{$item->m_distinction_id}}" value="{{$item->result_identification_detail_edit}}">
                                         <button type="button" class="btn_b confirm_copy confirm_copy_{{$item->id}}_{{$item->m_distinction_id}}" >{{__('labels.decision')}}</button>
                                         @else
                                             －
                                         @endif
                                     </td>
                                     <td class="result_update center {{$item->getClassColorByTypeProduct()}}">
                                         <span class="result_update_data result_update_{{$item->id}}_{{$item->m_distinction_id}}">
                                             @if($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == IS_REGISTER_PRODUCT)
                                                 @if(isset($item->result_identification_detail_final) && $item->is_decision_edit != 0 && $item->is_decision_edit != 0)
                                                     {{$iconPrecheck[$item->result_identification_detail_final]}}
                                                 @else

                                                 @endif

                                                 @if(count($item->detailPresent) >= 1 && isset($item->detailPresent[0]) && isset($item->is_decision_draft) && $item->is_decision_draft != 0)
                                                     {{$iconPrecheck[$item->detailPresent[0]->result_identification_detail]}}
                                                 @else

                                                 @endif
                                             @else
                                                 －
                                             @endif
                                         </span>
                                         @if($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == IS_REGISTER_PRODUCT)
                                             <input type="hidden" name="result_identification_detail_present[]" class="result_identification_detail_present_{{$item->id}}_{{$item->m_distinction_id}}" value="{{(isset($item->detailPresent) && isset($item->detailPresent[0])) ? $item->detailPresent[0]->result_identification_detail : ''}}">
                                             <input type="hidden" name="result_edit[]" class="result_edit_{{$item->id}}_{{$item->m_distinction_id}}" value="{{isset($item->result_identification_detail_edit) ? $item->result_identification_detail_edit : ''}}">
                                             <input type="hidden" name="precheck_product_id[]" value="{{$item->precheckProduct[count($item->precheckProduct) -1]->id}}">
                                             <input type="hidden" name="is_register_product[]" value="{{$item->precheckProduct[count($item->precheckProduct) -1]->is_register_product}}">
                                             <input type="hidden" name="m_product_id[]" value="{{$item->id}}">
                                             <input type="hidden" name="is_decision_draft[]" class="is_decision_draft" id="is_decision_draft_{{$item->id}}_{{$item->m_distinction_id}}">
                                             <input type="hidden" name="is_decision_edit[]" id="is_decision_edit_{{$item->id}}_{{$item->m_distinction_id}}">
                                             @foreach($item->code as $code)
                                                 <input type="hidden" name="m_code_id[{{$item->id}}][]" value="{{$code->id}}">
                                             @endforeach
                                         @endif
                                     </td>
                                     <td class="center {{$item->getClassColorByTypeProduct()}} checkbox_parent">
                                        @if($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == IS_REGISTER_PRODUCT)
                                            <input class="checkbox_{{$item->id}}_{{$item->m_distinction_id}}" type="checkbox" name="check_lock[{{ $item->id }}]" data-foo="check_lock[]" @if($item->is_block_identification == 1) checked @endif>
                                             <span class="text-checked">{{__('labels.precheck.precheck_select.confirm_all')}}</span>
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
                       <tr><td colspan="7" style="text-align: center">{{__('messages.general.Common_E032')}}</td></tr>
                    @endif
                </table>
                <!-- /table 区分、商品・サービス名 1 -->

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
                <p class="eol" style="display: flex">
                    <span>社内用コメント：{{$precheckCommentInternalOfTantou ? $precheckCommentInternalOfTantou->updated_at_convert : '' }}</span>
                    <span style="white-space: pre"> {!! $precheckCommentInternalOfTantou ? $precheckCommentInternalOfTantou->content : '' !!}</span>
                </p>

                <p>{{__('labels.precheck.title_comment_check_unique')}}:<br />
                    <textarea class="normal" name="content1" placeholder="{{__('labels.precheck.comment_customers_can_see')}}">{{($precheckKeepData && !empty($precheckKeepData->comment_from_ams_identification)) ? $precheckKeepData->comment_from_ams_identification :  ($precheckCommentUnique ? $precheckCommentUnique->content : '')}}</textarea>
                </p>
                </p>

                <h5>{{__('labels.precheck.title_comment_check_similar')}}</h5>
                <p class="mb10">{{ $precheckKeepData ? $precheckKeepData->comment_from_ams_similar : ($precheckCommentSimilar ? $precheckCommentSimilar->content : '')}}<br /><br /></p>

                <h5>{{__('labels.precheck.comment_internal')}}</h5>
                <p class="eol">
                    <textarea class="normal" name="content2"  placeholder="{{__('labels.precheck.comment_customers_cannot_see')}}">{{($precheckKeepData && !empty($precheckKeepData->comment_internal)) ? $precheckKeepData->comment_internal : ''}}</textarea></p>

                <ul class="footerBtn clearfix">
                    <li><button type="submit" name="submit" class="btn_a"  value="{{ FROM_A021SHIKISHU_TO_A021S }}" style="font-size: 1.3em;">{{__('labels.back')}}</button></li>
                    <li>
                        <button type="submit" name="submit" value="{{ CREATE }}" class="btn_b"
                                style="font-size: 1.3em;">{{__('labels.save')}}</button>
                    </li>
                    <li>
                        <button type="submit" name="submit" value="{{ CONFIRM }}" class="btn_b confirm"
                                style="font-size: 1.3em;">{{__('labels.precheck.confirm_check_unique')}}</button>
                    </li>
                </ul>


            </form>

        </div><!-- /contents inner -->

    </div><!-- /contents -->


    </div><!-- /wrapper -->

@endsection
@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script>
        const errorMessageContentMaxLength = '{{ __('messages.common.errors.Common_E026') }}';
        const errorMessageRequiredResultSimilarSimple = '{{ __('messages.common.errors.Common_E025') }}';
        let data = @JSON($datas);
        let precheckPresent = @json($precheckPresent);
        const isConfirmTrue = '{{IS_CONFIRM_TRUE}}';
        let errorE0025 = '{{__('messages.common.errors.Common_E025')}}';
        let errorE001 = '{{__('messages.common.errors.Common_E001')}}';
    </script>
    <script src="{{ asset('admin_assets/precheck/edit-check-unique/validate.js') }}"></script>
    <script src="{{ asset('admin_assets/precheck/edit-check-unique/action.js') }}"></script>
    @if($precheckPresent->is_confirm == IS_CONFIRM_TRUE)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ ROLE_SUPERVISOR ] ])
@endsection
