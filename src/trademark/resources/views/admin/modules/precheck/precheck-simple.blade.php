@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">

        <!-- contents inner -->
        <div class="wide clearfix">

            <form name="frmCreateCM" id="form" method="POST" action="{{route('admin.precheck.create-precheck-result')}}">
                @csrf
                @include('admin.components.includes.messages')
                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable
                ])
                <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->

                <h3>{{__('labels.precheck.title1')}}</h3>
                <input type="hidden" name="trademark_id" value="{{$id}}">
                <input type="hidden" name="precheck_id" value="{{$precheckPresent->id}}">
                <table class="normal_b product-list">
                    <tr>
                        <th rowspan="2" style="width:8em"
                            class="middle">{{__('labels.precheck.similar_group_code')}}</th>
                        <th rowspan="2" style="width:3em" class="middle">{{__('labels.precheck.distribute')}}</th>
                        <th rowspan="2" style="width:20em"
                            class="middle">{{__('labels.precheck.product_service_name')}}</th>
                        <th colspan="4">{{__('labels.precheck.simple_survey')}}</th>
                    </tr>
                    <tr>
                        <th style="width:7em">{{__('labels.precheck.passed')}}</th>
                        <th style="width:7em">{{__('labels.precheck.most_recently_displayed_results')}}</th>
                        <th style="width:7em">{{__('labels.precheck.apply_this_time')}}</th>
                        <th style="width:7em">{{__('labels.precheck.this_time')}}</th>
                    </tr>
                    @if(count($datas) > 0)
                        @foreach($datas as $key => $data)
                            <tr>
                                <td
                                    @if(count($data['product']) > 1) rowspan="{{count($data['product'])}}" @endif
                                class="@if(count($data['product']) == 1) {{ $data['product'][0]->getClassColorByTypeProduct() }}  @endif center code_{{$data['codeName']}}">{{$data['codeName']}}</td>
                                @foreach($data['product'] as $key => $item)
                                    @if($key != 0)
                                    <tr>
                                    @endif
                                        <td class="{{$item->getClassColorByTypeProduct()}} center">{{$item->mDistinction->name}}</td>
                                        <td class="{{$item->getClassColorByTypeProduct()}}">{{$item->name}}</td>

                                        @if(count($item->simple) >= 2 && isset($item->simple[1]))
                                            <td class="{{$item->getClassColorByTypeProduct()}} center">
                                                {{$item->simple[1]->getResultSimilarSimple()}}
                                                <br/> {{$item->simple[1]->updated_at_convert }}</td>
                                        @else
                                            <td class="{{$item->getClassColorByTypeProduct()}} center"> －</td>
                                        @endif

                                        @if(count($item->simple) >= 1 && isset($item->simple[0]))
                                            <td class="{{$item->getClassColorByTypeProduct()}} center">
                                                {{$item->simple[0]->getResultSimilarSimple()}}
                                                <br/> {{$item->simple[0]->updated_at_convert }}</td>
                                        @else
                                            <td class="{{$item->getClassColorByTypeProduct()}} center"> －</td>
                                        @endif

                                        <td class="{{$item->getClassColorByTypeProduct()}} center">
                                            @if(($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product) === 0)
                                                <span></span>
                                            @else
                                                <span>✔</span>
                                            @endif
                                        </td>

                                        <td class="{{$item->getClassColorByTypeProduct()}} center">
                                            @if(($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product) === 0)
                                                <span>－</span>
                                            @else
                                                <input type="hidden" name="m_code_id[]" value="{{$item->m_code_id}}">
                                                <input type="hidden" name="precheck_product_id[]"
                                                       value="{{$item->precheckProduct[count($item->precheckProduct) -1]->id}}">
                                                <input type="hidden" name="is_register_product[]"
                                                       value="{{$item->precheckProduct[count($item->precheckProduct) -1]->is_register_product}}">
                                                <input type="hidden" name="m_product_id[]" value="{{$item->id}}">
                                                <select
                                                    name="result_similar_simple[]"
                                                    @if(isset($item->result_confirm)) value="{{$item->result_confirm}}" @endif
                                                >
                                                    <option value="" @if((isset($item->result_confirm) && $item->result_confirm == null) ||
                                                    count($item->simplePresent) >= 1 && isset($item->simplePresent[0]) && $item->simplePresent[0]->result_similar_simple == 1) selected @endif></option>
                                                    <option value="1" @if((isset($item->result_confirm) && $item->result_confirm == 1) ||
                                                    count($item->simplePresent) >= 1 && isset($item->simplePresent[0]) && $item->simplePresent[0]->result_similar_simple == 1) selected @endif>{{__('labels.precheck.yes')}}</option>
                                                    <option value="2" @if((isset($item->result_confirm) && $item->result_confirm == 2) ||
                                                    count($item->simplePresent) >= 1 && isset($item->simplePresent[0]) && $item->simplePresent[0]->result_similar_simple == 2) selected @endif>{{__('labels.precheck.no')}}</option>
                                                </select>
                                                <div class="errormsg"></div>
                                            @endif
                                        </td>
                                    @if($key != 0)
                                    </tr>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" style="text-align: center">{{__('messages.general.Common_E032')}}</td>
                        </tr>
                    @endif
                </table>

                <div class="mb20"></div>

                <!-- /table 区分、商品・サービス名 1 -->

                <h3>{{__('labels.precheck.title2')}}</h3>

                <table class="normal_b mb10">
                    <tr>
                        <th style="width:8em">{{__('labels.precheck.similar_group_code')}}</th>
                        <th style="width:3em">{{__('labels.precheck.distribute')}}</th>
                        <th style="width:40em">{{__('labels.precheck.product_service_name')}}</th>
                    </tr>
                    @if(count($dataNotRegister) > 0)
                        @foreach($dataNotRegister as $key => $data)
                            <tr>
                                <td @if(count($data) > 1) rowspan="{{count($data)}}" @endif class="center">{{$key}}</td>
                                @foreach($data as $keyProduct => $item)
                                    <td class="center">{{$item->distinction_name}}</td>
                                    <td>{{$item->product_name}}</td>
                            </tr>
                            @endforeach
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="3" style="text-align: center">{{__('messages.general.Common_E032')}}</td>
                        </tr>
                    @endif
                </table>

                <h5>{{__('labels.precheck.for_copy')}}</h5>
                <p class="eol">
                    @foreach($dataRegister as $key => $item)
                        {{$item['code_name']}}
                    @endforeach</p>

                <h5>{{__('labels.precheck.comment_send_customers')}}</h5>
                <p class="eol">
                    {{--                    <textarea class="normal" name="content1" placeholder="{{__('labels.precheck.comment_customers_can_see')}}">{{$precheckCommentInternal ? $precheckCommentInternal->content : ''}}</textarea>--}}
                    <textarea class="normal" name="content1"
                              placeholder="{{__('labels.precheck.comment_customers_can_see')}}">{!!$precheckCommentInternal ? $precheckCommentInternal->content : ''!!}</textarea>
                </p>

                <h5>{{__('labels.precheck.comment_internal')}}</h5>
                <p class="eol">
                    <textarea class="normal" name="content2"
                              placeholder="{{ __('labels.precheck.comment_customers_cannot_see')}}">{!! $precheckCommentCustomer ? $precheckCommentCustomer->content : '' !!}</textarea>
                </p>

                <ul class="footerBtn clearfix">
                    <li><a class="btn_custom" href="{{route('admin.home')}}">{{__('labels.back')}}</a></li>
                    <li>
                        <button type="submit" name="submit" value="{{CREATE}}" class="btn_b"
                                style="font-size: 1.3em;">{{__('labels.save')}}</button>
                    </li>
                    <li>
                        <button type="submit" name="submit" value="{{CONFIRM}}" class="btn_c"
                                style="font-size: 1.3em;">{{__('labels.precheck.confirm')}}</button>
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
        const datas = @json($datas);
        const datasSetColorByCodeGroup = @json($datas);
        const precheckPresent = @json($precheckPresent);
        const contentModal = '{{__('messages.general.Common_E035')}}';
        const NO = '{{__('labels.back')}}';
        const CREATE = '{{ CREATE }}';
        const CONFIRM = '{{ CONFIRM }}';
    </script>
    <script src="{{ asset('admin_assets/precheck/simple/validate.js') }}"></script>
    <script src="{{ asset('admin_assets/precheck/set_color_by_code_group.js') }}"></script>
    @if($precheckPresent->flag_role == FLAG_ROLE)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ ROLE_MANAGER ] ])
@endsection
