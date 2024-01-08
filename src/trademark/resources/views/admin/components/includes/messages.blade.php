{{--@if (count($errors) > 0)--}}
{{--    <div class="alert alert-danger alert-dismissible">--}}
{{--        <button type="button" class="close-alert" data-dismiss="alert" aria-label="close">&times;</button>--}}
{{--        @foreach ($errors->all() as $error)--}}
{{--            <p class="mb-0">{{ $error }}</p>--}}
{{--        @endforeach--}}
{{--    </div>--}}
{{--@endif--}}

@if (session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close-alert" data-dismiss="alert" aria-label="close">&times;</button>
        {{ session('message') }}
    </div>
@endif

@if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close-alert" data-dismiss="alert" aria-label="close">&times;</button>
        {{ session('error') }}
    </div>
@endif

@if (!empty($alertMsg))
    <div class="alert alert-{{ $type }} alert-dismissible">
        <button type="button" class="close-alert" data-dismiss="alert" aria-label="close">&times;</button>
        {!! $message !!}
    </div>
@endif

@if (session()->has('message_confirm'))
    @php
        $messageConfirm = session('message_confirm')
    @endphp
    <script>
        const URL_REDIRECT = '{!! $messageConfirm['url'] ?? '' !!}';
        $.confirm({
            title: '{!! $messageConfirm['title'] ?? '' !!}',
            content: '{!! $messageConfirm['content'] ?? '' !!}',
            buttons: {
                somethingElse: {
                    text: '{!! $messageConfirm['btn'] ?? '' !!}',
                    btnClass: 'btn-default',
                    action: function(){
                        if (URL_REDIRECT.length > 0) {
                            window.location.href = URL_REDIRECT;
                        }
                    }
                }
            }
        });
    </script>
@endif

@if (session()->has('alertMsg'))
    {!! \CommonHelper::getMessage(request()) !!}
@endif
