@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            <table class="normal_b column1">
                <caption></caption>
                <tr>
                    <th style="width:3em;">{{ __('labels.a402hosoku01.distinctinon_label') }}</th>
                    <th>{{ __('labels.a402hosoku01.prod_service_label') }}</th>
                </tr>
                @php
                    $isNotApply = \App\Models\RegisterTrademarkProd::IS_NOT_APPLY;
                @endphp
                @foreach($products as $distionName => $data)
                    @php
                        $allIsApplyTrue = $data->every(function($value, $key) use($isNotApply) {
                            return $value->is_apply == $isNotApply;
                        });
                    @endphp
                    @foreach ($data as $k => $item)

                            <tr class="{{ $allIsApplyTrue ? 'bg_gray' : ''  }} ">
                                @if($k == 0)
                                    <td rowspan="{{ $data->count() * 2 }}" class="center">{{ $distionName }}</td>
                                @endif
                                    <td class="{{ $item->is_apply == $isNotApply ? 'bg_gray' : '' }}">{{ $item->name }}</td>
                            <tr>
                    @endforeach
                @endforeach
            </table>
            <!-- /table 対比表 -->

            <p class="left eol">{{ __('labels.a402hosoku01.note_text_a402hosoku01window') }}</p>

            <input type="button" class="btn_b btn-blank-page" id="btn-blank-page-block" onclick="window.open(`{{ route('admin.update.document.modification.product.window', $id) }}`, '_blank');" value="{{ __('labels.a402hosoku01.prod_service_label_new_tab') }}" /><br/>

        </div><!-- /contents inner -->

    </div><!-- /contents -->
    <style>
        .admin #header, #footer {
            display: none;
        }
        .btn-blank-page {
            cursor: pointer;
            display: none!important;
        }
        tr.bg_gray {
            background: #dfdfdf;
        }
    </style>
@endsection

