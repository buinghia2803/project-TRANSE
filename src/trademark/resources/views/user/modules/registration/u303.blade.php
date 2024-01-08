@extends('user.layouts.app')
@section('main-content')
        <!-- contents -->
        <div id="contents" class="normal">


            <h2>登録手続き完了のお知らせ</h2>


            <form  id="form" method="POST" action="{{route('user.registration.document.completed.post', $trademark->id)}}">
                @csrf
                <input type="hidden" name="trademark_id" value="{{$trademark->id}}">
                <input type="hidden" name="register_trademark_id" value="{{$registerTrademark->id}}">
                <p class="eol">登録手続きが完了しました。以下の書類をご確認ください。<br />
                    登録証が届き次第、ご連絡致します。</p>

                <h3>{{__('labels.form_trademark_information.title')}}</h3>

                <ul class="btn_left">
                    @if(count($trademarkDocumentType6) > 0)
                    <li>
                        <input type="button" value="{{__('labels.u304.text_pdf1')}}" class="btn_b document_type6" />
                        @foreach ($trademarkDocumentType6 as $item)
                            <a hidden href="{{ asset($item->url) }}" class="click_url_type6" target="_blank">{{ $item->url }}</a>
                        @endforeach
                    </li>
                    @endif

                    @if(count($trademarkDocumentType2) > 0)
                    <li>
                        <input type="button" value="{{__('labels.u304.text_pdf2')}}" class="btn_b document_type2" />
                        @foreach ($trademarkDocumentType2 as $item)
                            <a hidden href="{{ asset($item->url) }}" class="click_url_type2" target="_blank">{{ $item->url }}</a>
                        @endforeach
                    </li>
                    @endif

                    @if(count($trademarkDocumentType7) > 0)
                    <li>
                        <input type="button" value="{{__('labels.u304.text_pdf3')}}" class="btn_b document_type7" />
                        @foreach ($trademarkDocumentType7 as $item)
                            <a hidden href="{{ asset($item->url) }}" class="click_url_type7" target="_blank">{{ $item->url }}</a>
                        @endforeach
                    </li>
                    @endif
                    @if(count($trademarkDocumentType7) > 0)
                        <li>
                            <input type="button" value="{{__('labels.u304.text_pdf4')}}" class="btn_b document_type7" />
                            @foreach ($trademarkDocumentType7 as $item)
                                <a hidden href="{{ asset($item->url) }}" class="click_url_type7" target="_blank">{{ $item->url }}</a>
                            @endforeach
                        </li>
                    @endif
                </ul>
                <br/>
                {{-- Trademark table --}}
                @include('user.components.trademark-table', [
                    'table' => $trademarkTable
                ])
                <br/>
                <div class="center">
                    <ul class="footerBtn2 clearfix">
                        <li><button type="submit" name="submit" value="{{U031D}}" class="btn_b redirect_u031d" style="font-size: 1.3em">{{__('labels.u303.title_button_4')}}</button> {{__('labels.u303.title_button_1')}}</li>
                        <li><button type="submit" name="submit" value="{{U031C}}" class="btn_b redirect_u031c" style="font-size: 1.3em">{{__('labels.u303.title_button_4')}}</button> {{__('labels.u303.title_button_2')}}</li>
                        <li><button type="submit" name="submit" value="{{U021}}" class="btn_b redirect_u021" style="font-size: 1.3em">{{__('labels.u303.title_button_4')}}</button> {{__('labels.u303.title_button_3')}}</li>
                    </ul>
                </div>

                <h3>【商品・サービス名】</h3>

                    <table class="normal_b eol">
                        <tr>
                            <th class="">{{__('labels.trademark_info.distinguishing')}}</th>
                            <th>{{__('labels.modal_admin.product_name')}}</th>
                        </tr>
                        @if(count($dataListProducts) > 0)
                            @foreach($dataListProducts as $key => $data)
                                <tr>
                                    <td @if(count($data) > 1) rowspan="{{count($data)}}" @endif class="center">第{{$key}}類
                                        <br />
                                        {{__('labels.a206kyo_s.count_product', ['attr' => count($data)])}}</td>
                                    @foreach($data as $keyProduct => $item)
                                        <td>{{$item->name}}</td>
                                </tr>
                                @endforeach
                                </tr>
                            @endforeach
                        @else
                            <tr><td colspan="3" style="text-align: center">{{__('messages.general.Common_E032')}}</td></tr>
                        @endif
                    </table>

                <div class="center">
                    <ul class="footerBtn2 clearfix">
                        <li><button type="submit" name="submit" value="{{U031D}}" class="btn_b redirect_u031d" style="font-size: 1.3em">{{__('labels.u303.title_button_4')}}</button> {{__('labels.u303.title_button_1')}}</li>
                        <li><button type="submit" name="submit" value="{{U031C}}" class="btn_b redirect_u031c" style="font-size: 1.3em">{{__('labels.u303.title_button_4')}}</button> {{__('labels.u303.title_button_2')}}</li>
                        <li><button type="submit" name="submit" value="{{U021}}" class="btn_b redirect_u021" style="font-size: 1.3em">{{__('labels.u303.title_button_4')}}</button> {{__('labels.u303.title_button_3')}}</li>
                    </ul>
                </div>

            </form>

        </div><!-- /contents -->


    </body>
@endsection
@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('end-user/registration/u303.js') }}"></script>
@endsection
