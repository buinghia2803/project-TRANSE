@extends('admin.layouts.app')
@section('main-content')
    @php
        $errors = [];
        $errorSession = Session::get('errors');
        if ($errorSession && $errorSession->getBag('default') && $errorSession->getBag('default')->messages()) {
            $errors = $errorSession->getBag('default')->messages();
        }
    @endphp
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            <form id="form" action="{{ route('admin.save-xml-data') }}" method="POST">
                @csrf
                @if ($errors && count($errors))
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close-alert" data-dismiss="alert" aria-label="close">&times;</button>
                        @foreach ($errors as $key => $item)
                            {{ (isset($item['filename']) ? $item['filename'] . ' . ' : '') . (isset($item['message']) ? $item['message'] : '') }} <br/>
                        @endforeach
                    </div>
                @endif
                <input type="hidden" name="s" value="{{ Request::get('s') }}">
                <h2>{{ __('labels.import_02.matching_result_title') }}</h2>
                <table class="normal_b column1 eol">
                    <caption>{{ __('labels.import_02.import_XML_from_PO', ['attr' => count($dataView)]) }}</caption>
                    <thead>
                        <tr>
                            <th style="width:6em;">{{ __('labels.import_02.document_name') }}</th>
                            <th style="width:4em;">{{ __('labels.import_02.reference_number') }}</th>
                            <th style="width:4em;">{{ __('labels.import_02.app_number') }}</th>
                            <th style="width:4em;">{{ __('labels.import_02.ship_date') }}</th>
                            <th style="width:6em;">{{ __('labels.import_02.target_reference_number') }}</th>
                            <th style="width:6em;">{{ __('labels.import_02.target_application_number') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dataView as $item)
                            <tr>
                                <td>{{ $item['document_name'] ?? '' }}</td>
                                <td>
                                    <span>{{ $item['reference_id'] ?? '' }}</span>
                                </td>
                                <td>
                                    <span>{{ $item['application_number'] ?? '' }}</span>
                                </td>
                                <td>
                                    {{ isset($item['date']) && $item['date'] ?$item['date'] : ''  }}
                                </td>
                                <td>
                                    <a href="{{ isset($item['trademark_id']) && $item['trademark_id'] ? route('admin.application-detail.index', ['id' => $item['trademark_id']]): 'javascript:void(0)' }}">
                                        {{ $item['reference_id'] ?? '' }}
                                    </a><br>
                                </td>
                                <td>
                                    <a href="{{ isset($item['trademark_id']) && $item['trademark_id'] ? route('admin.application-detail.index', ['id' => $item['trademark_id']]): 'javascript:void(0)' }}">
                                        {{ $item['application_number'] ?? '' }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <hr>
                <table class="normal_b column1 eol">
                    <caption>{{ __('labels.import_02.no_import_destination', ['attr' => count($dataNull)]) }}</caption>
                    <thead>
                        <tr>
                            <th style="width:6em;">{{ __('labels.import_02.document_name') }}</th>
                            <th style="width:4em;">{{ __('labels.import_02.reference_number') }}</th>
                            <th style="width:4em;">{{ __('labels.import_02.app_number') }}</th>
                            <th style="width:4em;">{{ __('labels.import_02.ship_date') }}</th>
                            <th style="width:6em;">{{ __('labels.import_02.target_reference_number') }}</th>
                            <th style="width:6em;">{{ __('labels.import_02.target_application_number') }}</th>
                            <th style="width:4em;">{{ __('labels.import_02.confirmation') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dataNull as $item)
                            <tr>
                                <td>{{ $item['document_name'] ?? '' }}</td>
                                <td>
                                    <span> {{ $item['reference_id'] ?? '' }} </span>
                                </td>
                                <td>
                                        <span>{{ $item['application_number'] ?? '' }}</span>
                                </td>
                                <td>
                                    {{ isset($item['date']) && $item['date'] ? $item['date'] : ''  }}
                                </td>
                                <td></td>
                                <td></td>
                                <td class="center"><input type="checkbox" name="is_confirm_null" ></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <hr>
                <table class="normal_b column1 eol" id="trademarkCloseTbl">
                    <caption>{{ __('labels.import_02.close_import_destination', ['attr' => count($trademarkClose)]) }}</caption>
                    <thead>
                        <tr>
                            <th style="width:6em;">{{ __('labels.import_02.document_name') }}</th>
                            <th style="width:4em;">{{ __('labels.import_02.reference_number') }}</th>
                            <th style="width:4em;">{{ __('labels.import_02.app_number') }}</th>
                            <th style="width:4em;">{{ __('labels.import_02.ship_date') }}</th>
                            <th style="width:6em;">{{ __('labels.import_02.target_reference_number') }}</th>
                            <th style="width:6em;">{{ __('labels.import_02.target_application_number') }}</th>
                            <th style="width:4em;">{{ __('labels.import_02.confirmation') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($trademarkClose as $item)
                            <tr>
                                <td>
                                    <span>{{ $item['document_name'] ?? '' }}</span>
                                </td>
                                <td>
                                    <span> {{ $item['reference_id'] ?? '' }} </span>
                                </td>
                                <td>
                                    <span>{{ $item['application_number'] ?? '' }}</span>
                                </td>
                                <td>
                                    <span>{{ isset($item['date']) && $item['date'] ? $item['date'] : ''  }}</span>
                                </td>
                                <td>
                                    @if (isset($item['reference_id']) && isset($item['trademark_id']))
                                        <a href="{{ !empty($item['trademark_id']) ? route('admin.application-detail.index', ['id' => $item['trademark_id']]): 'javascript:void(0)' }}">
                                            {{ $item['reference_id'] ?? '' }}
                                        </a>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ !empty($item['trademark_id']) ? route('admin.application-detail.index', ['id' => $item['trademark_id']]): 'javascript:void(0)' }}">
                                        {{ $item['application_number'] ?? '' }}
                                    </a>
                                </td>
                                <td class="center"><input type="checkbox" name="is_confirm_close"></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <table class="normal_b column1 eol" id="trademarkDuplicateTbl">
                    <caption>{{ __('labels.import_02.duplicate_import_destination', ['attr' => count($trademarkDuplicate)]) }}</caption>
                    <tr>
                        {{-- <th style="width:4em;">XML: 結果</th> --}}
                        <th style="width:6em;">{{ __('labels.import_02.document_name') }}</th>
                        <th style="width:4em;">{{ __('labels.import_02.reference_number') }}</th>
                        <th style="width:4em;">{{ __('labels.import_02.app_number') }}</th>
                        <th style="width:4em;">{{ __('labels.import_02.ship_date') }}</th>
                        <th style="width:4em;">{{ __('labels.import_02.shipment_number') }}</th>
                        <th style="width:6em;">{{ __('labels.import_02.target_reference_number') }}</th>
                        <th style="width:6em;">{{ __('labels.import_02.target_application_number') }}</th>
                        <th style="width:4em;">{{ __('labels.import_02.confirmation') }}</th>
                    </tr>
                    @foreach ($trademarkDuplicate as $item)
                        <tr>
                            <td>
                                <span>{{ $item['document_name'] ?? '' }}</span>
                            </td>
                            <td>
                                <span> {{ $item['reference_id'] ?? '' }} </span>
                            </td>
                            <td>
                                <span>{{ $item['application_number'] ?? '' }}</span>
                            </td>
                            <td>
                                <span>{{  $item['date'] ?? ''  }}</span>
                            </td>
                            <td>
                                <span>{{ $item['dispatch_number'] ?? '' }}</span>
                            </td>
                            <td>
                                <a href="{{ !empty($item['trademark_id']) ? route('admin.application-detail.index', ['id' => $item['trademark_id']]): 'javascript:void(0)' }}">
                                    {{ $item['reference_id'] ?? '' }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ !empty($item['trademark_id']) ? route('admin.application-detail.index', ['id' => $item['trademark_id']]): 'javascript:void(0)' }}">
                                    {{ $item['application_number'] ?? '' }}
                                </a>
                            </td>
                            <td class="center"><input type="checkbox" name="is_confirm_duplicate"></td>
                        </tr>
                    @endforeach
                </table>
                <ul class="footerBtn clearfix">
                    <li><button type="submit" value="" class="btn_b btn_submit">{{ __('labels.import_02.capture') }}</button></li>
                </ul>
                <ul class="footerBtn clearfix">
                    <li>
                        <a class="btn_a" style="width: 112px; text-align: center; height: 38px; padding: 0; line-height: 38px;" href="{{ route('admin.import-doc-xml') }}">
                            {{ __('labels.import_02.return') }}
                        </a>
                    </li>
                </ul>
            </form>
        </div>
        <!-- /contents inner -->
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
<script type="text/JavaScript">
    const errorMsgNotConfirmNullData = '{{ __('messages.import_xml.not_check_null')}}'
    const errorMsgNotConfirmCloseData = '{{ __('messages.import_xml.not_check_close')}}'
    const errorMsgNotConfirmDuplicateData = '{{ __('messages.import_xml.not_check_duplicate')}}'

    const NO = '{{ __('labels.support_first_times.no') }}';
    const YES = '{{ __('labels.support_first_times.yes') }}';
</script>
<script type="text/JavaScript" src="{{ asset('admin_assets/import-xml/A000import02.js') }}"></script>
@endsection
