@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">

        <!-- contents inner -->
        <div class="wide clearfix">

            <form name="frmCreateCM" id="form" method="POST" action="{{route('admin.precheck.create-precheck-result')}}">
                @csrf
                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable
                ])
                <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->

                <input type="hidden" name="trademark_id" value="{{$id}}"/>
                <h3>{{__('labels.precheck.title1')}}</h3>
                <input type="hidden" name="precheck_id" value="{{$precheckPresent}}">
                @include('admin.components.includes.messages')
                <table class="normal_b mb20">
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
                                <td @if(count($data['product']) > 1) rowspan="{{count($data['product'])}}" @endif
                                    class="@if(count($data['product']) == 1 ) {{$data['product'][0]->getClassColorByTypeProduct()}} @endif center code_{{$data['codeName']}}"
                                >{{$data['codeName']}}</td>

                                @foreach($data['product'] as $key => $item)
                                    @if($key != 0)
                                    <tr>
                                    @endif
                                        <td class="{{ $item->getClassColorByTypeProduct() }} center">{{$item->mDistinction->name}}</td>
                                        <td class="{{ $item->getClassColorByTypeProduct() }}">{{$item->name}}</td>
                                        @if(count($item->simple) >= 2 && isset($item->simple[1]))
                                            <td class="{{ $item->getClassColorByTypeProduct() }} center">
                                                {{$item->simple[1]->getResultSimilarSimple()}}
                                                <br/> {{$item->simple[1]->updated_at_convert }}</td>
                                        @else
                                            <td class="{{ $item->getClassColorByTypeProduct() }} center"> －</td>
                                        @endif
                                        @if(count($item->simple) >= 1 && isset($item->simple[0]))
                                            <td class="{{ $item->getClassColorByTypeProduct() }} center">
                                                {{$item->simple[0]->getResultSimilarSimple()}}
                                                <br/> {{$item->simple[0]->updated_at_convert }}</td>
                                        @else
                                            <td class="{{ $item->getClassColorByTypeProduct() }} center"> －</td>
                                        @endif

                                        <td class="{{ $item->getClassColorByTypeProduct() }} center">
                                            @if(($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product) === 0)
                                                <span></span>
                                            @else
                                                <span>✔</span>
                                            @endif
                                        </td>
                                        <td class="{{ $item->getClassColorByTypeProduct() }} center">
                                            @if(($item->precheckProduct[count($item->precheckProduct) -1]->is_register_product) === 0)
                                                <span>－</span>
                                            @else
                                                <input type="hidden" name="m_code_id[]" value="{{$item->m_code_id}}">
                                                <input type="hidden" name="m_product_id[]" value="{{$item->id}}">
                                                <input type="hidden" name="precheck_product_id[]"
                                                       value="{{$item->precheckProduct[count($item->precheckProduct) -1]->id}}">
                                                <input type="hidden" name="is_register_product[]"
                                                       value="{{$item->precheckProduct[count($item->precheckProduct) -1]->is_register_product}}">
                                                <input type="hidden" name="result_similar_simple[]"
                                                       value="{{$item['result_confirm']}}">

                                                @if(isset($item['result_confirm']))
                                                    @if($item['result_confirm'] == 1)
                                                        <span>{{__('labels.precheck.yes')}}</span>
                                                    @else
                                                        <span>{{__('labels.precheck.no')}}</span>
                                                    @endif
                                                @else
                                                    <span>-</span>
                                                @endif
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
                            <td colspan="7"
                                style="text-align: center">{{__('messages.general.Common_E032')}}</td>
                        </tr>
                    @endif
                </table>
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
                                    <td class="center">{{$item['distinction_name']}}</td>
                                    <td>{{$item['product_name']}}</td>
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
                    @endforeach
                </p>

                <h5>{{__('labels.precheck.comment_send_customers')}}</h5>
                <p class="eol">
                    <span style="white-space: pre">{{$precheckCommentInternal ? $precheckCommentInternal : ''}}</span>
                    <input type="hidden" class="normal" name="content1"
                           placeholder="{{__('labels.precheck.comment_customers_can_see')}}"
                           value="{{$precheckCommentInternal ? $precheckCommentInternal : ''}}"/>
                    <div class="errormsgContent1"></div>
                </p>

                <h5>{{__('labels.precheck.comment_internal')}}</h5>
                <p class="eol">
                    <span style="white-space: pre">{{$precheckCommentCustomer ? $precheckCommentCustomer : ''}}</span>
                    <input type="hidden" class="normal" name="content2"
                           value="{{$precheckCommentCustomer ? $precheckCommentCustomer : ''}}"
                           placeholder="{{__('labels.precheck.comment_customers_cannot_see')}}"/>
                    <div class="errormsgContent2"></div>
                </p>
                <ul class="footerBtn clearfix">
                    <li>
                        <a class="btn_custom" href="{{ route('admin.precheck.view-precheck-simple', ['id' => $id, 'precheck_id' => $precheckIdPresent]) }}">{{__('labels.back')}}</a>
                    </li>
                    <li>
                        <button type="submit" name="submit" value="{{ SEND_TO_USER }}" class="btn_c confirm"
                                style="font-size: 1.3em;">{{__('labels.precheck.show_confirm_to_user')}}</button>
                    </li>
                </ul>
            </form>

        </div><!-- /contents inner -->

    </div><!-- /contents -->

    </div><!-- /wrapper -->

@endsection
@section('footerSection')
    <script type="text/javascript" src="{{asset('admin_assets/themes/plugins/jquery-validation/jquery.validate.js')}}"></script>
    <script>
        const errorMessageContentMaxLength = '{{ __('messages.common.errors.Common_E026') }}';
        const errorMessageRequiredResultSimilarSimple = '{{ __('messages.common.errors.Common_E025') }}';
        const datasSetColorByCodeGroup = @json($datas);
        const datas = @json($datas);
        const CREATE = '{{ CREATE }}';
        const CONFIRM = '{{ CONFIRM }}';
    </script>
    <script src="{{ asset('admin_assets/precheck/simple/validate.js') }}"></script>
    <script src="{{ asset('admin_assets/precheck/set_color_by_code_group.js') }}"></script>
@endsection
