<div class="info">
    <h3>【商標情報】</h3>
    <table class="info_table">
        <tr>
            <th>お客様整理番号</th>
            <td colspan="3">{{ $data['reference_number'] ?? '' }}</td>
        </tr>
        <tr>
            <th>申込番号</th>
            <td> {{ $data['trademark_number'] ?? '' }} </td>
            <th>申込日</th>
            <td>{{ isset($data['created_at']) ? \Carbon\Carbon::parse($data['created_at'])->format('Y/m/d') : '' }}</td>
        </tr>
        <tr>
            <th>商標出願種別</th>
            <td colspan="3">{{ isset($data['type_trademark']) ? \App\Models\Trademark::listTradeMarkTypeOptions()[$data['type_trademark']] : ''  }}</td>
        </tr>
        <tr>
            <th>商標名</th>
            @if (isset($from_page) && $from_page == U021C)
                <td colspan="3">
                    <input type="text" name="name_trademark" class="name_trademark" value="{{ old('name_trademark', $data['name_trademark'] ?? '') }}" />
                </td>
            @else
                <td colspan="3">{{ $data['name_trademark'] ?? '' }}</td>
            @endif
        </tr>
    </table>
    @if (isset($from_page) && $from_page == U020B)
    <font color="orange"><br>
        {!! __('messages.precheck.note_from_page_u020b') !!}
    </font>
    @endif
</div><!-- /info -->
