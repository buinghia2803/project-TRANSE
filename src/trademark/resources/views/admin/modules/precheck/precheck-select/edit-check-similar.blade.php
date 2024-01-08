@extends('admin.layouts.app')
@section('headSection')
    <link rel="stylesheet" href="{{ asset('common/css/simple-modal.css') }}">
    <style>
        .table_block {
            float: left;
            margin-right: 10px;
            margin-bottom: 10px;
        }
        .a-hover:hover {
            text-decoration: underline;
            cursor: pointer;
        }
    </style>
@endsection
@section('main-content')
    <!-- contents -->
    <div id="contents">

        <!-- contents inner -->
        <div class="wide clearfix">

            <form id="form" method="POST" action="{{route('admin.precheck_select.edit-precheck-similar')}}">
                @csrf
                @include('admin.components.includes.messages')
                @include('admin.components.includes.trademark-table', [
                         'table' => $trademarkTable
                     ])
                <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->


                <h3>{{__('labels.precheck.precheck_select.check_unique.title4')}}</h3>

                    <table class="normal_b mb20">
                        <tr>
                            <th rowspan="2" style="width:8em"
                                class="middle">{{__('labels.precheck.similar_group_code')}}</th>
                            <th rowspan="2" style="width:3em" class="middle">{{__('labels.precheck.distribute')}}</th>
                            <th rowspan="2" style="width:20em"
                                class="middle">{{__('labels.precheck.product_service_name')}}</th>
                            <th colspan="4">{{__('labels.precheck.simple_survey')}}</th>
                            <th colspan="7">{{__('labels.precheck.detailed_investigation')}}</th>
                        </tr>
                        <tr>
                            <th style="width:7em">{{__('labels.precheck.passed')}}</th>
                            <th style="width:7em">{{__('labels.precheck.most_recently_displayed_results')}}</th>
                            <th style="width:7em">{{__('labels.precheck.apply_this_time')}}</th>
                            <th style="width:7em">{{__('labels.precheck.this_time')}}</th>
                            <th style="width:7em">{{__('labels.precheck.passed')}}</th>
                            <th style="width:7em">{{__('labels.precheck.most_recently_displayed_results')}}</th>
                            <th style="width:7em">{{__('labels.precheck.apply_this_time')}}</th>
                            <th>
                                {{__('labels.precheck.precheck_select.edit_all_similar')}}
                                <br/>
                                <button type="button" class="btn_a copy_undisabled_all">一括修正</button>
                                <button type="button" class="btn_b edit_copy_all">一括決定</button>
                            </th>
                            <th>修正：類似調査<br/><button type="button" value="" class="btn_b confirm_copy_all">一括決定</button></th>
                            <th>決定：類似調査</th>
                            <th>一括確認＆ロック <input type="checkbox" class="checkbox_all"></th>
                        </tr>
                        @if(count($datas) > 0)
                            @foreach($datas as $key => $data)
                                @php
                                    $totalProduct = count($data['product']);
                                    $codeName = $data['codeName'];
                                @endphp
                                <tr>
                                    <td @if(count($data['product']) > 1) rowspan="{{count($data['product'])}}"
                                        @endif class=""
                                        @if(count($data['product']) == 1) {{$data['product'][0]->getClassColorByTypeProduct()}} @endif center
                                    ">{{ $codeName }}</td>

                                    @foreach($data['product'] as $keyProduct => $item)
                                        @php $index = uniqid(); @endphp
                                        @if($keyProduct != 0) <tr> @endif
                                            <td class="center {{$item->getClassColorByTypeProduct()}}">
                                                {{$item->mDistinction->name}}

                                                <input type="hidden" name="result[{{ $index }}][code_id]" value="{{ $item->m_code_id }}">
                                                <input type="hidden" name="result[{{ $index }}][code_name]" value="{{ $codeName }}">
                                                <input type="hidden" name="result[{{ $index }}][m_product_id]" value="{{ $item->id }}">
                                                <input type="hidden" name="result[{{ $index }}][is_register_product]"
                                                       value="{{$item->precheckProduct[count($item->precheckProduct) -1]->is_register_product}}">
                                                <input type="hidden" name="result[{{ $index }}][precheck_product_id]"
                                                       value="{{$item->precheckProduct[count($item->precheckProduct) -1]->id}}">
                                            </td>
                                            <td class="{{$item->getClassColorByTypeProduct()}}">{{$item->name}}</td>

                                            @if(count($item->simple) >= 2 && isset($item->simple[1]) && !empty($item->simple[1]->result_similar_simple))
                                                <td class="{{$item->getClassColorByTypeProduct()}} center">
                                                    {{$item->simple[1]->getResultSimilarSimple()}}
                                                    <br/> {{$item->simple[1]->updated_at_convert }}
                                                </td>
                                            @else
                                                <td class="{{$item->getClassColorByTypeProduct()}} center"> －</td>
                                            @endif

                                            @if(count($item->simple) >= 1 && isset($item->simple[0]) && !empty($item->simple[0]->result_similar_simple))
                                                <td class="{{$item->getClassColorByTypeProduct()}} center">
                                                    {{$item->simple[0]->getResultSimilarSimple()}}
                                                    <br/> {{$item->simple[0]->updated_at_convert }}</td>
                                            @else
                                                <td class="{{$item->getClassColorByTypeProduct()}} center"> －</td>
                                            @endif

                                            <td class="center {{$item->getClassColorByTypeProduct()}}">－</td>
                                            <td class="center {{$item->getClassColorByTypeProduct()}}">－</td>

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

                                            <td class="center {{$item->getClassColorByTypeProduct()}}">
                                                @if($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == 1)
                                                    ✔
                                                @else
                                                    －
                                                @endif
                                            </td>

                                            @if($keyProduct == 0)
                                                <td class="center @if($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == IS_REGISTER_PRODUCT) bg_purple @endif"
                                                    @if($totalProduct > 1) rowspan="{{ $totalProduct }}" @endif>
                                                    @if(count($item->detailPresent) >= 1 && isset($item->detailPresent[0]) && $item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == IS_REGISTER_PRODUCT)
                                                        {{$item->detailPresent[0]->getResultSimilarDetail()}}
                                                        <button type="button" value=""
                                                                class="btn_a copy_undisabled copy_undisabled_{{$item->id}}_{{$codeName}}">
                                                            修正
                                                        </button>
                                                        <button type="button" value=""
                                                                class="btn_b edit_copy edit_copy_{{$item->id}}_{{$codeName}}">
                                                            決定
                                                        </button>
                                                    @else
                                                        －
                                                    @endif
                                                </td>
                                            @endif

                                            @if($keyProduct == 0)
                                                <td class="center bg_purple"
                                                    @if($totalProduct > 1) rowspan="{{ $totalProduct }}" @endif>
                                                    @if($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == 1)
                                                        <select
                                                            class="select_button_edit_copy select_button_edit_copy_{{$item->id}}_{{ $codeName }}"
                                                            disabled>
                                                            <option value="0"></option>
                                                            <option value="1"
                                                                    @if(isset($item->keepDataProdResults->result_similar_detail_edit) && $item->keepDataProdResults->result_similar_detail_edit == LIKELY_TO_BE_REGISTERED) selected @endif>
                                                                ○
                                                            </option>
                                                            <option value="2"
                                                                    @if(isset($item->keepDataProdResults->result_similar_detail_edit) && $item->keepDataProdResults->result_similar_detail_edit == LOOK_FORWARD_TO_REGISTERING) selected @endif>
                                                                △
                                                            </option>
                                                            <option value="3"
                                                                    @if(isset($item->keepDataProdResults->result_similar_detail_edit) && $item->keepDataProdResults->result_similar_detail_edit == LESS_LIKELY_TO_BE_REGISTERED) selected @endif>
                                                                ▲
                                                            </option>
                                                            <option value="4"
                                                                    @if(isset($item->keepDataProdResults->result_similar_detail_edit) && $item->keepDataProdResults->result_similar_detail_edit == DIFFICULT_TO_REGISTER) selected @endif>
                                                                ×
                                                            </option>
                                                        </select>
                                                        <button type="button" class="btn_b confirm_copy confirm_copy_{{$item->id}}_{{ $codeName }}">
                                                            決定
                                                        </button>
                                                    @else
                                                        －
                                                    @endif
                                                </td>
                                            @endif

                                            @if($keyProduct == 0)
                                                <td class="center result_update result_update_{{$item->id}}_{{ $codeName }} {{$item->getClassColorByTypeProduct()}}"
                                                    @if($totalProduct > 1) rowspan="{{ $totalProduct }}" @endif
                                                >
                                                    <span class="keep_data_result keep_data_result_{{$item->id}}_{{ $codeName }}">
                                                        @if($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == 1)
                                                            @if(isset($item->keepDataProdResults->result_similar_detail_final) && $item->keepDataProdResults->result_similar_detail_final != 0 && $item->keepDataProdResults->is_decision_similar_edit != 0)
                                                                {{$iconPrecheck[$item->keepDataProdResults->result_similar_detail_final]}}
                                                            @endif

                                                            @if(isset($item->keepDataProdResults->result_similar_detail_edit) && $item->keepDataProdResults->result_similar_detail_edit != 0 && $item->keepDataProdResults->is_decision_similar_daft != 0)
                                                                {{$iconPrecheck[$item->keepDataProdResults->result_similar_detail_edit]}}
                                                            @endif

                                                            @if(count($item->detailPresent) >= 1 && isset($item->detailPresent[0]) && isset($item->keepDataProdResults->is_decision_similar_draft) && $item->keepDataProdResults->is_decision_similar_draft != 0)
                                                                {{$iconPrecheck[$item->detailPresent[0]->result_similar_detail]}}
                                                            @endif
                                                        @else
                                                            －
                                                        @endif
                                                    </span>
                                                </td>
                                            @endif

                                            @if($keyProduct == 0)
                                                <td class="center {{$item->getClassColorByTypeProduct()}} checkbox_parent"
                                                    @if($totalProduct > 1) rowspan="{{ $totalProduct }}" @endif
                                                >
                                                    @if($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == 1)
                                                        <input type="hidden" class="result_similar_detail"
                                                               id="result_similar_detail{{ $item->id}}-{{  $item->m_code_id}}"/>
                                                        <input type="hidden"
                                                               name="result_data[{{ $codeName }}][result_similar_detail_edit]"
                                                               class="select_button_edit_{{$item->id}}_{{ $codeName }}"
                                                               value="{{isset($item->keepDataProdResults) ? $item->keepDataProdResults->result_similar_detail_edit : ''}}">
                                                        <input type="hidden"
                                                               name="result_data[{{ $codeName }}][result_edit]"
                                                               class="result_edit_{{$item->id}}_{{$codeName}}"
                                                               value="{{isset($item->keepDataProdResults->result_similar_detail_edit) ? $item->keepDataProdResults->result_similar_detail_edit : ''}}">
                                                        <input type="hidden"
                                                               name="result_data[{{ $codeName }}][is_decision_draft]"
                                                               class="is_decision_draft"
                                                               id="is_decision_draft_{{$item->id}}_{{$codeName}}"
                                                               value="{{isset($item->keepDataProdResults->is_decision_similar_draft) ? $item->keepDataProdResults->is_decision_similar_draft : 0}}">
                                                        <input type="hidden"
                                                               name="result_data[{{ $codeName }}][is_decision_edit]"
                                                               id="is_decision_edit_{{$item->id}}_{{$codeName}}"
                                                               value="{{isset($item->keepDataProdResults->is_decision_similar_edit) ? $item->keepDataProdResults->is_decision_similar_edit : 0}}">
                                                        <input type="hidden"
                                                               name="result_data[{{ $codeName }}][precheck_keep_data_prod_result_id]"
                                                               value="{{isset($item->keepDataProdResults) ? $item->keepDataProdResults->id : 0}}">
                                                        <input type="hidden"
                                                               name="result_data[{{ $codeName }}][result_similar_detail_present]"
                                                               class="result_similar_detail_present_{{$item->id}}_{{$codeName}}"
                                                               value="{{(isset($item->detailPresent) && isset($item->detailPresent[0])) ? $item->detailPresent[0]->result_similar_detail : ''}}">
                                                        <input type="hidden"
                                                               name="result_data[{{ $codeName }}][precheck_keep_data_prod_id]"
                                                               value="{{isset($item->keepDataProdResults->precheck_keep_data_prod_id) ? $item->keepDataProdResults->precheck_keep_data_prod_id : 0}}">
                                                        <input type="checkbox"
                                                               name="result_data[{{ $codeName }}][check_lock]"
                                                               class="checkbox_{{$item->id}}_{{$codeName}}"
                                                               @if(isset($item->keepDataProdResults) && $item->keepDataProdResults->is_block_similar == 1) checked
                                                               @endif data-foo="check_lock[]">
                                                        <span class="text-checked">確認＆ロック</span>
                                                    @else
                                                        －
                                                    @endif
                                                </td>
                                            @endif
                                        @if($keyProduct != 0) </tr> @endif
                                    @endforeach
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="14"
                                    style="text-align: center">{{__('messages.general.Common_E032')}}</td>
                            </tr>
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
                    <td class="center" @if(count($item) > 1)  rowspan="{{count($item)}}" @endif>{{$key}}</td>
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
                <input type="hidden" name="precheck_id" value="{{$precheckPresent->id}}">

                <p class="eol" style="display: flex">
                    <span>社内用コメント：{{ ($precheckCommentInternalOfTantou ? $precheckCommentInternalOfTantou->updated_at_convert : '') }}</span>
                    <span style="white-space: pre"> {!! ($precheckCommentInternalOfTantou ? $precheckCommentInternalOfTantou->content : '') !!}</span>
                </p>

                <h5>{{__('labels.precheck.title_comment_check_unique')}}:<br />
                <p class="mb10">{!! ($precheckKeepData && !empty($precheckKeepData->comment_from_ams_identification)) ? $precheckKeepData->comment_from_ams_identification : ($precheckCommentUnique ? $precheckCommentUnique->content : '') !!}<br /><br /></p>

                <h5>{{__('labels.precheck.title_comment_check_similar')}}</h5>
                <p class="eol">
                    <textarea class="normal" name="content1" placeholder="{{__('labels.precheck.comment_customers_can_see')}}">{!! ($precheckKeepData && !empty($precheckKeepData->comment_from_ams_similar)) ? $precheckKeepData->comment_from_ams_similar : ($precheckCommentSimilar ? $precheckCommentSimilar->content : old('content1'))  !!}</textarea>
                </p>

                <h5>{{__('labels.precheck.comment_internal')}}</h5>
                <p class="eol">
                    <textarea class="normal" name="content2"  placeholder="{{__('labels.precheck.comment_customers_cannot_see')}}">{{($precheckKeepData && !empty($precheckKeepData->comment_internal)) ? $precheckKeepData->comment_internal  : old('content2') }}</textarea>
                </p>

                <p class="eol" style="text-decoration: underline;"><a class="a-hover open-modal">{{ __('labels.confirm_precheck.same_screen_as_customer') }}</a></p>

                <ul class="footerBtn clearfix">
                    <li><a class="btn_custom" style="font-size: 1.3em;" href="{{route('admin.precheck_select.show-edit-precheck-unique', [
                         'id' => $id,
                         'precheck_id' => $precheckPresent->id
                         ])}}">{{__('labels.back')}}</a></li>
                    <li>
                        <button type="submit" name="submit" value="{{CREATE}}" class="btn_b"
                                style="font-size: 1.3em;">{{__('labels.precheck.edit_precheck_similar.submit_draft')}}</button>
                    </li>
                    <li>
                        <button type="submit" name="submit" value="{{CONFIRM}}" class="btn_c {{CONFIRM}}"
                                style="font-size: 1.3em;">{{__('labels.precheck.edit_precheck_similar.confirm')}}</button>
                    </li>
                </ul>

                    <div id="modal-check-role" class="modal fade" role="dialog">
                        <div class="modal-dialog" style="min-width: 80%;">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="content loaded">
                                        <p>責任者へ確認依頼しました。これから編集できなくなります。</p>
                                        <a class="btn_custom" style="font-size: 1.3em;" href="{{route('admin.home')}}">{{__('labels.back')}}</a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            </form>

        </div><!-- /contents inner -->

    </div><!-- /contents -->


    </div><!-- /wrapper -->
@endsection

@section('footerSection')
    <script>
        const errorMessageContentMaxLength = '{{ __('messages.common.errors.Common_E026') }}';
        const openModalUrl = '{{ route('admin.precheck.open-modal') }}';
        const likelyToBeRegistered = '{{ LIKELY_TO_BE_REGISTERED }}';
        const lookForwardToRegistering = '{{ LOOK_FORWARD_TO_REGISTERING }}';
        const lessLikelyToBeRegistered = '{{ LESS_LIKELY_TO_BE_REGISTERED }}';
        const difficultToRegister = '{{ DIFFICULT_TO_REGISTER }}';
        const id = @JSON($id);
        let data = @JSON($datas);
        let dataIdentification = @JSON($dataPrecheckProductSelects);
        let datasSetColorByCodeGroup = @JSON($datas);
        let precheckPresent = @json($precheckPresent);
        let precheckPresentId = @json($precheckPresent->id);
        const isConfirmTrue = '{{IS_CONFIRM_TRUE}}';
        const CONFIRM = '{{CONFIRM}}';
        let errorE0025 = '{{__('messages.common.errors.Common_E025')}}';
        let errorE001 = '{{__('messages.common.errors.Common_E001')}}';
    </script>
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script src="{{ asset('admin_assets/precheck/edit-check-similar/action.js') }}"></script>
    <script src="{{ asset('admin_assets/precheck/edit-check-unique/validate.js') }}"></script>
    <script src="{{ asset('admin_assets/precheck/open_modal.js') }}"></script>
    <script src="{{ asset('admin_assets/precheck/set_color_by_code_group.js') }}"></script>
    @if($precheckPresent->is_confirm == IS_CONFIRM_TRUE)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ ROLE_SUPERVISOR ] ])
@endsection
