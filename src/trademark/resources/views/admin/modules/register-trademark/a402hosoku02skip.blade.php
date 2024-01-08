@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">

        <!-- contents inner -->
        <div class="wide clearfix">

            <form>
                {{-- Trademark table --}}
                @include('admin.components.includes.trademark-table', [
                    'table' => $trademarkTable
                ])

                <h3>{{ __('labels.a402hosoku02skip.title_page') }}</h3>


                <h4 class="eol">{{ __('labels.a402hosoku02skip.h4') }}</h4>

                <ul class="footerBtn clearfix">
                    <li><input type="button" onclick="window.location='{{ route('admin.update.procedure.document', $id) }}'" value="{{ __('labels.a402hosoku02skip.next') }}" class="btn_b" /></li>
                </ul>

            </form>

        </div><!-- /contents inner -->

    </div><!-- /contents -->
@endsection

@section('script')
    <script>
        const isAuthIsJimu = @json($isAuthIsJimu);
    </script>
    <script>
        if(!isAuthIsJimu) {
            const form = $('form');
            form.find('a, input, button, textarea, select').addClass('disabled')
            form.find('a, input, button, textarea, select').prop('disabled', true)
            form.find('a').attr('href', '#')
            form.find('a').attr('target', '')
            $('[type=submit]').remove();
        }
    </script>
@endsection
