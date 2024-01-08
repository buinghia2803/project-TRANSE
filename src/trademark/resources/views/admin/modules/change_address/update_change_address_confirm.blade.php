@extends('admin.layouts.app')
@section('main-content')
<!-- contents -->
<div id="contents">
    <!-- contents inner -->
    <div class="wide clearfix">
        @include('compoments.messages')
        <form id="form">
            @csrf
            <h3>{{ __('labels.a700kenrisha02.h3') }}</h3>
            <dl class="w16em eol clearfix">
                <dt>{{ __('labels.a700kenrisha02.dt_1') }}</dt>
                <dd>
                    <span class="blue">{{ $data['trademark_info_name'] ?? null }}</span><br />
                    {{ $dataSession['name'] ?? null }}
                </dd>

                <dt>{{ __('labels.a700kenrisha02.dt_2') }}</dt>
                <dd>
                    <span class="blue">{{ $data['trademark_info_nation_name'] ?? null }}</span><br />
                    {{ $nationSession ?? null }}
                </dd>

                <dt>{{ __('labels.a700kenrisha02.dt_3') }}</dt>
                <dd>
                    <table>
                        <tr>
                            <td class="blue">
                                @if($data['trademark_info_nation_id'] == NATION_JAPAN_ID)
                                    <span class="me-3">{{  $data['trademark_info_address_first_name'] }}</span>
                                    <span class="blue me-3"> {{  $data['trademark_info_address_second'] }}</span>
                                @endif
                                    <span class="blue me-3"> {{ $data['trademark_info_address_three'] }}</span>
                            </td>

                        </tr>
                        <tr>
                            <td>
                                @if($dataSession['m_nation_id'] == NATION_JAPAN_ID)
                                <span class="me-3">{{ trim($prefectureSession)  }}</span>
                                <span class="me-3">{{ trim($dataSession['address_second']) }}</span>
                                @endif
                                <span class="me-3">{{ trim($dataSession['address_three']) ?? null }}</span>
                            </td>
                        </tr>
                    </table>
                </dd>
            </dl>

            <ul class="footerBtn clearfix">
                <li>
                    <input type="button" value="{{ __('labels.a700kenrisha02.back') }}" class="btn_a" onclick="window.location = '{{ $backUrl ?? route('admin.home') }}'">
                </li>
                <li><input type="button" value="{{ __('labels.a700kenrisha02.submit') }}" class="btn_b" onclick="window.location = '{{$redirectUrl}}'"/></li>
            </ul>
        </form>
    </div>
    <!-- /contents inner -->
</div>
<!-- /contents -->
@endsection
