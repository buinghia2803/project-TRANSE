@extends('admin.layouts.app')

@section('main-content')
    <!-- contents -->
    <div id="contents" class="admin_wide3">

        <!-- contents inner -->
        <div class="wide clearfix">

            <form id="form" method="POST" action="{{route('admin.refusal.response-plan.edit.supervisor.post', [
                    'id' => $id,
                    'trademark_plan_id' => $trademarkPlan->id
                ])}}">
                @csrf
                @include('compoments.messages')

                {{-- Trademark table --}}

                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable
                ])
                <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->

                @include('admin.modules.plans.common.common_a203', [
                    'dataCommon' => $dataCommon,
                    'title' => __('labels.a203shu.title')
                ])


                <p>{{__('labels.a203shu.title_1')}}</p>
                <input type="hidden" name="page" value="a203shu">
                @if(count($plans) > 0)
                    @foreach($plans as $keyPlan => $plan)
                        <div class="parent_table_plan parent_plan parent_plan_{{$keyPlan}}" data-key-plan="{{$keyPlan}}">
                            <h3>・{{__('labels.a203.countermeasures')}}-{{$keyPlan+1}}</h3>
                            <input type="hidden" name="plan_id[]" value="{{$plan->id}}">
                            <p>
                                <select multiple="multiple"
                                        class="plan_reasons reasons_first plan_reasons_{{$keyPlan}}"
                                        style="width: 12em; display: none;" data-index="{{$keyPlan}}" name="">
                                    @foreach($reasons as $reason)
                                        <option value="{{$reason->id}}" class="reason reason_{{$reason->id}}" @if(in_array($reason->id, $plan->reasonIds)) selected @endif>{{$reason->reason_name}}</option>
                                    @endforeach
                                </select>
                                {{__('labels.a203.response')}}
                                <input type="hidden" name="plan_reason[]" value="">
                            </p>

                            <table class="normal_b table_plan table_plan_{{$keyPlan}}">
                                <tr>
                                    <th></th>
                                    <th colspan="{{$keyPlan == 0 ? 7 : 5}}" class="bg_sky">{{__('labels.a203shu.draft')}}</th>
                                    <th colspan="{{$keyPlan == 0 ? 7 : 5}}">{{__('labels.a203shu.edit')}}</th>
                                    <th colspan="{{$keyPlan == 0 ? 7 : 5}}" class="bg_blue2">{{__('labels.a203shu.decided')}}</th>
                                </tr>
                                <tr>
                                    <th></th>
                                    <th style="width:16%;" class="bg_sky">{{__('labels.a203shu.draft_policy')}}</th>
                                    <th style="width:16%;" class="bg_sky">{{__('labels.a203shu.material_needed')}}</th>
                                    <th class="bg_sky">{{__('labels.a203shu.dissolution')}}
                                        <br/>{{__('labels.a203shu.ability')}}</th>
                                    @if($keyPlan == 0)
                                    <th class="bg_sky">{{__('labels.a203shu.summation')}}
                                        <br/>{{__('labels.a203shu.compulsory_division')}}</th>
                                    <th class="bg_sky">{{__('labels.a203shu.payment')}}
                                        <br/>{{__('labels.a203shu.compulsory_division')}}</th>
                                    @endif
                                    <th class="bg_sky">{{__('labels.a203shu.product_name')}}
                                        <br/>{{__('labels.a203shu.leave_it_all')}}</th>
                                    <th class="bg_sky">
                                        <input type="button" value="{{__('labels.a203shu.edit_all')}}"
                                               class="btn_a mb05 copy_all_value_to_edit_col"
                                               data-key-plan="{{$keyPlan}}"/><br/>
                                        <input type="button" value="{{__('labels.a203shu.decided_all')}}"
                                               class="btn_b copy_all_value_to_confirm_col"
                                               data-key-plan="{{$keyPlan}}"/></th>
                                    <th style="width:16%;">{{__('labels.a203shu.edit')}}
                                        ：<br/>{{__('labels.a203shu.draft_policy')}}</th>
                                    <th style="min-width:16%;">{{__('labels.a203shu.edit')}}
                                        ：<br/>{{__('labels.a203shu.material_needed')}}</th>
                                    <th>{{__('labels.a203shu.edit')}}：<br/>{{__('labels.a203shu.dissolution')}}
                                        <br/>{{__('labels.a203shu.ability')}}</th>
                                    @if($keyPlan == 0)
                                    <th>{{__('labels.a203shu.edit')}}：<br/>{{__('labels.a203shu.summation')}}
                                        <br/>{{__('labels.a203shu.compulsory_division')}}</th>
                                    <th>{{__('labels.a203shu.edit')}}：<br/>{{__('labels.a203shu.payment')}}
                                        <br/>{{__('labels.a203shu.compulsory_division')}}</th>
                                    @endif
                                    <th>{{__('labels.a203shu.edit')}}：<br/>{{__('labels.a203shu.product_name')}}
                                        <br/>{{__('labels.a203shu.leave_it_all')}}</th>
                                    <th>{{__('labels.a203shu.edit')}}：<br/><input type="button"
                                                                                  value="{{__('labels.a203shu.decided_all')}}"
                                                                                  class="btn_b copy_all_value_edit_to_confirm_col"
                                                                                  data-key-plan="{{$keyPlan}}"/>
                                    </th>
                                    <th style="width:16%;" class="bg_blue2">{{__('labels.a203shu.decided')}}
                                        ：<br/>{{__('labels.a203shu.draft_policy')}}</th>
                                    <th style="width:16%;" class="bg_blue2">{{__('labels.a203shu.decided')}}
                                        ：<br/>{{__('labels.a203shu.material_needed')}}</th>
                                    <th class="bg_blue2">{{__('labels.a203shu.decided')}}
                                        ：<br/>{{__('labels.a203shu.dissolution')}}<br/>{{__('labels.a203shu.ability')}}
                                    </th>
                                    @if($keyPlan == 0)
                                    <th class="bg_blue2">{{__('labels.a203shu.decided')}}
                                        ：<br/>{{__('labels.a203shu.summation')}}
                                        <br/>{{__('labels.a203shu.compulsory_division')}}</th>
                                    <th class="bg_blue2">{{__('labels.a203shu.decided')}}
                                        ：<br/>{{__('labels.a203shu.payment')}}
                                        <br/>{{__('labels.a203shu.compulsory_division')}}</th>
                                    @endif
                                    <th class="bg_blue2">{{__('labels.a203shu.decided')}}
                                        ：<br/>{{__('labels.a203shu.product_name')}}
                                        <br/>{{__('labels.a203shu.leave_it_all')}}</th>
                                    <th class="bg_blue2">{{__('labels.a203shu.confirm_all')}}
                                        <input type="checkbox" class="check_disalbed_all" data-key-plan="{{$keyPlan}}">
                                    </th>
                                </tr>
                                @foreach($plan->planDetails as $keyPlanDetail => $planDetail)
                                    <tr class="row_plan_detail row_plan_detail_{{$keyPlanDetail}}" data-index="{{$keyPlanDetail}}">
                                        <th>
                                            方針案<br/>({{$keyPlanDetail+1}})<br/>
                                            <input type="hidden" name="plan_detail_id[{{$keyPlan}}][{{$keyPlanDetail}}]"
                                                   value="{{$planDetail->id}}">
                                           @if($keyPlan == 0)
                                                @if($keyPlanDetail !== 0)
                                                    <input type="button" value="{{__('labels.delete')}}"
                                                           class="small btn_d delete_plan_detail_backend"
                                                           data-plan-detail-id="{{$planDetail->id}}">
                                                @endif
                                           @else
                                                <input type="button" value="{{__('labels.delete')}}"
                                                       class="small btn_d delete_plan_detail_backend"
                                                       data-plan-detail-id="{{$planDetail->id}}">
                                           @endif
                                        </th>
                                        <td class="bg_sky parent_type_plan_detail">
                                            <div style="width: 350px;">
                                                <span class="type_plan_detail_name">{{isset($planDetail->mTypePlan) ? $planDetail->mTypePlan->name : ''}}</span>
                                                <input type="hidden" value="{{$planDetail->type_plan_id}}"
                                                       class="type_plan_id"
                                                       name="type_plan_id[{{$keyPlan}}][{{$keyPlanDetail}}]">
                                                <br/>
                                                <br/>
                                                <span style="white-space: pre-line">{{$planDetail->plan_description}}</span>
                                                <input type="hidden" class="type_plan_detail_description"
                                                       value="{{$planDetail->plan_description}}"
                                                       name="plan_description[{{$keyPlan}}][{{$keyPlanDetail}}]">
                                                <textarea hidden class="type_plan_detail_content"
                                                       name="plan_content[{{$keyPlan}}][{{$keyPlanDetail}}]">{{$planDetail->plan_content}}</textarea>
                                            </div>

                                        </td>
                                        <td class="bg_sky parent_type_plan_doc">
                                                @if(count($planDetail->planDetailDocs) > 0)
                                                    @foreach($planDetail->planDetailDocs as $keyPlanDetailDoc => $planDetailDoc)
                                                        @if(!empty($planDetailDoc->m_type_plan_doc_id) || !empty($planDetailDoc->doc_requirement_des))
                                                        <div style="width: 350px;" class="@if($keyPlanDetailDoc != 0) mt-3 @endif">
                                                            <span class="type_plan_doc_name white-space-pre-line">{{$planDetailDoc->MTypePlanDoc ? $planDetailDoc->MTypePlanDoc->name : ''}}</span>
                                                            <input type="hidden" class="type_plan_doc_id"
                                                                   value="{{$planDetailDoc->m_type_plan_doc_id}}"
                                                                   name="type_plan_doc_id[{{$keyPlan}}][{{$keyPlanDetail}}][]">
                                                            <input type="hidden" name="plan_detail_doc_id[{{$keyPlan}}][{{$keyPlanDetail}}][{{$keyPlanDetailDoc}}]" value="{{$planDetailDoc->id}}">
                                                            <br/>
                                                            <br/>
                                                            <span class="type_plan_doc_requirement_des white-space-pre-line type_plan_doc_requirement_des_{{$keyPlanDetailDoc}}">{!! $planDetailDoc->doc_requirement_des !!}</span>
                                                            <input type="hidden" name="doc_requirement_des[{{$keyPlan}}][{{$keyPlanDetail}}][{{$keyPlanDetailDoc}}]" value="{{$planDetailDoc->doc_requirement_des}}">
                                                        </div>
                                                        @endif
                                                @endforeach
                                                @endif
                                        </td>
                                        <td class="center bg_sky parent_possibility_resolution">
                                            {{$planDetail->getTextRevolution()}}
                                            <input type="hidden" class="possibility_resolution"
                                                   value="{{$planDetail->possibility_resolution}}"
                                                   name="possibility_resolution[{{$keyPlan}}][{{$keyPlanDetail}}]">
                                        </td>
                                        @if($keyPlan == 0)
                                        <td class="center bg_sky distincts_is_add">
                                            @if(count($planDetail->distinctsIsAdd) > 0)
                                                @foreach($planDetail->distinctsIsAdd as $keyDistinct => $distinctsIsAdd)
                                                    {{$distinctsIsAdd->name ?? null}}
                                                    @if($keyDistinct < count($planDetail->distinctsIsAdd) - 1)
                                                        ,
                                                    @endif
                                                    <input type="hidden"
                                                           name="distinct_is_add[{{$keyPlan}}][{{$keyPlanDetail}}][]"
                                                           value="{{(int)$distinctsIsAdd->id ?? null}}" class="distinct_is_add">
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="center bg_sky distincts_is_distinct_ettement">
                                            @if(count($planDetail->distinctsIsAdd) > 0)
                                                @foreach($planDetail->distinctsIsDistinctSettement as $keyDistinct => $distinctsIsAdd)
                                                    {{$distinctsIsAdd->name ?? null}}  @if($keyDistinct < count($planDetail->distinctsIsDistinctSettement) - 1)
                                                        ,
                                                    @endif

                                                    <input type="hidden"
                                                           name="distinct_settement[{{$keyPlan}}][{{$keyPlanDetail}}][]"
                                                           value="{{(int)$distinctsIsAdd->id ?? null}}" class="distinct_settement">
                                                @endforeach
                                            @endif
                                        </td>
                                        @endif
                                        <td class="center bg_sky is_leave_all">
                                            @if(count($planDetail->planDetailDistincts) > 0 && $planDetail->planDetailDistincts[0]->is_leave_all == 1)
                                                {{__('labels.a203c.plan_table.leave_all')}}
                                            @endif
                                            @foreach($planDetail->planDetailDistincts as $planDetailDistinct)
                                                    <input type="hidden" name="is_leave_all[{{$keyPlan}}][{{$keyPlanDetail}}]" value="{{$planDetailDistinct->is_leave_all}}">
                                                @endforeach
                                        </td>
                                        <td class="center bg_sky">
                                            <input type="button" value="{{__('labels.a203shu.edit')}}"
                                                   class="btn_a mb05 copy_value_to_edit_col"
                                                   data-key-plan="{{$keyPlan}}"
                                                   data-key-plan-detail="{{$keyPlanDetail}}"/><br/>
                                            <input type="button" value="{{__('labels.a203shu.decided')}}"
                                                   class="btn_b copy_value_to_confirm_col"
                                                   data-key-plan="{{$keyPlan}}"/><br/>
                                        </td>
                                        <td class="info_type_plan">
                                            <div class="parent_type_plan_name">
                                                <select class="type_plan_id_edit type_plan_name"
                                                        name=""
                                                        style="width: 350px;"
                                                        data-key="{{$keyPlan}}"
                                                        data-key-detail="{{$keyPlanDetail}}">
                                                    <option value="0">選択してください</option>
                                                    @foreach($mTypePlans as $mTypePlan)
                                                        <option value="{{$mTypePlan->id}}"
                                                                @if(isset($planDetail) && $planDetail->type_plan_id_edit == $mTypePlan->id) selected @endif>
                                                            {{$mTypePlan->name}}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <input type="hidden"
                                                   name="type_plan_id_edit[{{$keyPlan}}][{{$keyPlanDetail}}]"
                                                   value="{{$planDetail->type_plan_id_edit}}"
                                                   class="type_plan_id_edit"/>
                                            <div class="parent_type_plan_description">
                                                  <textarea
                                                      class="wide plan_description_edit type_plan_description mt10"
                                                      name="plan_description_edit[{{$keyPlan}}][{{$keyPlanDetail}}]"
                                                      data-key="{{$keyPlan}}"
                                                      data-key-detail="{{$keyPlanDetail}}"
                                                  >{{$planDetail ? $planDetail->plan_description_edit : ''}}</textarea>
                                                  <textarea
                                                      class="wide plan_content_edit type_plan_content mt10"
                                                      name="plan_content_edit[{{$keyPlan}}][{{$keyPlanDetail}}]"
                                                      data-key="{{$keyPlan}}"
                                                      data-key-detail="{{$keyPlanDetail}}"
                                                  >{{$planDetail ? $planDetail->plan_content_edit : ''}}</textarea>
                                            </div>
                                            <br/>
                                        </td>
                                        @php
                                        @endphp

                                        <td class="info_file parent_type_plan_doc_edit">
                                            @if(count($planDetail->planDetailDocs) > 0)
                                                @foreach($planDetail->planDetailDocs as $keyPlanDoc => $planDetailDoc)
                                                    @if(!empty($planDetailDoc->m_type_plan_doc_id_edit) || !empty($planDetailDoc->doc_requirement_des_edit))
                                                    <div class="infor-file-item @if($keyPlanDoc != 0) mt-2 @endif">
                                                        <input type="hidden" name="plan_detail_doc_id[{{$keyPlan}}][{{$keyPlanDetail}}][]" value="{{$planDetailDoc->id}}">
                                                            @if($planDetailDoc->m_type_plan_doc_id_edit == 6 || $planDetailDoc->m_type_plan_doc_id_edit > 8)
                                                                @if(!empty($planDetailDoc->m_type_plan_doc_id_edit))
                                                                    <select class="type_plan_doc_id_edit mb-2 type_plan_doc" style="width: 350px;">
                                                                        @foreach($mTypePlanDocs as $mTypePlanDoc)
                                                                            @if($mTypePlanDoc->m_type_plan_id == 8)
                                                                                <option value="{{$mTypePlanDoc->id}}"
                                                                                        @if(isset($planDetailDoc) && $planDetailDoc->m_type_plan_doc_id_edit == $mTypePlanDoc->id) selected @endif>{{$mTypePlanDoc->name}}</option>
                                                                            @endif
                                                                        @endforeach
                                                                    </select>
                                                                    <input type="hidden" class="mb10 type_plan_doc_id_edit_value"
                                                                           name="type_plan_doc_id_edit[{{$keyPlan}}][{{$keyPlanDetail}}][]" value="{{$planDetailDoc->m_type_plan_doc_id_edit}}"/>
                                                                @endif
                                                                <br/>
                                                            @else

                                                                @if(!empty($planDetailDoc->m_type_plan_doc_id_edit))
                                                                    <span class="file_info_name white-space-pre-line">{{$planDetailDoc->MTypePlanDocEdit ? $planDetailDoc->MTypePlanDocEdit->name : ''}}</span>
                                                                    <input type="hidden" class="type_plan_doc_id_edit_value"
                                                                           value="{{$planDetailDoc->m_type_plan_doc_id_edit}}"
                                                                           name="type_plan_doc_id_edit[{{$keyPlan}}][{{$keyPlanDetail}}][]">
                                                                @endif
                                                                <br/>
                                                            @endif
                                                            <div class="parent_doc_requirement_des_edit_{{$keyPlanDoc}}">
                                                                <textarea
                                                                    class="wide file_info_description doc_requirement_des_edit doc_requirement_des_edit_{{$keyPlanDoc}}
                                                                    @if(isset($planDetailDoc) && $planDetailDoc->m_type_plan_doc_id_edit == 11) disabled @endif"
                                                                    name="doc_requirement_des_edit[{{$keyPlan}}][{{$keyPlanDetail}}][]"
                                                                    style="width: 500px"
                                                                    data-key-plan-detail-doc="{{$keyPlanDoc}}"
                                                                    @if(isset($planDetailDoc) && $planDetailDoc->m_type_plan_doc_id_edit == 11) readonly @endif>{{$planDetailDoc ? $planDetailDoc->doc_requirement_des_edit : ''}}</textarea>
                                                                <input type="button" value="{{__('labels.a203.delete2')}}" class="btn_a small delete_file_info_description"><br/>
                                                            </div>
                                                    </div>
                                                    @endif
                                                @endforeach
                                                    <div class="parent_add_file_info mt-3">
                                                        @if(count($planDetail->planDetailDocs) > 0)
                                                            @if((isset($planDetail->planDetailDocs) && $planDetail->planDetailDocs[0]->m_type_plan_doc_id_edit >= 6))
                                                                <a href="javascript:;" class="add_file_info" data-key="{{$keyPlan}}" data-key-detail="{{$keyPlanDetail}}">+ {{__('labels.a203.add4')}}</a>
                                                            @endif
                                                        @endif
                                                    </div>
                                                @endif
                                        </td>
                                        <td class="center">
                                            <select name="possibility_resolution_edit[{{$keyPlan}}][{{$keyPlanDetail}}]"
                                                    class="possibility_resolution_edit">
                                                @foreach($possibilityResolutions as $key => $value)
                                                    <option value="{{$value}}"
                                                            @if($planDetail->possibility_resolution_edit == $value) selected @endif>{{$key}}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        @if($keyPlan == 0)
                                        <td class="center distincts_is_add_edit">
                                            @if(count($planDetail->distinctsIsAdd) > 0)
                                            @foreach($planDetail->distinctsIsAdd as $keyDistinct =>  $distinct)
                                              {{$distinct->name }}
                                                @if($keyDistinct < count($planDetail->distinctsIsAdd) - 1)
                                                    ,
                                                @endif
                                            @endforeach
                                            @endif
                                        </td>
                                        <td class="center parent_is_distinct_settlement_edit">
                                            @if(count($planDetail->distinctsIsAdd) > 0)
                                            <select multiple="multiple"
                                                    class="multi is_distinct_settlement_edit is_distinct_settlement_edit_{{$keyPlan}}_{{$keyPlanDetail}}"
                                                    style="width: 12em; display: none;" data-key-plan="{{$keyPlan}}"
                                                    data-key-plan-detail="{{$keyPlanDetail}}"
                                                    name="Distinct">
                                                @foreach($planDetail->distinctsIsAdd as $keyPlanDetailDisinct =>  $distinct)
                                                        @php
                                                            $selected = $planDetail->isDistinctSettmentEdit->where('id', $distinct->id);
                                                        @endphp
                                                        <option value="{{$distinct->id}}" class="distinct distinct_{{$keyDistinct}}" {{ (!empty($selected) && count($selected) > 0) ? 'selected' : '' }}>第{{$distinct->name}}類 </option>
                                                @endforeach
                                            </select>
                                            @endif
                                            <input type="hidden" name="is_distinct_settlement_edit[]">
                                            <input type="hidden" name="is_distinct_settlement[]" value="1">
                                        </td>
                                        @endif
                                        <td class="center">
                                            <input type="checkbox"
                                                   class="is_leave_all_edit"
                                                   @if(isset($planDetail) && isset($planDetail->planDetailDistincts[0]) && $planDetail->planDetailDistincts[0]->is_leave_all_edit == 1) checked @endif />
                                            <input type="hidden"  name="is_leave_all_edit[{{$keyPlan}}][]"
                                                   class="is_leave_all_edit_value is_leave_all_edit_value_{{$keyPlan}}_{{$keyPlanDetail}}"
                                                    value="{{isset($planDetail->planDetailDistincts[0]) && isset($planDetail->planDetailDistincts[0]->is_leave_all_edit) ? $planDetail->planDetailDistincts[0]->is_leave_all_edit : ''}}"/>
                                        </td>
                                        <td class="center">
                                            <input type="button" value="{{__('labels.a203shu.decided')}}"
                                                   class="btn_b copy_value_edit_to_confirm_col"
                                                   data-key-plan="{{$keyPlan}}"
                                                   data-key-plan-detail="{{$keyPlanDetail}}"/>
                                        </td>
                                        <td class="bg_blue2 parent_type_plan_detail_confirm">
                                            <div style="width: 350px;">
                                                <span
                                                    class="confirm_type_plan_name confirm_type_plan_name_{{$keyPlanDetail}}">
                                                    @if($planDetail->is_decision == 1)
                                                        {{$planDetail->mTypePlan ? $planDetail->mTypePlan->name : ''}}
                                                    @elseif($planDetail->is_decision == 2)
                                                        {{$planDetail->mTypePlanEdit ? $planDetail->mTypePlanEdit->name : ''}}
                                                    @endif
                                                </span>
                                                <br/>
                                                @php
                                                    $planDescriptionText = '';
                                                    if($planDetail->is_decision == 1) {
                                                        $planDescriptionText = $planDetail->plan_description ? $planDetail->plan_description : '';
                                                    } elseif ($planDetail->is_decision == 2) {
                                                        $planDescriptionText = $planDetail->plan_description_edit ? $planDetail->plan_description_edit : '';
                                                    }
                                                @endphp
                                                <span class="confirm_type_plan_description mt10" style="white-space: pre-line">{{ $planDescriptionText ?? '' }}</span>
                                            </div>
                                        </td>
                                        <td class="bg_blue2 parent_type_plan_detail_doc_confirm">
                                            @if(count($planDetail->planDetailDocs) > 0)
                                                @foreach($planDetail->planDetailDocs as $planDetailDocc)
                                                    @if($planDetail->is_decision == 1 && (!empty($planDetailDocc->m_type_plan_doc_id) || !empty($planDetailDocc->doc_requirement_des)))
                                                        <div style="min-width: 350px;" class="infor-file-item_confirm">
                                                             <span class="confirm_type_plan_doc_name white-space-pre-line w-100">{{$planDetailDocc->MTypePlanDoc ? $planDetailDocc->MTypePlanDoc->name : ''}}</span>
                                                             <br/>
                                                             <br/>
                                                             <span class="confirm_doc_requirement_des" style="white-space: pre-line">{{$planDetailDocc->doc_requirement_des}}</span>
                                                        </div>
                                                    @elseif($planDetail->is_decision == 2 && (!empty($planDetailDocc->m_type_plan_doc_id_edit) || !empty($planDetailDocc->doc_requirement_des_edit)))
                                                        <div style="min-width: 350px;" class="infor-file-item_confirm">
                                                            <span class="confirm_type_plan_doc_name white-space-pre-line">{{$planDetailDocc->MTypePlanDocEdit ? $planDetailDocc->MTypePlanDocEdit->name : ''}}</span>
                                                            <br/>
                                                            <br/>
                                                            <span class="confirm_doc_requirement_des" style="white-space: pre-line">{{$planDetailDocc->doc_requirement_des_edit}}</span>
                                                        </div>
                                                    @else
                                                        <div style="min-width: 350px;" class="infor-file-item_confirm">
                                                            <span class="confirm_type_plan_doc_name white-space-pre-line"></span>
                                                            <br/>
                                                            <br/>
                                                            <span class="confirm_doc_requirement_des" style="white-space: pre-line"></span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            @else
                                                <div style="width: 350px;" class="infor-file-item_confirm">
                                                    <span class="confirm_type_plan_doc_name white-space-pre-line"></span>
                                                    <br/>
                                                    <br/>
                                                    <span class="confirm_doc_requirement_des" style="white-space: pre-line"></span>
                                                </div>
                                            @endif
                                        </td>
                                        <td class="center bg_blue2 parent_possibility_resolution_confirm">
                                            <span class="confirm_possibility_resolution">
                                                @if($planDetail->is_decision == 1)
                                                    {{isset($planDetail->possibility_resolution) ? $textPosibilitiResolution[$planDetail->possibility_resolution] : ''}}
                                                @elseif($planDetail->is_decision == 2)
                                                    {{isset($planDetail->possibility_resolution_edit) ? $textPosibilitiResolution[$planDetail->possibility_resolution_edit] : ''}}
                                                @endif
                                            </span>
                                        </td>
                                        @if($keyPlan == 0)
                                        <td class="center bg_blue2 confirm_distincts_is_add">
                                            @if(count($planDetail->distinctsIsAdd) > 0)
                                                @foreach($planDetail->distinctsIsAdd as $keyDistinct => $distinctsIsAdd)
                                                    @if($planDetail->is_decision == 1)
                                                        {{$distinctsIsAdd->name ?? null}}
                                                        @if($keyDistinct < count($planDetail->distinctsIsAdd) - 1)
                                                            ,
                                                        @endif
                                                        <input type="hidden"
                                                               name="distinct_is_add_edit[{{$keyPlan}}][{{$keyPlanDetail}}][]"
                                                               value="{{$distinctsIsAdd->id ?? null}}" class="distinct_is_add_edit">
                                                    @elseif($planDetail->is_decision == 2)
                                                        {{$distinctsIsAdd->name ?? null}}
                                                        @if($keyDistinct < count($planDetail->distinctsIsAdd) - 1)
                                                            ,
                                                        @endif
                                                        <input type="hidden"
                                                               name="distinct_is_add_edit[{{$keyPlan}}][{{$keyPlanDetail}}][]"
                                                               value="{{$distinctsIsAdd->id ?? null}}" class="distinct_is_add_edit">
                                                    @endif
                                                @endforeach
                                            @endif
                                        </td>
                                        <td class="center bg_blue2 confirm_is_distinct_settlement">
                                                @if($planDetail->is_decision == 1)
                                                    @if(count($planDetail->distinctsIsDistinctSettement) > 0)
                                                        @foreach($planDetail->distinctsIsDistinctSettement as $keyDistinctSettment => $distinctsSettment)
                                                                {{$distinctsSettment->name}}
                                                                @if($keyDistinctSettment < count($planDetail->distinctsIsDistinctSettement) - 1)
                                                                    ,
                                                                @endif
                                                                <input type="hidden"
                                                                       name="distinct_is_add_edit[{{$keyPlan}}][{{$keyPlanDetail}}][]"
                                                                       value="{{$distinctsIsAdd->id ?? null}}" class="distinct_is_add_confirm">
                                                         @endforeach
                                                    @endif
                                                @elseif($planDetail->is_decision == 2)
                                                    @if(count($planDetail->isDistinctSettmentEdit) > 0)
                                                        @foreach($planDetail->isDistinctSettmentEdit as $keyDistinct => $distinctsSettmentEdit)
                                                            {{$distinctsSettmentEdit->name}}
                                                            @if($keyDistinct < count($planDetail->isDistinctSettmentEdit) - 1)
                                                                ,
                                                            @endif
                                                            <input type="hidden"
                                                                   name="distinct_is_add_edit[{{$keyPlan}}][{{$keyPlanDetail}}][]"
                                                                   value="{{$distinctsIsAdd->id ?? null}}" class="distinct_is_add_confirm">
                                                        @endforeach
                                                    @endif
                                                @endif
                                            @endif
                                        </td>
                                        <td class="center bg_blue2 confirm_is_leave_all">
                                            @if(count($planDetail->planDetailDistincts) > 0)
                                                @if($planDetail->is_decision == 1)
                                                    @if($planDetail->planDetailDistincts[0]->is_leave_all != 0) 全て残す@endif
                                                @elseif($planDetail->is_decision == 2)
                                                @if($planDetail->planDetailDistincts[0]->is_leave_all_edit == 1) 全て残す@endif
                                                @endif
                                            @endif
                                        </td>
                                        <td class="center bg_blue2 parent_div_is_confirm" nowrap>
                                            <div class="parent_is_confirm">
                                                <input type="checkbox"
                                                       class="check_disabled" @if($planDetail->is_confirm == 1) checked @endif"
                                                       data-key-plan="{{$keyPlan}}"
                                                       data-key-plan-detail="{{$keyPlanDetail}}"/> {{__('labels.a203shu.check_disabled')}}
                                                <input type="hidden"
                                                       name="is_confirm[{{$keyPlan}}][{{$keyPlanDetail}}]"
                                                       class="is_confirm_value" value="{{$planDetail->is_confirm}}"/>
                                                <input type="hidden"
                                                       name="is_decision[{{$keyPlan}}][{{$keyPlanDetail}}]"
                                                       class="is_decision is_decision_{{$keyPlan}}_{{$keyPlanDetail}}">
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                            <div class="eol"></div>
                            <p><a href="javascript:;"
                                  class="add_plan_detail add_plan_detail_{{$keyPlan}}"
                                  data-count="{{$keyPlan}}">+ {{__('labels.a203.add1')}}</a></p>
                            @if($keyPlan != 0)
                                <p><input type="button" class="btn_a delete_plan_backend" data-count="{{$keyPlan}}"
                                          data-plan-id="{{$plan->id}}" value="この対応策を削除"></p>
                            @endif
                        </div>
                    @endforeach
                    <hr/>
                @endif
                <p class="eol"><a href="javascript:;" class="add_reciprocal_countermeasures">+ 対応策の追加</a></p>
                <dl class="w16em eol clearfix">
                    <dt>{{__('labels.support_first_times.comment')}}</dt>
                    <dd class="parent_plan_comment"><textarea class="middle_c plan_comment"
                                                              name="plan_comment">{!! isset($planComment) ? $planComment->content : '' !!}</textarea>
                    </dd>

                    <dt>{{__('labels.support_first_times.comment')}}</dt>
                    @foreach($planComments as $planComment)
                        <dd>{{\App\Helpers\CommonHelper::formatTime($planComment->created_at, 'Y/m/d')}}  <span style="white-space: pre-line">{!! $planComment->content !!}</span></dd>
                    @endforeach

                </dl>
                <ul class="footerBtn clearfix">
                    @if(!empty(request()->type) || $isBlockScreen == true)
                        <li>
                            <button type="submit" class="btn_b no_disabled"
                                onclick="window.location = '{{ route('admin.refusal.response-plan.product.edit.supervisor', [
                                    'id' => $comparisonTrademarkResult->id,
                                    'trademark_plan_id' => $trademarkPlan->id,
                                ]) }}'; return false;"
                            >{{__('labels.a203.save_draft_and_next_page')}}</button>
                        </li>
                    @endif
                    <li>
                        <button type="submit" name="submit" value="save"
                                class="btn_b save">{{__('labels.a203.save_draft')}}</button>
                    </li>
                    <li>
                        <button type="submit" name="submit" value="submit"
                                class="btn_b submit" id="submit">{{__('labels.a203.save_draft_and_next_page')}}</button>
                        <input type="submit" id="input_submit" hidden name="submit" value="submit">
                    </li>
                </ul>
            </form>
        </div><!-- /contents inner -->
    </div>
@endsection
@section('footerSection')
    <script>
        const errorMessageRequired = '{{__('messages.general.Common_E001')}}';
        const draftPolicy = '{{__('labels.a203.draft_policy')}}';
        const ability = '{{__('labels.a203.ability')}}';
        const handle = '{{__('labels.a203.handle')}}';
        const planDetailDoc = '{{__('labels.a203.plan_detail_doc')}}';
        const district1 = '{{__('labels.a203.district1')}}';
        const district2 = '{{__('labels.a203.district2')}}';
        const product = '{{__('labels.a203.product')}}';
        const serviceName = '{{__('labels.a203.service_name')}}';
        const backAll = '{{__('labels.a203.back_all')}}';
        const desription1 = '{{__('labels.a203.desription_1')}}';
        const countermeasures = '{{__('labels.a203.countermeasures')}}';
        const uses = '{{__('labels.a203.uses')}}';
        const response = '{{__('labels.a203.response')}}';
        const delete1 = '{{__('labels.a203.delete1')}}';
        const delete2 = '{{__('labels.a203.delete2')}}';
        const delete3 = '{{__('labels.a203.delete3')}}';
        const delete4 = '{{__('labels.delete')}}';
        const add1 = '{{__('labels.a203.add1')}}';
        const add2 = '{{__('labels.a203.add2')}}';
        const add3 = '{{__('labels.a203.add3')}}';
        const add4 = '{{__('labels.a203.add4')}}';
        const defaultSelect = '{{__('labels.profile_edit.please_select')}}';
        const save_draft = '{{__('labels.a203.save_draft_and_next_page')}}';
        const save_draft_and_next_page = '{{__('labels.a203.save_draft_and_next_page')}}';
        const deletePlanDetailTitle = '{{__('labels.a203.delete_plan_detail_title')}}';
        const deletePlanTitle = '{{__('labels.a203.delete_plan_title')}}';
        let reasons = @JSON($reasons);
        let mTypePlans = @JSON($mTypePlans);
        let mTypePlanDocs = @JSON($mTypePlanDocs);
        let id = @JSON($id);
        let trademarkPlan = @JSON($trademarkPlan);
        let createPlanReasonURL = '{{route('admin.refusal.response-plan.store')}}';
        let deletePlanDetailURL = '{{route('admin.refusal.response-plan.delete-plan-detail')}}';
        let deletePlanURL = '{{route('admin.refusal.response-plan.delete-plan')}}';
        let plans = @JSON($plans);
        const errorCommonE026 = '{{__('messages.general.Common_E026')}}';
        const errorCommonE025 = '{{__('messages.general.Common_E025')}}';
        const errorCommonE053 = '{{__('messages.general.Common_E053')}}';
        const errorHosinA203E001 = '{{__('messages.general.Hoshin_A203_E001')}}';
        const errorValidate = '{{__('messages.general.Common_E001')}}';
        const CANCEL = '{{__('labels.btn_cancel')}}';
        const OK = '{{__('labels.confirm')}}';
        const OK2 = '{{__('labels.btn_ok')}}';
        const NO = '{{__('labels.back')}}';
        const checkAll = '{{__('labels.u031b.check_all')}}';
        const errorCorrespondenceA203E002 = '{{__('messages.general.correspondence_A203_E002')}}';
        const errorCorrespondenceA203E003 = '{{__('messages.general.correspondence_A203_E003')}}';
        const errorCorrespondenceA203E004 = '{{__('messages.general.correspondence_A203_E004')}}';
        let redirectBack = @JSON($redirectBack);
    </script>
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/JavaScript" src="{{asset('admin_assets/plan/a203shu.js')}}"></script>
    <script>
        $('body').on('click', '.close-alert', function () {
            history.pushState(null, null, redirectBack);
        })
    </script>
    @if(!empty(request()->type) || $isBlockScreen == true)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ROLE_SUPERVISOR] ])
@endsection
