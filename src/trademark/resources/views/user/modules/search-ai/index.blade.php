@extends('user.layouts.app')

@section('main-content')
    <div id="contents" class="normal">
        <h2>{{ __('labels.search-ai.title') }}</h2>
        <form action="{{ route('user.search-ai.post') }}" id="form" method="POST" enctype="multipart/form-data">
            @csrf

            <h3>
                {{ __('labels.search-ai.note_1') }}<br />
                <span class="note">{{ __('labels.search-ai.note_2') }}</span>
            </h3>

            <dl class="w16em eol clearfix">
                <dt>{{ __('labels.search-ai.form.type_trademark') }}</dt>
                <dd class="fRadio">
                    <ul class="r_c clearfix radio-group">
                        @php
                            $typeTrademark = old('type_trademark', $searchAIData['type_trademark'] ?? 1);
                        @endphp
                        <li>
                            <label>
                                <input type="radio" name="type_trademark" value="{{ TRADEMARK_TYPE_LETTER }}"
                                    {{ $typeTrademark == TRADEMARK_TYPE_LETTER ? 'checked' : '' }} />
                                {{ __('labels.search-ai.form.type_trademark_letter') }}
                            </label>
                        </li>
                    </ul>
                </dd>

                <dt>{{ __('labels.search-ai.form.name_trademark') }}</dt>
                <dd>
                    <input type="text" class="em40" name="name_trademark"
                        value="{{ old('name_trademark', $searchAIData['name_trademark'] ?? '') }}" />
                    <br>
                    @error('name_trademark')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                </dd>
            </dl>

            <h3>{{ __('labels.search-ai.keyword.desc') }}</h3>

            <h3>{{ __('labels.search-ai.keyword.title') }}</h3>

            <p>{!! __('labels.search-ai.keyword.subtitle') !!}</p>
            @if (!count($dataSearch))
                <ul class="list list-keyword">
                    <li><input type="text" class="em30" name="keyword[]" /></li>
                    <li><input type="text" class="em30" name="keyword[]" /></li>
                    <li><input type="text" class="em30" name="keyword[]" /></li>
                    <li><input type="text" class="em30" name="keyword[]" /></li>
                </ul>
            @else
                <ul class="list list-keyword">
                    @foreach ($dataSearch as $item)
                        <li><input type="text" class="em30" name="keyword[]" value="{{ $item }}" /></li>
                    @endforeach
                </ul>
            @endif
            <div class="add-more">
                <a href="javascript:;" id="addMoreKeyword">{{ __('labels.search-ai.keyword.add_more_keyword') }}</a>
            </div>

            <div class="mb20"></div>

            <ul class="r_c eol clearfix">
                <li>
                    <input type="button" value="{{ __('labels.search-ai.keyword.product_before') }}" class="btn_a" data-open_modal="#u031pass-modal"/>
                </li>
            </ul>
            <div id="u031pass-modal" class="modal fade" role="dialog">
                <div class="modal-dialog" style="min-width: 60%;min-height: 60%;">
                    <!-- Modal content-->
                    <div class="modal-content">
                        <div class="modal-body">
                            <button type="button" class="close-alert" data-dismiss="modal">&times;</button>
                            <div class="content loaded">
                                <iframe
                                    src=""
                                    data-src="{{route('user.apply-trademark.show-pass', [
                                        'id' => $id ?? 0,
                                        'from_page' => U020A
                                    ])}}"
                                    style="width: 100%; height: 70vh;" frameborder="0"
                                ></iframe>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (count($keywords))
                <p>
                    {{ __('labels.search-ai.keyword.list_keyword') }}<br />
                    @foreach ($keywords as $keyword)
                        {{ ($keyword ?? '') . '　' }}
                    @endforeach
                </p>
            @endif

            <ul class="footerBtn eol clearfix">
                <li><input type="reset" value="{{ __('labels.search-ai.form.reset') }}" class="btn_a" style="font-size: 1.3em;" /></li>
                <li><input type="submit" value="{{ __('labels.search-ai.form.submit') }}" class="btn_b" /></li>
            </ul>
        </form>
    </div>
    <!-- /contents -->
@endsection

@section('footerSection')
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        const MAX_KEYWORD_FORM = 30;

        const errorMessageIsFullWidth = '{{ __('messages.general.Register_U001_E006') }}';
        const errorMessageIsFullWidthKeyword = '{{ __('messages.general.support_U011_E001') }}';
        const errorMessageImageTrademarkFormat = '{{ __('messages.general.Common_E023') }}';
    </script>
    <script src="{{ asset('end-user/search-ai/index.js') }}"></script>
@endsection
