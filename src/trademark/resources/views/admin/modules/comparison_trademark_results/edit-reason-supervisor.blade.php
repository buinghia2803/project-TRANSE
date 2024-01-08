@extends('admin.layouts.app')

@section('main-content')
    <div id="contents">

        <!-- contents inner -->
        <div class="wide clearfix">

            <form id="form" method="POST" action="{{route('admin.refusal.eval-report.edit-reason.supervisor.post')}}">

                @csrf
                @include('admin.components.includes.messages')
                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable
                ])
                <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->

                <table class="normal_a eol">
                    <caption>{{__('labels.refusal.eval_report_create_reason.rejection_notification_response')}}</caption>
                    <tr>
                        <th>{{__('labels.refusal.eval_report_create_reason.date_notification_response')}}</th>
                        <td>
                            {{\App\Helpers\CommonHelper::formatTime($comparisonTrademarkResult->sending_noti_rejection_date, 'Y/m/d')}}
                            <input type="button" value="{{__('labels.maching_results.btn_1')}}" class="btn_b" id="click_file_pdf">
                            @if(count($trademarkDocuments) > 0)
                                @foreach ($trademarkDocuments as $ele_a)
                                    <a hidden href="{{ asset($ele_a) }}" class="click_ele_a" target="_blank">{{$ele_a}}</a>
                                @endforeach
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{__('labels.refusal.eval_report_create_reason.time_limit_reply_notice')}}</th>
                        <td>{{\App\Helpers\CommonHelper::formatTime($comparisonTrademarkResult->response_deadline, 'Y/m/d')}}</td>
                    </tr>
                </table>

                <h3>{{__('labels.refusal.eval_report_create_reason.human_reply_response_notice_v2')}}</h3>

                {{-- if plan_correspondences.type != 1|3(simple|pack C) then hidden response_deadline field  --}}
                @if($reasonNo->response_deadline != null &&
                    !in_array($planCorrespondence->type, [App\Models\PlanCorrespondence::TYPE_1, App\Models\PlanCorrespondence::TYPE_3]))
                    <div class="parent_response_deadline">
                        {{__('labels.refusal.eval_report_create_reason.time_reply_response_notice_of_customer')}}
                        <input type="date"
                               class="em10 response_deadline"
                               value="{{$reasonNo ? $reasonNo->response_deadline : ''}}"
                               name="response_deadline"
                               placeholder="カレンダー選択"/>
                        <div class="error-register"></div>
                    </div>
                @endif
                <input type="hidden" name="plan_correspondence_id" value="{{$planCorrespondence ? $planCorrespondence->id : 0}}">
                <input type="hidden" name="reason_no_id" value="{{$reasonNo ? $reasonNo->id : 0}}">
                <br/>
                <br/>
                <p>{{__('labels.refusal.eval_report_create_reason.note_number_reason_if')}}</p>
                <br/>
                <dl class="w10em clearfix">
                    <dt>{{__('labels.refusal.eval_report_create_reason.number_reason')}}</dt>
                    <dd>
                        <div class="parent_change_reason">
                            <select class="change_reason mr-4" name="reason_number" style="margin-right: 20px">
                                @for($i = 0; $i <= 9; $i ++)
                                    @if($i == 0)
                                        <option value="{{$i}}" @if(isset($reasonNo) && $reasonNo->reason_number == $i) selected @endif>{{__('labels.refusal.eval_report_create_reason.option')}}</option>
                                    @else
                                        <option value="{{$i}}" @if(isset($reasonNo) && $reasonNo->reason_number == $i) selected @endif>{{$i}}</option>
                                    @endif
                                @endfor
                            </select>
                            <input type="button" value="{{__('labels.refusal.eval_report_create_reason.btn_1')}}" class="btn_a open_modal_add_reason" @if(!$reasonNo) disabled @endif>
                        </div>
                    </dd>
                </dl>

                <br />
                <br />

                <dl class="w10em clearfix">
                    <dt>{{__('labels.refusal.eval_report_create_reason.reason_for_branch')}}</dt>
                    <dd>
                        <select class="change_reason_branch" name="reason_branch_number">
                            @for($i = 0; $i <= 9; $i ++)
                                @if($i == 0)
                                    <option value="{{$i}}" @if(isset($reasonNo) && (int)$reasonNo->reason_branch_number == $i) selected @endif>{{__('labels.refusal.eval_report_create_reason.no_branch')}}</option>
                                @else
                                    <option value="{{$i}}" @if(isset($reasonNo) && (int)$reasonNo->reason_branch_number == $i) selected @endif>{{__('labels.refusal.eval_report_create_reason.branch')}} {{$i}}</option>
                                @endif
                            @endfor
                        </select>
                        　
                        <input type="button" value="{{__('labels.refusal.eval_report_create_reason.accept_branch')}}" class="btn_a open_modal_add_reason_branch">
                    </dd>
                </dl>
                <br />
                <br />

                <ul class="btn_left">
                    <li><input type="submit" value="{{__('labels.refusal.eval_report_create_reason.present_db')}}" class="btn_a disabled" disabled /><br /></li>
                </ul>
                <br />
                <ul class="btn_left eol">
                    <li><a  class="btn_custom" href="{{route('admin.goods-master-search')}}" target="_blank">{{__('labels.product_master')}}</a></li>
                </ul>
                <div class="reasons">
                    @foreach($reasons as $key => $reason)
                        @php
                            $reference_number = json_decode($reason->reference_number);
                        @endphp
                        <hr />
                        <dl class="w08em clearfix">
                            <dt>理由</dt>
                            <dd>
                                <span class="reason_name_label">{{$reason->reason_name}}</span>
                                <input type="hidden" name="reason_name[{{$key}}]" value="{{$reason->reason_name}}">
                                <input type="hidden" name="reason_id[{{$key}}]" value="{{$reason->id}}">
                            </dd>
                            <dt>　法令</dt>
                            <dd class="parent_law_regulation">
                                <select name="m_laws_regulation_id[{{$key}}]" data-i="{{$key}}" class="m_law_regulation" >
                                    @foreach($mLawsRegulations as $mLawsRegulation)
                                        <option value="{{$mLawsRegulation->id}}" @if($reason->m_laws_regulation_id == $mLawsRegulation->id) selected @endif>{{$mLawsRegulation->name}}</option>
                                    @endforeach
                                </select>
                            </dd>
                            @php
                                $count = 0;
                            @endphp
                            @if(is_array($reference_number))
                                @foreach($reference_number as $keyReferenceNumber => $value)
                                    <dt class="title_reference_number title_add_row_reason_{{$keyReferenceNumber}}">　{{__('labels.refusal.eval_report_create_reason.number_of_citations')}}</dt>
                                    <dd class="append_reference_number input_row_reason_{{$keyReferenceNumber}}">
                                        <input type="text" name="reference_number[{{$key}}][]" class="reference_number" value="{{$value}}"/>
                                        @if($count !== 0)
                                            <a class="delete note delete_row delete_row_{{$count}}" href="javascript:;" data-delete-key="{{$key}}" data-delete-count="{{$count}}">×{{__('labels.delete')}}</a>
                                        @endif
                                    </dd>
                                    @php
                                        $count ++;
                                    @endphp
                                @endforeach
                                <a href="javascript:;" class="add_row_reason add_row_reason_${i}" data-i="{{$key}}">{{__('labels.refusal.eval_report_create_reason.add')}} +</a><br /></dd>
                            @endif
                        </dl>
                    @endforeach
                </div>
                <br />
                <br />
                <table class="normal_b eol" style="width:50%;">
                    <tr>
                        <th width="18%">{{__('labels.refusal.eval_report_create_reason.distinct')}}</th>
                        <th>{{__('labels.refusal.eval_report_create_reason.product_name')}}</th>
                        <th width="12%">{{__('labels.refusal.eval_report_create_reason.evaluation_report')}}</th>
                        <th width="12%">{{__('labels.refusal.eval_report_create_reason.code_distinction')}}</th>
                    </tr>
                    @foreach($productGroupByDistinctions as $keyproductGroupByDistinction => $products)
                        <tr>
                            <td @if(count($products) > 1) rowspan="{{count($products)}}" @endif class="left">{{ __('labels.apply_trademark._table_product_choose.distinctions', ['distinction' => $keyproductGroupByDistinction]) }}</td>
                        @foreach($products as $keyProduct => $product)
                            @if($keyProduct != 0)
                                <tr>
                                    @endif
                                    <td>{{$product->name}}</td>
                                    <td>
                                        @if($product->planCorrespondence->type == 1)
                                            {{__('labels.refusal.eval_report_create_reason.simple')}}
                                        @elseif ($product->planCorrespondence->type == 2)
                                            @if($product->plan_correspondence_prod->is_register == 1)
                                                {{__('labels.refusal.eval_report_create_reason.request')}}
                                            @else
                                                {{__('labels.refusal.eval_report_create_reason.no')}}
                                            @endif
                                        @else
                                            {{__('labels.refusal.eval_report_create_reason.pack_c')}}
                                        @endif
                                    </td>
                                    <td style="min-width: 150px"  class="parent_code">
                                        @if(count($product->code) <= 0)
                                            <div class="abc">
                                                <input type="text" class="em06 code_name" name="code_name[{{$keyproductGroupByDistinction}}_{{$product->id}}][]">
                                                <input type="hidden" name="product_type[{{$keyproductGroupByDistinction}}_{{$product->id}}]" value="{{$product->type}}">
                                                <input type="hidden" name="product_id[{{$keyproductGroupByDistinction}}_{{$product->id}}]" value="{{$product->id}}">
                                                {{--                                    <input type="hidden" name="m_code_id[{{$keyproductGroupByDistinction}}_{{$product->id}}][]" value="{{$code->id}}">--}}
                                            </div>
                                            <a href="javascript:;" class="create_row_code" data-product-key="{{$keyproductGroupByDistinction}}_{{$product->id}}" data-product-id="{{$product->id}}">追加 +</a>
                                        @else
                                            @foreach($product->code as $key => $code)
                                                @if($product->type == 3 || $product->type == 4)
                                                    <div class="abc">
                                                        <input type="text" value="{{$code->name}}" class="em06 code_name" name="code_name[{{$keyproductGroupByDistinction}}_{{$product->id}}][]">
                                                        @if($key != 0) <a class="delete delete_row_code delete_row_code_{{$key}}" data-delete-count="{{$key}}" href="javascript:;">×<br></a>@endif
                                                    </div>
                                                    <input type="hidden" name="product_type[{{$keyproductGroupByDistinction}}_{{$product->id}}][]" value="{{$product->type}}">
                                                    <input type="hidden" name="m_code_id[{{$keyproductGroupByDistinction}}_{{$product->id}}][]" value="{{$code->id}}">
                                                @else
                                                    {{$code->name}}
                                                    <input type="hidden" name="m_code_id[{{$keyproductGroupByDistinction}}_{{$product->id}}][]" value="{{$code->id}}">
                                                    <input type="hidden" name="product_type[{{$keyproductGroupByDistinction}}_{{$product->id}}][]" value="{{$product->type}}">
                                                    <input type="hidden" value="{{$code->name}}" class="em06 code_name" name="code_name[{{$keyproductGroupByDistinction}}_{{$product->id}}][]">
                                                @endif
                                                <input type="hidden" name="product_id[{{$keyproductGroupByDistinction}}_{{$product->id}}]" value="{{$product->id}}">
                                            @endforeach
                                            {{--                                <input type="hidden" name="product_type[{{$keyproductGroupByDistinction}}_{{$product->id}}]" value="{{$product->type}}">--}}
                                            <a href="javascript:;" class="create_row_code"
                                               data-product-key="{{$keyproductGroupByDistinction}}_{{$product->id}}"
                                               data-product-id="{{$product->id}}"
                                               data-product-type="{{$product->type}}">  {{__('labels.refusal.eval_report_create_reason.add2')}} +</a>
                                        @endif
                                    </td>
                                    @if($keyProduct != 0)
                                </tr>
                                @endif
                                @endforeach
                                </tr>
                                @endforeach
                </table>
                <input type="hidden" name="id" value="{{$comparisonTrademarkResult->id}}">

                <ul class="footerBtn clearfix">
                    <li><a class="btn_custom" href="{{route('admin.home')}}">{{__('labels.back')}}</a></li>
                    @if(isset($reasonNo) && $reasonNo->is_confirm == IS_CONFIRM_TRUE)
                        <li><input type="button" value="{{__('labels.refusal.eval_report_create_reason.page2')}}" class="btn_c no_disabled"
                            onclick="window.location = '{{ route('admin.refusal.eval-report.edit-examine.supervisor', [
                                'id' => $comparisonTrademarkResult->id,
                                'reason_no_id' => $reasonNo->id,
                            ]) }}'"
                        /></li>
                    @endif
                    <li><input type="submit" value="{{__('labels.refusal.eval_report_create_reason.page2')}}" class="btn_c submit"/></li>
                    <div class="warning-submit red mt-2" style="margin-left: 130px"></div>
                </ul>
            </form>
        </div>
    </div>
@endsection
@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script>
        const CANCEL = '{{__('labels.btn_cancel')}}'
        const OK = '{{__('labels.btn_ok')}}'
        const reasonNumber = @json($reasonNo ? $reasonNo->reason_branch_number : '');
        const messTime = '{{__('messages.a201.mess_time')}}';
        const errorMessageRequired = '{{__('messages.common.errors.Common_E001')}}';
        const errorMessageFullWidth = '{{__('messages.general.Common_E021')}}';
        const MessageE035 = '{{__('messages.general.Common_E035')}}';
        const errorCommonE025 = '{{__('messages.general.Common_E025')}}'
        const errorCommonE38 = '{{__('messages.general.Common_E038')}}';
        const errorCodeIsNotValid = '{{__('messages.general.support_A011_E003')}}';
        let NO = '{{__('labels.back')}}';
        let urlBackToTop = '{{ route('admin.home') }}';
        let mLawsRegulation = @JSOn($mLawsRegulations);
        let reasonNo = @JSON($reasonNo);
        let sendingNotiRejectionDate = @JSON($comparisonTrademarkResult->sending_noti_rejection_date);
        let planCorrespondence = @JSON($planCorrespondence);
        let planCorrespondenceType2 = '{{ \App\Models\PlanCorrespondence::TYPE_2 }}';
        let responseDeadline = @JSON($comparisonTrademarkResult->response_deadline);
        let warningDontSubmit = '{{ __('messages.a201.warning_dont_submit') }}';
        let warningDuplicateLawRegulations = '{{ __('messages.a201.warning_duplicate_law_regulations') }}';
        let warningChangeReason = '{{ __('messages.a201.warning_change_reason') }}';
        const messagesTitle = '{{ __('messages.a201.title1') }}';
    </script>
    <script type="text/JavaScript" src="{{asset('admin_assets/comparison-trademark-result/create-reason.js')}}"></script>
    @if(isset($reasonNo) && $reasonNo->is_confirm == IS_CONFIRM_TRUE)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ ROLE_SUPERVISOR ] ])
@endsection
