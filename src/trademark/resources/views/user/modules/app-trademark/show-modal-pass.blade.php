
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <title>AMS オンライン出願サービス</title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />

    <link href="{{ asset('common/css/contents.css') }}" rel="stylesheet" type="text/css" />
    <script type="text/JavaScript" src="{{asset('common/js/jquery-3.6.0.min.js')}}"></script>
</head>
<body id="pagetop" class="user" style="height: auto">


<!-- wrapper -->
<div id="wrapper" style="height: 80%">


    <!-- contents -->
    <div id="contents">


        <h3></h3>
        <p><input type="button" value="{{__('labels.u031_pass.add_product')}}" class="btn_a add_prod" /></p>
        <div class="error-table" style="color: red"></div>
        <table class="normal_b w-100 mw640 mb10 table-u031-pass">
            <tr>
                <th>{{__('labels.user_common_payment.applicant_name')}}</th>
                <th class="em06">{{__('labels.user_common_payment.division')}}</th>
                <th class="">{{__('labels.user_common_payment.product_service_name')}}</th>
                <th class="em04">{{__('labels.update_profile.select_default')}}</th>
            </tr>
            @if(count($datas) > 0)
                @foreach($datas as $key => $data)
                    <tr>
                        <td
                            @if($data['countProduct'] > 1))
                            rowspan="{{$data['countProduct']}}"
                            @else
                                rowspan="{{count($data['distinction'])}}"
                            @endif
                        >{{$data['trademark_info_name']}}</td>
                    @foreach($data['distinction'] as $keyData => $item)
                        @if($keyData != 0)
                            <tr>
                                @endif
                                <td @if (count($item['product']) > 1) rowspan="{{count($item['product'])}}" @endif>
                                    {{__('labels.apply_trademark._table_product_choose.distinctions', ['distinction' => $item['distinction_name']])}}
                                </td>
                            @foreach($item['product'] as $keyProduct => $value)
                            @if($keyProduct != 0)
                                    <tr>
                                        @endif
                                        <td>{{$value->product_name}}</td>
                                        <td class="center">
                                            <input type="checkbox" name="products" value="{{ json_encode($value) }}" data-product-id="{{$value->product_id}}"/>
                                        </td>
                                        @if($keyProduct != 0)
                                    </tr>
                                    @endif
                                    @endforeach
                                    @if($keyData != 0)
                                        </tr>
                                    @endif
                                    @endforeach
                                    </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="4" style="text-align: center">{{__('labels.no_data')}}</td>
                                    </tr>
                                @endif
        </table>
        <div class="error-table" style="color: red"></div>
        <p class="eol"><input type="button" value="{{__('labels.u031_pass.add_product')}}" class="btn_a add_prod" id="add_prod"/></p>
        <p class="center fs12"><a href="#" id="close" class="btn_b">{{__('labels.registrant_information.button_close_modal')}}</a></p>
        </form>

    </div><!-- /contents -->
</div><!-- /wrapper -->

<script src="{{ asset('common/js/functions.js') }}"></script>
<script>
    const ErrorApplicationU031E004 = '{{__('messages.general.Application_U031_E004')}}';
    const constNo = '{{__('labels.support_first_times.No')}}';
    const constKind = '{{__('labels.support_first_times.kind')}}';
    const fromPage = @json($fromPage);
</script>
<script type="text/JavaScript" src="{{ asset('end-user/app_trademark/show-modal-pass.js') }}"></script>
</body>
</html>
