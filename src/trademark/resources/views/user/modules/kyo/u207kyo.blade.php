@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">
        <h2>{{ __('labels.u207kyo.title') }}</h2>
        <form id="form" action="{{ route('user.redirect_page_find_refusal') }}" method="post">
            @csrf
            @if (
                $trademark->comparisonTrademarkResult->is_cancel == 1 ||
                    $trademark->comparisonTrademarkResult->checkResponseDeadline())
                <p>{{ __('labels.u207kyo.text_1') }}<br />
                    {{ __('labels.u207kyo.text_2') }}</p>
            @else
                <p>{{ __('labels.u207kyo.text_3') }}<br />
                    {{ __('labels.u207kyo.text_4') }}<br />
                    {{ __('labels.u207kyo.text_5') }}</p>
            @endif
            <br />
            <input type="hidden" name="comparison_trademark_result_id" value="{{$comparisonTrademarkResult->id}}">
            <input type="hidden" name="plan_correspondence_id" value="{{$planCorrespondence->id}}">
            <h3>{{ __('labels.u207kyo.text_6') }}</h3>
            <div class="info mb10">
                {{-- Trademark table --}}
                @include('user.components.trademark-table', [
                    'table' => $trademarkTable,
                ])
            </div>
            <!-- /info -->
            @if ($trademarkDocuments->count())
                <p class="eol"><input type="button" value="{{ __('labels.u207kyo.open_file') }}" class="btn_b"
                        id="openAllFileAttach" /></p>
            @endif
            @if (isset($trademark->comment_refusal) && $trademark->comment_refusal)
                <p>{{ __('labels.u207kyo.comment_refusal') }}<br />
                    <span style="white-space: pre-line"> {{ $trademark->comment_refusal ?? '' }}<br /></span>
                </p>
            @endif
            <h3>{{ __('labels.u207kyo.text_7') }}</h3>
            <p>{{ __('labels.u207kyo.text_8') }}<br />
                {{ __('labels.u207kyo.text_9') }}</p>
            @if ($mDistinct->count())
                <table class="normal_b eol custom_table">
                    <tr>
                        <th style="width:4em;">{{ __('labels.u207kyo.distinct') }}</th>
                        <th>{{ __('labels.u207kyo.product') }}</th>
                    </tr>
                    @foreach ($mDistinct as $nameDistinct => $mProducts)
                        @foreach ($mProducts as $keyItem => $product)
                            <tr>
                                @if ($keyItem == 0)
                                    <td rowspan="{{ $mProducts->count() > 1 ? $mProducts->count() : '' }}">
                                        {{ __('labels.u207kyo.name_distinct', ['attr' => $nameDistinct]) }}
                                    </td>
                                @endif
                                <td class="boxes">{{ $product->name }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                </table>
            @endif
            <br />
            <ul class="footerBtn clearfix">
                <li>
                    <input type="submit" value="{{ __('labels.u207kyo.redirect_u031c') }}" class="btn_b"
                        data-submit="{{ U031C }}" />{{ __('labels.u207kyo.text_10') }}
                    <br>
                    <input type="button" value="{{ __('labels.u207kyo.back') }}" class="btn_a mt-2"
                        onclick="history.back()" />
                </li>
            </ul>
            <input type="hidden" name="submit_type">
            <input type="hidden" name="trademark_id" value="{{ $trademark->id }}">
            @foreach ($planCorrespondence->planCorrespondenceProds as $planCorrespondenceProd)
                <input type="hidden" name="planCorrespondenceProd[]" value="{{ $planCorrespondenceProd->id }}">
            @endforeach
        </form>

    </div><!-- /contents -->
@endsection
<style>
    @media only screen and (max-width: 767px) {
        .custom_table {
            width: 100% !important;
        }
    }

    .custom_table {
        width: 740px;
    }
</style>
@section('footerSection')
    <script>
        const trademarkDocument = @json($trademarkDocuments);
    </script>
    <script type="text/JavaScript" src="{{ asset('end-user/u207/u207.js') }}"></script>
@endsection
