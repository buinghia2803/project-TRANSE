@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">

        <!-- contents inner -->
        <div class="wide clearfix">
            <form id="form" method="POST" action="{{route('admin.update.document.modification.product.document.post', ['id' => $registerTrademark->id])}}">
                @csrf
                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable
                ])
                <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->

                <h3>{{__('labels.u402hosoku02.title')}}</h3>

                <dl class="w16em clearfix">

                    <dt>{{__('labels.u402hosoku02.name_file')}}</dt>
                    <dd>{{__('labels.u402hosoku02.name_file_2')}}</dd>

                    <dt>{{__('labels.u402hosoku02.title_1')}}</dt>
                    <dd>{{isset($trademark) ? $trademark->trademark_number : ''}}</dd>

                    <dt>{{__('labels.u402hosoku02.title_2')}}</dt>
                    <dd>{{\App\Helpers\CommonHelper::formatTime($registerTrademark->filing_date)}}</dd>

                    <dt>{{__('labels.u402hosoku02.title_3')}}</dt>
                    <dd>{{__('labels.u402hosoku02.title_4')}}</dd>

                </dl>

                <h4>{{__('labels.u402hosoku02.title_5')}}</h4>
                <dl class="w16em clearfix">

                    <dt>{{__('labels.u402hosoku02.title_6')}}</dt>
                    <dd> @if(!empty($registerTrademark->register_number)) {{__('labels.u402hosoku02.title_14', ['attr' => $registerTrademark->register_number])}} @endif</dd>

                </dl>

                <h4>{{__('labels.u402hosoku02.title_7')}}</h4>

                <dl class="w16em clearfix">

                    <dt>{{__('labels.u402hosoku02.title_8')}}</dt>
                    <dd>
                        @if($registerTrademark->trademark_info_nation_id == NATION_JAPAN_ID)
                        {{$registerTrademark->prefecture->name . ' ' .$registerTrademark->trademark_info_address_second . ' '}}
                        @endif
                        {{ $registerTrademark->trademark_info_address_three }}
                    </dd>

                    <dt>{{__('labels.u402hosoku02.title_9')}}</dt>
                    <dd>{{ isset($registerTrademark) ? $registerTrademark->trademark_info_name : '' }}</dd>

                </dl>

                <h4>{{__('labels.u402hosoku02.title_10')}}</h4>
                <dl class="w16em clearfix">
                    <dt>{{__('labels.u402hosoku02.title_11')}}</dt>
                    <dd>
                        @if(count($agentGroupMaps) > 0)
                        @foreach($agentGroupMaps as $agentGroupMap)
                            {{$agentGroupMap->agent->identification_number}}<br />
                        @endforeach
                            @endif
                    </dd>

                    <dt>{{__('labels.u402hosoku02.title_12')}}</dt>
                    <dd></dd>

                    <dt>{{__('labels.u402hosoku02.title_13')}}</dt>
                    <dd>
                        @foreach($agentGroupMaps as $agentGroupMap)
                            {{$agentGroupMap->agent->name}}<br />
                        @endforeach
                    </dd>

                </dl>
                <dl class="w16em clearfix">

                    <dt>{{__('labels.u402hosoku02.title_15')}}</dt>
                    <dd>{{__('labels.u402hosoku02.title_16')}}</dd>

                    <dt>{{__('labels.u402hosoku02.title_17')}}</dt>
                    <dd>{{__('labels.u402hosoku02.title_18')}}</dd>

                </dl>

                <p>{{__('labels.u402hosoku02.title_19')}}</p>
                <dl class="w16em clearfix">

                    <dt>{{__('labels.u402hosoku02.title_20')}}</dt>
                    <dd>{{__('labels.u402hosoku02.title_21')}}</dd>

                </dl>

                <h4>{{__('labels.u402hosoku02.title_22')}}</h4>
                <dl class="w16em eol clearfix">
                    <dt>{{__('labels.u402hosoku02.title_23')}}</dt>
                    <dd>
                        @if($registerTrademark->trademark_info_nation_id == NATION_JAPAN_ID)
                            {{$registerTrademark->prefecture->name . ' ' .$registerTrademark->trademark_info_address_second . ' '}}
                        @endif
                        {{ $registerTrademark->trademark_info_address_three }}
                    </dd>

                    <dt>{{__('labels.u402hosoku02.title_24')}}</dt>
                    <dd>{{ isset($registerTrademark) ? $registerTrademark->trademark_info_name : '' }}</dd>

                    <dt>{{__('labels.u402hosoku02.title_25')}}</dt>
                    <dd>{{ isset($registerTrademark) ? $registerTrademark->representative_name : '' }}</dd>

                </dl>


                <ul class="footerBtn clearfix">
                    <li><button name="submit" type="submit" value="{{BACK_URL}}" class="btn_a" >{{__('labels.u402hosoku02.back_url')}}</button></li>
                    <li><button name="submit" type="submit" value="{{SUBMIT}}" class="btn_b" >{{__('labels.u402hosoku02.submit')}}</button></li>
                </ul>

            </form>

        </div><!-- /contents inner -->

    </div><!-- /contents -->
@endsection
@section('css')
    <style>
        .disabled-tag-a {
            pointer-events: none;
            cursor: default;
        }
    </style>
@endsection
@section('script')
    @include('compoments.readonly', ['only' => [ROLE_OFFICE_MANAGER], 'hasRemoveSubmit' => false])
    @if(isset($registerTrademark) && $registerTrademark->is_confirm == IS_CONFIRM_TRUE)
    <script>
        $('#form').find('button').each(function (key, item) {
            $(item).attr('disabled', 'disabled')
            $(item).addClass('disabled')
        })
    </script>
    @endif
@endsection
