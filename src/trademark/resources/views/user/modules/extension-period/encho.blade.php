@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.encho.title') }}</h2>
        <form>
            <p>{{ __('labels.encho.text_1') }}<br />
                {{ __('labels.encho.end_date') }}{{ CommonHelper::formatTime($registerTrademarkRenewal->end_date ?? '', 'Y年m月d日') }}
            </p>

            <p class="red">{{ __('labels.encho.text_2') }}

            </p>
            <p class="eol"><input type="button" value="{{ __('labels.encho.text_4') }}" class="btn_b" id="open_file_import"
                    {{ $trademarkDocImport->count() == 0 ? 'disabled' : '' }} /></p>
            <h3>{{ __('labels.encho.text_3') }}</h3>
            <div class="info mb10">
                {{-- Trademark table --}}
                @include('user.components.trademark-table', [
                    'table' => $trademarkTable,
                ])
            </div><!-- /info -->
            <div class="custom">
                <div class="text">
                    <dt class="mb-2">
                        {{ __('labels.encho.sending_noti_reject_date') }}{{ $comparisonTrademarkResult->parseSendingNotiRejecttionDate() }}
                    </dt>
                    <dt class="end_date">
                        {{ __('labels.encho.end_date') }}{{ CommonHelper::formatTime($registerTrademarkRenewal->end_date ?? '', 'Y年m月d日') }}
                    </dt>
                </div>
                <dd>
                    <input type="button" value="{{ __('labels.encho.text_5') }}" class="btn_b" id="open_file_upload"
                        {{ $trademarkDocUpload->count() == 0 ? 'disabled' : '' }} />
                </dd>
            </div>
            <ul class="footerBtn clearfix">
                <li>
                    <a href="{{ route('user.application-detail.index', ['id' => $trademark->id]) }}"
                        class="btn_a custom_a">{{ __('labels.encho.text_6') }}</a>
                </li>
            </ul>
        </form>
    </div>
    <!-- /contents -->
@endsection
<style>
    .custom {
        display: flex;
        margin-bottom: 3rem;
    }

    .custom_a {
        padding: 5px 2em !important;
    }

    .text {
        width: 280px;
    }

    @media only screen and (max-width: 510px) {
        .custom {
            display: block !important;
        }

        .end_date {
            margin-bottom: 10px;
        }
    }
</style>
@section('footerSection')
    @if ($trademarkDocImport->count() == 0)
        <script>
            $('#open_file_import').addClass('disabled')
        </script>
    @endif
    @if ($trademarkDocUpload->count() == 0)
        <script>
            $('#open_file_upload').addClass('disabled')
        </script>
    @endif
    <script>
        const trademarkDocImports = @json($trademarkDocImport->values());
        const trademarkDocUploads = @json($trademarkDocUpload);

        openAllFileAttach(trademarkDocImports, '#open_file_import');
        openAllFileAttach(trademarkDocUploads, '#open_file_upload');
    </script>
@endsection
