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
               <h3>{{ __('labels.a402hosoku01.title_page') }}</h3>
               @if($expirationYear)
                   <p>{{ __('labels.a402hosoku01.renewal_period') }}{{ $expirationYear }}</p>
               @endif
               <p>
                   <iframe src="{{ route('admin.update.document.modification.product.window', $id) }}" id="iframe-show" width="70%" height="400"></iframe>
               </p>
               <ul class="footerBtn clearfix">
                   <li>
                       <input type="button" onclick="window.location='{{ $routeRedirect }}'" value="{{ __('labels.signup.form.submit_code') }}" class="btn_b"/><br/>
                   </li>
               </ul>
           </form>
        </div><!-- /contents inner -->
    </div><!-- /contents -->
@endsection
@section('css')
   <style>
       .disabled-tag-a {
           pointer-events: none;
           cursor: default;
       }
   </style>
@endsection
@section('script')
    <script>
        const isAuthIsJimu = @json($isAuthIsJimu);
        $(document).ready(function() {
            //disabled tag a
            if(!isAuthIsJimu) {
                $('form').find('a').addClass('disabled-tag-a')
            }
            //show btn blank page iframe
            $('#iframe-show').on('load', function() {
                let iframe = document.getElementById("iframe-show");
                let innerDoc = iframe.contentDocument || iframe.contentWindow.document;
                let btnEl = innerDoc.getElementById('btn-blank-page-block')
                if(btnEl) {
                    btnEl.style.cssText = 'display: block!important;'
                    if(!isAuthIsJimu) {
                        btnEl.style.cssText = 'disabled: true'
                    }
                }
            });
        })
    </script>
    @include('compoments.readonly', ['only' => [ROLE_OFFICE_MANAGER]])
@endsection
