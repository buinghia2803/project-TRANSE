@extends('admin.layouts.app')
@section('main-content')
<!-- contents -->
<div id="contents">
    <!-- contents inner -->
    <div class="wide clearfix">
        @include('compoments.messages')
        <form id="form" action="{{ $urlPost }}" method="POST">
            @csrf
            <h3>{{ __('labels.a700kenrisha03.h3') }}</h3>

            <p>{{ __('labels.a700kenrisha03.p') }}</p>

            <dl class="w16em eol clearfix">
                <dt>{{ __('labels.a700kenrisha03.dt_1') }}</dt>
                <dd>
                    {{ $data['trademark_info_name'] ?? null }}<br />
                    {{ $dataSession['name'] ?? null }}
                </dd>

                <dt>{{ __('labels.a700kenrisha03.dt_2') }}</dt>
                <dd>
                    {{ $data['trademark_info_nation_name'] ?? null }}<br />
                    {{ $nationSession ?? null }}
                </dd>

                <dt>{{ __('labels.a700kenrisha03.dt_3') }}</dt>
                <dd>
                    <table>
                        <tr>
                            <td>
                                @if($data['trademark_info_nation_id'] == NATION_JAPAN_ID)
                                <span class="me-3">{{  $data['trademark_info_address_first_name'] }}</span>
                                <span class="me-3"> {{  $data['trademark_info_address_second'] }} </span>
                                @endif
                                <span class="me-3"> {{ $data['trademark_info_address_three'] }} </span>
                            </td>

                        </tr>
                        <tr>
                            <td>
                                @if($dataSession['m_nation_id'] == NATION_JAPAN_ID)
                                    <span class="me-3">{{ trim($prefectureSession)  }}</span>
                                    <span class="me-3">{{ trim($dataSession['address_second']) }}</span>
                                @endif
                                <span class="me-3">{{ $dataSession['address_three'] ?? null }}</span>
                            </td>
                        </tr>
                    </table>
                </dd>
            </dl>

            <ul class="footerBtn clearfix">
                <li><input type="submit" value="{{ __('labels.a700kenrisha03.submit') }}" class="btn_b" /></li>
            </ul>
        </form>
    </div>
    <!-- /contents inner -->
</div>
<!-- /contents -->
@endsection
