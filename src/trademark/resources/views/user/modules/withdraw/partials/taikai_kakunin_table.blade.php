
@forelse ($trademarks as $trademark)
    @php
        $trademarkInfo = null;
        if(isset($trademark->appTrademark) && $trademark->appTrademark) {
            $trademarkInfo = $trademark->appTrademark->trademarkInfo->last();
        }
    @endphp
    <tr>
        <td><a href="{{ route('user.application-detail.index', ['id' => $trademark->id]) }}">{{ $trademark->trademark_number ?? '' }}</a></td>
        <td>{{ $trademark->reference_number ?? '' }}</td>
        <td>
            @if ($trademark->type_trademark == TYPE_TRADEMARK_CHARACTERS)
                <span>{{ $trademark->name_trademark }}</span>
            @else
                <span>
                    <img src="{{ $trademark->image_trademark }}" alt="image trademark">
                </span>
            @endif
        </td>
        <td>
            @if ($trademark->registerTrademark && $trademark->register_number != null)
                {{ $trademark->registerTrademark->trademark_info_name ?? '' }}
            @else
                {{ $trademarkInfo['name'] ?? '' }}
            @endif
        </td>
        <td>
            @if ($trademark->registerTrademark && $trademark->registerTrademark->register_number)
                {{ $trademark->register_number ?? '' }}
            @endif
        </td>
        <td>
            @if (isset($trademark->noticeDetail) && $trademark->noticeDetail)
                @if ($trademark->noticeDetail && $trademark->noticeDetail->redirect_page)
                    <a href="{{ $trademark->noticeDetail->redirect_page }}">
                        <span>{{ $trademark->noticeDetail->content ?? '' }}</span>
                    </a>
                @else
                    <span>
                        {{ $trademark->noticeDetail->content ?? '' }}
                    </span>
                @endif
            @endif
        </td>
        <td>{{ isset($noticeDetail->created_at) && $noticeDetail->created_at ? Carbon\Carbon::parse($noticeDetail->created_at)->format('Y/m/d') : '' }}</td>
    </tr>
@empty
    <tr>
        <td colspan="7" style="text-align: center">
            {{ __('messages.general.Common_E032') }}
        </td>
    </tr>
@endforelse