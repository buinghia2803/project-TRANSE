@if (session()->has('message'))
    <div class="alert alert-success alert-dismissible">
        <button type="button" class="close-alert" data-dismiss="alert" aria-label="close-alert">&times;</button>
        {{ session('message') }}
    </div>
@endif

@if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible">
        <button type="button" class="close-alert" data-dismiss="alert" aria-label="close-alert">&times;</button>
        {{ session('error') }}
    </div>
@endif

@if (!empty($alertMsg))
    <div class="alert alert-{{ $type }} alert-dismissible">
        <button type="button" class="close-alert" data-dismiss="alert" aria-label="close-alert">&times;</button>
        {!! $message !!}
    </div>
@endif

@if (session()->has('message_confirm'))
    @php
        $messageConfirm = session('message_confirm');
        session()->forget('message_confirm');
    @endphp
    <script>
        const URL_REDIRECT = '{!! $messageConfirm['url'] ?? '' !!}';
        $.confirm({
            title: '{!! __($messageConfirm['title'] ?? '') !!}',
            content: '{!! __($messageConfirm['content'] ?? '') !!}',
            buttons: {
                submit: {
                    text: '{!! __($messageConfirm['btn'] ?? '') !!}',
                    btnClass: 'btn-blue',
                    action: function () {
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
