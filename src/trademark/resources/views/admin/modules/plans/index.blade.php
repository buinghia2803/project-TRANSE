@extends('admin.layouts.app')
@php
    $user = auth()->user();
@endphp
@section('main-content')
<div id="contents" class="admin">
    <!-- contents inner -->
    <div class="wide clearfix">
        <form id="form" method="POST" action="{{route('admin.refusal.response-plan.store')}}">
        @csrf
        @include('compoments.messages')
        <div class="error-mess-ajax"></div>
        @include('admin.components.includes.trademark-table', [
            'table' => $trademarkTable
        ])
        @include('admin.modules.plans.common.common_a203', [
            'dataCommon' => $dataCommon
        ])
        <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->
            <input type="hidden" name="page" value="a203">
        @if(isset($plans) && count($plans) > 0)
    @foreach($plans as $keyPlan => $plan)
        <div class="parent_plan parent_plan_{{$keyPlan}}" data-key-plan="{{$keyPlan}}">
            <h3>・{{__('labels.a203.countermeasures')}}-{{$keyPlan+1}}</h3>
            <input type="hidden" name="plan_id[]" value="{{$plan->id}}">
            <p>{{__('labels.a203.desription_1')}}</p>
            <p style="margin-bottom: 0" class="parent_reasons mb0">
                <select multiple="multiple"
                        class="plan_reasons reasons_first plan_reasons_{{$keyPlan}}"
                        style="width: 15em; display: none;" data-index="{{$keyPlan}}" name="">
                        @foreach($reasons as $reason)
                            <option value="{{$reason->id}}" class="reason reason_{{$reason->id}}" @if(in_array($reason->id, $plan->reasonIds)) selected @endif>{{$reason->reason_name}}</option>
                        @endforeach
                </select>
                {{__('labels.a203.response')}}</p>
            <input type="hidden" name="plan_reason[]" value="">
            <ul class="clearfix mb10 mt-3">
                <li><button type="submit" value="save" name="submit"
                            class="btn_b create_plan_reason @if($user->role != ROLE_MANAGER) disabled @endif">保存</button>
                </li>
            </ul>
            <div class="parent_table_plan">
                <table class="normal_b mb10 table_plan">
                    <tbody class="tbody tbody_0">
                    <tr>
                        <th></th>
                        <th class="em40">{{__('labels.a203.draft_policy')}}</th>
                        <th>{{__('labels.a203.handle')}}<br>{{__('labels.a203.ability')}}</th>
                        <th class="em24">{{__('labels.a203.plan_detail_doc')}}</th>
                        @if($keyPlan == 0)
                        <th>{{__('labels.a203.district1')}}</th>
                        <th>{{__('labels.a203.district2')}}</th>
                        @endif
                        <th>{{__('labels.a203.product')}}<br>{{__('labels.a203.service_name')}}
                            <br>{{__('labels.a203.back_all')}}</th>
                    </tr>
                    @if(count($plan->planDetails) > 0)
                    @foreach($plan->planDetails as $keyDetail => $planDetail)
                        <tr class="row_plan_detail row_plan_detail_{{$keyDetail}}" data-index="{{$keyDetail}}">
                            <th>
                                <span class="title_plan_detail_{{$keyDetail}}">{{__('labels.a203.draft_policy')}}<br>({{$keyDetail+1}})</span>
                                <br>
                                <input type="hidden" name="plan_detail_id[{{$keyPlan}}][]"
                                       value="{{$planDetail->id}}" class="plan_id">
                                @if($keyPlan == 0)
                                    @if($keyDetail !== 0)
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
                            <td class="info_type_plan">
                                <select
                                    class="type_plan_name type_plan_name_{{$keyPlan}}_{{$keyDetail}} w-75"
                                    name="type_plan_id[{{$keyPlan}}][{{$keyDetail}}]"
                                    data-key="{{$keyPlan}}" data-key-detail="{{$keyDetail}}">
                                    <option value="0">{{__('labels.profile_edit.please_select')}}</option>
                                    @foreach($mTypePlans as $mTypePlan)
                                        <option value="{{$mTypePlan->id}}"
                                                @if($planDetail->type_plan_id == $mTypePlan->id) selected @endif>{{$mTypePlan->name}}</option>
                                    @endforeach
                                </select>
                                <textarea class="mt10 wide type_plan_description"
                                          name="plan_description[{{$keyPlan}}][{{$keyDetail}}]"
                                          value="">{{$planDetail->plan_description}}</textarea><br>
                                <textarea type="text" hidden class="type_plan_content" name="plan_content[{{$keyPlan}}][{{$keyDetail}}]">
                                    {{$planDetail->plan_content}}</textarea>
                                <input type="button" value="{{__('labels.a203.delete2')}}"
                                       class="btn_a small delete_type_plan_description @if($user->role != ROLE_MANAGER) disabled @endif">
                                <br>
                                <br>
                            </td>
                            <td class="center">
                                <select name="possibility_resolution[{{$keyPlan}}][{{$keyDetail}}]">
                                    @foreach($possibilityResolutions as $key => $value)
                                        <option value="{{$value}}"
                                                @if((isset($planDetail) && $value === $planDetail->possibility_resolution)) selected @endif>{{$key}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="info_file">
                                @foreach($planDetail->planDetailDocs as $keyPlanDoc => $planDetailDoc)
                                    @if(!empty($planDetailDoc->m_type_plan_doc_id) ||!empty($planDetailDoc->doc_requirement_des))
                                        <div class="infor-file-item @if($keyPlanDoc != 0) mt-3 @endif">
                                            <input type="hidden" name="plan_detail_doc_id[{{$keyPlan}}][{{$keyDetail}}][]" value="{{$planDetailDoc->id}}">
                                            @if($planDetailDoc->m_type_plan_doc_id == 6 || $planDetailDoc->m_type_plan_doc_id > 8)
                                                @if(!empty($planDetailDoc->m_type_plan_doc_id))
                                                    <select class="type_plan_doc_id mb-2 type_plan_doc" name="type_plan_doc_id[{{$keyPlan}}][{{$keyDetail}}][]" style="width: 350px;" >
                                                        @foreach($mTypePlanDocs as $mTypePlanDoc)
                                                            @if($mTypePlanDoc->m_type_plan_id == 8)
                                                                <option value="{{$mTypePlanDoc->id}}" @if(isset($planDetailDoc) && $planDetailDoc->m_type_plan_doc_id == $mTypePlanDoc->id) selected @endif>{{$mTypePlanDoc->name}}</option>
                                                            @endif
                                                        @endforeach
                                                    </select>
                                                @endif
                                                <br/>
                                            @else
                                                @if(!empty($planDetailDoc->m_type_plan_doc_id))
                                                    <span class="file_info_name white-space-pre-line">{{$planDetailDoc->MTypePlanDoc ? $planDetailDoc->MTypePlanDoc->name : ''}}</span>
                                                    <input type="hidden" class="type_plan_doc_id" value="{{$planDetailDoc->m_type_plan_doc_id}}" name="type_plan_doc_id[{{$keyPlan}}][{{$keyDetail}}][]">
                                                @endif
                                                <br/>
                                            @endif
                                            <div class="parent_doc_requirement_des_{{$keyPlanDoc}}">
                                                 <textarea
                                                     class="wide file_info_description doc_requirement_des doc_requirement_des_{{$keyPlanDoc}}
                                                      @if(isset($planDetailDoc) && $planDetailDoc->m_type_plan_doc_id == 11) disabled @endif"
                                                     name="doc_requirement_des[{{$keyPlan}}][{{$keyDetail}}][]"
                                                     style="width: 500px"
                                                     data-key-plan-detail-doc="{{$keyPlanDoc}}"
                                                     @if(isset($planDetailDoc) && $planDetailDoc->m_type_plan_doc_id == 11) readonly @endif>{{$planDetailDoc ? $planDetailDoc->doc_requirement_des : ''}}</textarea>
                                                <input type="button" value="{{__('labels.a203.delete2')}}" class="btn_a small delete_file_info_description"><br/>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                <div class="parent_add_file_info mt-3">
                                    @if(count($planDetail->planDetailDocs) > 0)
                                        @if(isset($planDetail->planDetailDocs) && $planDetail->planDetailDocs[0]->m_type_plan_doc_id >= 6)
                                            <a href="javascript:;" class="add_file_info mt-1" data-key="{{$keyPlan}}" data-key-detail="{{$keyDetail}}">+ {{__('labels.a203.add4')}}</a>
                                        @endif
                                    @endif
                                </div>
                            </td>
                            @if($keyPlan == 0)
                            <td class="center">
                                @if(count($planDetail->distinctsIsAdd) > 0)
                                    @foreach($planDetail->distinctsIsAdd as $keyDistinct => $distinctsIsAdd)
                                        {{$distinctsIsAdd->name}}
                                        @if($keyDistinct < count($planDetail->distinctsIsAdd) - 1)
                                            ,
                                        @endif
                                        <input type="hidden" name="distinct_is_add[{{$keyPlan}}][{{$keyDetail}}][]" class="distinct_is_add[]" value="{{$distinctsIsAdd->id}}">
                                    @endforeach
                                @endif
                            </td>
                            <td class="center parent_is_distinct_settlement" style="">
                                @if(count($planDetail->distinctsIsAdd) > 0)
                                    <select multiple="multiple" class="multi is_distinct_settlement_{{$keyPlan}}_{{$keyDetail}}"
                                            style="width: 12em; display: none;" data-index="{{$keyPlan}}"  data-key-plan-detail="{{$keyDetail}}" name="Distinct">
                                        @foreach($planDetail->distinctsIsAdd as $keyDistinct =>  $distinct)
                                            @php
                                               $selected = $planDetail->distinctsIsDistinctSettement->where('id', $distinct->id);
                                            @endphp
                                            <option value="{{$distinct->id}}" class="distinct distinct_{{$keyDistinct}}" {{  (!empty($selected) && count($selected) > 0) ? 'selected' : '' }}>{{$distinct->name}}</option>
                                        @endforeach
                                    </select>
                                    <input type="hidden" name="is_distinct_settlements[]" class="is_distinct_settlement[]">
                                @endif
                            </td>
                            @endif
                            <td class="center">
                                <input type="checkbox" class="is_leave_all" name="is_leave_all[{{$keyPlan}}_{{$keyDetail}}]" @if(isset($planDetail) && isset($planDetail->planDetailDistincts[0]) && $planDetail->planDetailDistincts[0]->is_leave_all == 1) checked @endif data-foo="is_leave_all[]"/>
                            </td>
                        </tr>
                    @endforeach
                    @else
                        <tr class="row_plan_detail row_plan_detail_0" data-index="0">
                            <th>
                                {{__('labels.a203.draft_policy')}}<br>(1) <br>
                                <input type="hidden" name="plan_detail_id[{{$keyPlan}}][0]" value="0">
                            </th>
                            <td class="info_type_plan">
                                <select class="mb10 type_plan_name" name="type_plan_id[{{$keyPlan}}][0]" data-id="0" data-key="{{$keyPlan}}" data-key-detail="0">
                                    <option value="0">{{__('labels.profile_edit.please_select')}}</option>
                                    @foreach($mTypePlans as $mTypePlan)
                                        <option value="{{$mTypePlan->id}}">{{$mTypePlan->name}}</option>
                                    @endforeach
                                </select>
                                <textarea class="wide type_plan_description" name="plan_description[{{$keyPlan}}][0]"></textarea><br>
                                <textarea type="text" hidden class="type_plan_content" name="plan_content[{{$keyPlan}}][0]"></textarea>
                                <input type="button" value="クリア" class="btn_a small delete_type_plan_description">
                                <br>
                                <br>
                            </td>
                            <td class="center">
                                <select name="possibility_resolution[{{$keyPlan}}][0]">
                                    @foreach($possibilityResolutions as $key => $value)
                                        <option value="{{$value}}" @if((isset($planDetail) && $value === $planDetail->possibility_resolution)) selected @endif>{{$key}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="info_file"></td>
                            <td class="center"></td>
                            <td class="center" style="">
                                <select multiple="multiple" class="multi" style="width: 8em; display: none;"></select>
                            </td>
                            <td class="center">
                                <input type="checkbox" class="is_leave_all" name="is_leave_all[{{$keyPlan}}_0]" @if(isset($planDetail) && isset($planDetail->planDetailDistincts[0]) && $planDetail->planDetailDistincts[0]->is_leave_all == 1) checked @endif data-foo="is_leave_all[]">
                            </td>
                        </tr>
                    @endif
                    </tbody>
                </table>
                @if($user->role == ROLE_MANAGER)
                    <p class="eol"><a href="javascript:;"
                                      class="add_plan_detail add_plan_detail_{{$keyPlan}}"
                                      data-count="{{$keyPlan}}">+ {{__('labels.a203.add1')}}</a></p>
                @endif
                @if($keyPlan != 0)
                    <p><input type="button" class="btn_a delete_plan_backend" data-plan-id="{{$plan->id}}"
                              value="{{__('labels.a203.delete3')}}"></p>
                @endif
                <hr>
            </div>
        </div>
    @endforeach
    @else
     <div class="parent_plan parent_plan_0" data-key-plan="0">
          <h3>・{{__('labels.a203.countermeasures')}}-1</h3>
          <p>{{__('labels.a203.desription_1')}}</p>
          <input type="hidden" name="plan_id[]" value="0">
          <p class="parent_reasons" style="margin: 0">
                        <select multiple="multiple" class="multi plan_reasons reasons_first plan_reasons_0" style="width: 12em; display: none;" data-index="0" name="">
                            @foreach($reasons as $keyReason =>  $reason)
                                <option value="{{$reason->id}}"
                                        class="reason_{{$keyReason}}">{{$reason->reason_name}}</option>
                            @endforeach
                        </select>

                        {{__('labels.a203.response')}}</p>
         <input type="hidden" name="plan_reason[]" value="">
         <ul class="clearfix mb10 mt-2">
             <li><button type="submit" value="save" name="submit"
                         class="btn_b create_plan_reason @if($user->role != ROLE_MANAGER) disabled @endif">{{__('labels.a203.add2')}}</button>
             </li>
         </ul>
         <div class="parent_table_plan">
            <table class="normal_b mb10 table_plan">
                <tbody class="tbody tbody_0">
                <tr>
                    <th></th>
                    <th class="em40">{{__('labels.a203.draft_policy')}}</th>
                    <th>{{__('labels.a203.handle')}}<br>{{__('labels.a203.ability')}}</th>
                    <th class="em24">{{__('labels.a203.plan_detail_doc')}}</th>
                    <th>{{__('labels.a203.district1')}}</th>
                    <th>{{__('labels.a203.district2')}}</th>
                    <th>{{__('labels.a203.product')}}<br>{{__('labels.a203.service_name')}}
                        <br>{{__('labels.a203.back_all')}}</th>
                </tr>
                <tr class="row_plan_detail row_plan_detail_0" data-index="0">
                    <th>
                        {{__('labels.a203.draft_policy')}}<br>(1) <br>
                        <input type="hidden" name="plan_detail_id[0][0]" value="0">
                    </th>
                    <td class="info_type_plan">
                        <select class="mb10 type_plan_name" name="type_plan_id[0][0]" data-id="0"
                                data-key="0" data-key-detail="0">
                            <option value="0">{{__('labels.profile_edit.please_select')}}</option>
                            @foreach($mTypePlans as $mTypePlan)
                                <option value="{{$mTypePlan->id}}">{{$mTypePlan->name}}</option>
                            @endforeach
                        </select>
                        <textarea class="wide type_plan_description"
                                  name="plan_description[0][0]"></textarea><br>
                        <textarea type="text" class="type_plan_content" hidden name="plan_content[0][0]"></textarea>
                        <input type="button" value="{{__('labels.a203.delete2')}}"
                               class="btn_a small delete_type_plan_description">
                        <br>
                        <br>
                    </td>
                    <td class="center">
                        <select name="possibility_resolution[0][]">
                            @foreach($possibilityResolutions as $key => $value)
                                <option value="{{$value}}">{{$key}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td class="info_file">
                    </td>
                    <td class="center">

                    </td>
                    <td class="center" style="">
                        <select multiple="multiple" class="multi"
                                style="width: 8em; display: none;">
                        </select>
                    </td>
                    <td class="center"><input type="checkbox" name="is_leave_all[0_0]" data-foo="is_leave_all[]" class="is_leave_all">
                    </td>
                </tr>
                </tbody>
            </table>
            @if($user->role == ROLE_MANAGER)
                <p class="eol"><a href="javascript:;" class="add_plan_detail add_plan_detail_0"
                                  data-count="0">+ {{__('labels.a203.add1')}}</a></p>
            @endif
            <hr>
         </div>
     </div>
     @endif
        @if($user->role == ROLE_MANAGER)
            <p class="eol add_reciprocal_countermeasures">
                <a href="javascript:;" class="">+ {{__('labels.a203.add3')}}</a>
            </p>
        @endif

        @if(count($planComments) > 0)
            <p class="eol">
                {{__('labels.support_first_times.comment')}}
                @foreach($planComments as $planComment)
                    <span>{{\App\Helpers\CommonHelper::formatTime($planComment->created_at, 'Y/m/d')}} {{$planComment->content}}</span>
                    <br>
                @endforeach
            </p>
        @endif

        <ul class="footerBtn clearfix">
            @if(!empty(request()->type) || $flagDisabled == true)
                <li>
                    <button type="button" class="btn_b no_disabled" style="font-size: 1.3em"
                            onclick="window.location = '{{ route('admin.refusal.response-plan.product.create', [
                                'id' => $comparisonTrademarkResult->id,
                                'trademark_plan_id' => $trademarkPlan->id,
                            ]) }}'"
                    >{{__('labels.a203.save_draft_and_next_page')}}</button>
                </li>
            @else
                <li>
                    <button type="submit" name="submit" value="save"
                            class="btn_b save @if($user->role != ROLE_MANAGER) disabled @endif"
                            @if($user->role != ROLE_MANAGER) disabled @endif>{{__('labels.a203.save_draft')}}</button>
                </li>
                <li>
                    <button type="button"  id="submit"
                            class="btn_b submit @if($user->role != ROLE_MANAGER) disabled @endif" style="font-size: 1.3em"
                            @if($user->role != ROLE_MANAGER) disabled @endif">{{__('labels.a203.save_draft_and_next_page')}}</button>
                    <input type="submit" id="input_submit" hidden name="submit" value="submit">
                </li>
            @endif
        </ul>
        <input type="hidden" name="comparison_trademark_result_id" value="{{$id}}">
    </form>
    </div>
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
        let createPlanReasonURL = '{{route('admin.refusal.response-plan.store')}}';
        let deletePlanDetailURL = '{{route('admin.refusal.response-plan.delete-plan-detail')}}';
        let deletePlanURL = '{{route('admin.refusal.response-plan.delete-plan')}}';
        let plans = @JSON($plans ?? []);
        const errorCommonE026 = '{{__('messages.general.Common_E026')}}';
        const errorValidate = '{{__('messages.general.Common_E001')}}';
        const erorrespondenceA203E001 = '{{__('messages.general.correspondence_A203_E001')}}'
        const errorCorrespondenceA203E002 = '{{__('messages.general.correspondence_A203_E002')}}';
        const errorCorrespondenceA203E003 = '{{__('messages.general.correspondence_A203_E003')}}';
        const errorCorrespondenceA203E004 = '{{__('messages.general.correspondence_A203_E004')}}';
        const CANCEL = '{{__('labels.btn_cancel')}}';
        const BACK = '{{__('labels.back')}}';
        const OK = '{{__('labels.confirm')}}';
        const OK2 = '{{__('labels.btn_ok')}}';
        const checkAll = '{{__('labels.u031b.check_all')}}';
        const titleConfirmNoTypePlan = '{{__('labels.a203.title_confirm_no_type_plan')}}';
        let redirectBack = @JSON($redirectBack);
    </script>
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/JavaScript" src="{{ asset('common/js/clsValidation.js') }}"></script>
    <script type="text/JavaScript" src="{{asset('admin_assets/plan/a203.js')}}"></script>
    <script>
        $('body').on('click', '.close-alert', function () {
            history.pushState(null, null, redirectBack);
        })
    </script>
    @if(!empty(request()->type) || $flagDisabled == true)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ROLE_MANAGER] ])
@endsection
