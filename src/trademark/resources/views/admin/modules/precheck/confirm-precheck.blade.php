@extends('admin.layouts.app')
@section('headSection')
    <style>
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

        <form method="POST" action="{{route('admin.precheck.precheck-confirm')}}">
            @csrf
            @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable
               ])
            <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->


            <h3>{{ __('labels.confirm_precheck.title') }}</h3>
            @include('admin.components.includes.messages')

            <table class="normal_b eol">
                <caption>{{ __('labels.precheck.title_confirm_1') }}</caption>
                <tr>
                    <th rowspan="2">{{ __('labels.trademark_info.distinguishing') }}</th>
                    <th rowspan="2" style="width:20em;">{{ __('labels.user_common_payment.product_service_name') }}</th>
                    <th rowspan="2">{{ __('labels.precheck.similar_group_code') }}</th>
                    <th colspan="2">{{ __('labels.confirm_precheck.discernment') }}</th>
                </tr>
                <tr>
                    <th style="width:7em">{{ __('labels.confirm_precheck.past') }}</th>
                    <th style="width:7em">{{ __('labels.confirm_precheck.this_time') }}</th>
                </tr>
                <!-- -------- -->
                <input type="hidden" name="precheck_id" value="{{$precheckPresent->id}}">
                <input type="hidden" name="trademark_id" value="{{$trademarkId}}">
             @if(count($datasUnique) > 0)
            @foreach($datasUnique as $key => $data)
                <tr>
                    <td @if(count($data['product']) > 1)rowspan={{count($data['product'])}} @endif
                     class="@if($data['product'][0]['type'] == 4 && count($data['product']) == 1) {{$data['product'][0]->getClassColorByTypeProduct()}} @endif distrinction_{{$data['codeDistriction']}}">{{ $data['codeDistriction'] }}</td>
                    @foreach($data['product'] as $keyValue => $item)
                    @if($keyValue != 0)
                        <tr>
                    @endif
                    <td class="{{ $item->getClassColorByTypeProduct() }}">{{ $item->name}}</td>
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
                            <span class="icon-add-sub add_{{$item->id}}" style="cursor: pointer">+</span><br />
                        @endif
                        <input type="hidden" name="count_code" value="{{count($item->code)}}">
                    </td>
                    <td class="{{ $item->getClassColorByTypeProduct() }} center">
                        @if(count($item->detail) >= 2 && isset($item->detail[1]) && !empty($item->detail[1]->result_identification_detail))
                            {{$item->detail[1]->getResultIdentificationDetail()}}
                            <br>{{$item->detail[1]->updated_at_convert}}
                        @elseif(count($item->detail) >= 1  && isset($item->detail[0]) && isset($item->detail[0]->result_identification_detail))
                             {{$item->detail[0]->getResultIdentificationDetail()}}
                             <br>{{$item->detail[0]->updated_at_convert}}
                       @else
                             －
                       @endif
                    </td>
                    <td class="{{ $item->getClassColorByTypeProduct() }} center">
                        @if(count($item->detailPresent) >= 1  && isset($item->detailPresent[0]))
                            {{$item->detailPresent[0]->getResultIdentificationDetail()}}
                        @else
                             －
                        @endif
                    </td>
                    @if($keyValue != 0)
                       </tr>
                     @endif
                @endforeach
                </tr>
                @endforeach
               @else
                  <tr><td colspan="5" style="text-align: center">{{__('messages.general.Common_E032')}}</td></tr>
               @endif
            </table>
            <table class="normal_b eol">
                <caption>{{ __('labels.confirm_precheck.similar_group_code_table') }}</caption>
                <tr>
                    <th rowspan="2" style="width:8em" class="middle">{{ __('labels.confirm_precheck.similar_group_code') }}</th>
                    <th rowspan="2" style="width:3em" class="middle">{{ __('labels.trademark_info.distinguishing') }}</th>
                    <th rowspan="2" style="width:20em" class="middle">{{ __('labels.user_common_payment.product_service_name') }}</th>
                    <th colspan="4">{{ __('labels.confirm_precheck.simple_survey') }}</th>
                    <th colspan="4">{{ __('labels.confirm_precheck.detailed_investigation') }}</th>
                </tr>
                <tr>
                    <th style="width:7em">{{ __('labels.confirm_precheck.past') }}</th>
                    <th style="width:7em">{{ __('labels.confirm_precheck.most_recently_displayed_result') }}</th>
                    <th style="width:7em">{{ __('labels.confirm_precheck.apply_this_time') }}</th>
                    <th style="width:7em">{{ __('labels.confirm_precheck.this_time') }}</th>
                    <th style="width:7em">{{ __('labels.confirm_precheck.past') }}</th>
                    <th style="width:7em">{{ __('labels.confirm_precheck.most_recently_displayed_result') }}</th>
                    <th style="width:7em">{{ __('labels.confirm_precheck.apply_this_time') }}</th>
                    <th style="width:7em">{{ __('labels.confirm_precheck.this_time') }}</th>
                </tr>
                @if(count($datas) > 0)
                    @foreach($datas as $key => $data)
                        <tr>
                            <td @if(count($data['product']) > 1) rowspan={{count($data['product'])}} @endif
                            class="@if(count($data['product']) == 1) {{$data['product'][0]->getClassColorByTypeProduct()}} @endif  code_{{$data['codeName']}}">{{ $data['codeName'] }}</td>
                        @foreach($data['product'] as $keyProduct => $item)

                        @if($keyProduct != 0)
                            <tr>
                        @endif
                            <td class="{{ $item->getClassColorByTypeProduct() }} center">{{ $item->mDistinction->name }}</td>
                            <td class="{{ $item->getClassColorByTypeProduct() }}">{{ $item->name }}</td>
                            @if(count($item->simple) >= 2 && isset($item->simple[1]))
                                <td class="{{ $item->getClassColorByTypeProduct() }} center">
                                    {{$item->simple[1]->getResultSimilarSimple()}}
                                    <br/> {{$item->simple[1]->updated_at_convert }}</td>
                            @else
                                <td class="{{ $item->getClassColorByTypeProduct() }} center"> － </td>
                            @endif

                            @if(count($item->simple) >= 1 && isset($item->simple[0]))
                                <td class="{{ $item->getClassColorByTypeProduct() }} center">
                                    {{$item->simple[0]->getResultSimilarSimple()}}
                                <br/> {{$item->simple[0]->updated_at_convert }}</td>
                            @else
                                <td class="{{ $item->getClassColorByTypeProduct() }} center">  － </td>
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
                            <td class="{{ $item->getClassColorByTypeProduct() }} center">{{ $item->precheckProduct[count($item->precheckProduct) -1]->is_register_product == 1 ? '✔' : ''}}
                            @if($keyProduct == 0)
                                <td class="{{ $item->getClassColorByTypeProduct() }} center"
                                    @if(count($data['product']) > 1) rowspan="{{ count($data['product']) }}" @endif
                                >
                                    @if(count($item->detailPresent) >= 1  && isset($item->detailPresent[0]) && isset($item->detailPresent[0]->result_similar_detail))
                                        {{$item->detailPresent[0]->getResultSimilarDetail()}}
                                    @else
                                    －
                                    @endif
                                </td>
                            @endif
                        @if($keyProduct != 0)
                            </tr>
                        @endif
                        @endforeach
                        </tr>
                    @endforeach
                @else
                    <tr><td colspan="3" style="text-align: center">{{__('messages.general.Common_E032')}}</td></tr>
                @endif
            </table>

            <h5>{{ __('labels.support_first_times.customer_comment') }}</h5>
            <p class="eol" style="white-space: pre">{{ $commentAMSShiki ? $commentAMSShiki->content : '' }}</p>
            <p class="eol" style="white-space: pre">{{ $commentAMSRui ? $commentAMSRui->content : '' }}</p>


            <h5>{{ __('labels.create_support_first_time.internal_comment') }}</h5>
            @if(count($precheckCommentInternal) > 0)
                @foreach($precheckCommentInternal as $item)
                <p class="eol" style="white-space: pre">{{ $item->content }}</p>
                @endforeach
             @endif
            <p class="eol" style="text-decoration: underline;"><a class="a-hover open-modal">{{ __('labels.confirm_precheck.same_screen_as_customer') }}</a></p>

            <ul class="footerBtn clearfix">
                <li><a class="btn_custom" style="font-size: 1.3em;" href="{{route('admin.home')}}">{{__('labels.back')}}</a></li>
                <li><input type="submit" value="{{ __('labels.support_first_times.display_customer') }}" class="btn_c" /></li>
                <li><a class="btn_custom" href="{{route('admin.precheck_select.show-edit-precheck-unique', [
                   'id' => $trademarkId,
                   'precheck_id' => $precheckPresent->id ])}}">{{ __('labels.support_first_times.fix') }}</a></li>
            </ul>


        </form>

    </div><!-- /contents inner -->

</div><!-- /contents -->

@endsection
@section('footerSection')
    <script src="{{ asset('admin_assets/precheck/open_modal.js') }}"></script>
    <script type="text/JavaScript">
        const openModalUrl = '{{ route('admin.precheck.open-modal') }}';
        const id = @JSON($trademarkId);
        let datasSetColorByDistrictionGroup = @JSON($datasUnique);
        let datasSetColorByCodeGroup = @JSON($datas);
        let datas = @JSON($datas);
        let dataIdentification = [];
        let precheckPresent = @json($precheckPresent);
        let precheckPresentId = @json($precheckPresent->id);
        let contentModal = '{{__('messages.general.Common_E035')}}';
        let NO = '{{__('labels.back')}}';
        let urlBackToTop = '{{ route('admin.home') }}';
    </script>
    <script>
        function disableInput() {
            const form = $('form');
            form.find('a').attr('href', 'javascript:void(0)')
            form.find('a').attr('target', '')
            form.find('a, input, button, textarea, select').prop('disabled', true)
            form.find('a, input, button, textarea, select').addClass('disabled')
            form.find('.btn_a, .close-alert').removeClass('disabled').prop('disabled', false)
        }
    </script>
    @if($flagDisabled == true)
        <script>
            disableInput();
            $('.submit, .save, .add_plan_detail, .add_reciprocal_countermeasures').remove();
        </script>
    @endif
    <script src="{{ asset('admin_assets/precheck/set_color_by_code_group.js') }}"></script>
    <script src="{{ asset('admin_assets/precheck/set_color_by_distriction_group.js') }}"></script>
    <script src="{{ asset('admin_assets/precheck/check-unique/action.js') }}"></script>
    @if($precheckPresent->is_confirm == IS_CONFIRM_TRUE)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', [ 'only' => [ ROLE_SUPERVISOR ] ])
@endsection
