
<div class="info eol">
    <table class="info_table">
        <tr>
            <th class="em14">{{ __('labels.user_common_payment.quote_number') }}</th>
            <td>
                <a href="/quote" target="_blank">{{ $payment['quote_number'] ?? ''}}</a>
                <input type="hidden" name="quote_number" value="{{ $payment['quote_number'] ?? 0 }}" />
            </td>
        </tr>
        <tr>
            <th class="em14">{{ __('labels.user_common_payment.applicant_name') }}</th>
            <td>
                {{ $data['trademark_info_name'] ?? '' }}
                <input type="hidden" name="trademark_info_name" value="{{ $data['trademark_info_name'] ?? null }}" />
            </td>
        </tr>
        <tr>
            <th class="em14">{{ __('labels.user_common_payment.applicant_address') }}</th>
            <td>
                {{ ( isset($data['m_prefecture_id']) && $data['m_prefecture_id'] ? $data['m_prefecture_name'] : '').($data['address_second'] ?? '') }}
                <input type="hidden" name="m_prefecture_id" value="{{ $data['m_prefecture_id'] ?? 0 }}" />
                <input type="hidden" name="address_second" value="{{$data['address_second'] ?? 0 }}" />
            </td>
        </tr>
        <tr>
            <th class="em14">{{ __('labels.user_common_payment.brand_name') }}</th>
            <td>
                {{ $data['name_trademark'] ?? '' }}
                <input type="hidden" name="name_trademark" value="{{ $data['name_trademark'] ?? 0 }}" />
                <input type="hidden" name="type_trademark" value="{{ $data['type_trademark'] ?? 0 }}" />
            </td>
        </tr>
        <tr>
            <th class="em14">{{ __('labels.user_common_payment.brand_name') }}</th>
            <td>
                <a href="#">
                    <img height="80" width="120" src="{{ $data['image_url']['filepath'] ?? '' }}">クリックして拡大 >>
                    <input type="hidden" name="image_trademark" value="{{ $data['image_url']['filepath'] ?? 0 }}" />
                    <input type="hidden" name="image_url" value="{{ $data['image_url']['filepath'] ?? 0 }}" />
                </a>
            </td>
        </tr>
    </table>
</div>
