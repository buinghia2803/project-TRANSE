<html lang="ja">
<head>
    <meta http-equiv=Content-Type content="text/html; charset=shift_jis">
@if(!empty($option['title']))    <title>{{ $option['title'] ?? '' }}</title>@endif
</head>
<body>
    <div>
    @foreach($dataExport as $item)@switch($item['type'])
        @case(TEXT)<p{!! !empty($item['attr']) ? ' ' . $item['attr'] : '' !!}>{!! __($item['label'] ?? '') !!}{!! __($item['value'] ?? '') !!}</p>
    @break
        @case(TITLE)<p>{!! __($item['label'] ?? '') !!}</p>
    @break
        @case(IMAGE)<p><img src="{!! __($item['value'] ?? '') !!}" style="{{$item['style']??''}}"></p>
    @break
    @case(MULTI_IMAGE)<p class="attachment_title" style="display: inline-block;vertical-align: top;margin: 0;">{!! __($item['label'] ?? '') !!}</p>
    <div class="image_box" style="display: inline-block;width: 640px;text-align: left;padding-top: 7px;">
        @foreach($item['images'] ?? [] as $attachment)<div style="display: block;text-align: center;">
            <img src="{{ $attachment['attach_file'] ?? '' }}" style="max-width: 100%;"/>
            <p>{{ $attachment['file_no'] ?? '' }}</p>
        </div>
        @endforeach
</div>
    @break
@endswitch
@endforeach
</div>
</body>
</html>
