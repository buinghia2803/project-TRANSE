<ul class="r_c mb10 clearfix">
    <li><label><input type="radio" name="type_search" value="and" checked
                {{ (isset($dataSession['type_search']) && $dataSession['type_search'] == 'and') ? 'checked' : '' }} />{{ __('labels.payment_all.filter_radio_all') }}
        </label></li>
    <li><label><input type="radio" name="type_search"
                      value="or" {{ (isset($dataSession['type_search']) && $dataSession['type_search'] == 'or')  ? 'checked' : '' }} />{{ __('labels.payment_all.filter_radio_only') }}
        </label></li>
</ul>
<table class="normal_a mb10">
    @for($i = 0; $i < 3; $i++)
        <tr class="search-item">
            <td>
                <select class="search_field w-100" name="search[{{ $i }}][field]">
                    @foreach($searchFields as $key => $item)
                        <option value="{{ $key }}"
                                {{ !empty($dataSession['search']) && $dataSession['search'][$i]['field'] == $key ? 'selected' : '' }} data-typing="{{ $item['typing'] ?? 'text' }}">{{ $item['title'] }} </option>
                    @endforeach
                    <input type="hidden" name="search[{{ $i }}][typing]" class="typing"
                           value="{{ !empty($dataSession['search']) && $dataSession['search'][$i]['typing'] == 'date' ? 'date' : 'text' }}"/>
                </select>
            </td>
            <td>
                <input
                    type="{{ !empty($dataSession['search']) && $dataSession['search'][$i]['typing'] == 'date' ? 'date' : 'text' }}"
                    class="search_value w-100" name="search[{{ $i }}][value]"
                    value="{{ (!empty($dataSession['search']) && $dataSession['search'][$i]['value']) ? $dataSession['search'][$i]['value'] : '' }}"/>
            </td>
            <td>
                <select class="search_condition w-100" name="search[{{ $i }}][condition]">
                    @if (!empty($dataSession['search']))
                        @if($dataSession['search'][$i]['typing'] == 'date')
                            @foreach($conditionDate as $key => $text)
                                <option
                                    value="{{ $key }}" {{ !empty($dataSession['search']) && $dataSession['search'][$i]['condition'] == $key ? 'selected' : '' }}>{{ $text }}</option>
                            @endforeach
                        @elseif($dataSession['search'][$i]['typing'] == 'text' && !empty($dataSession['search'][$i]['field']))
                            @foreach($conditions as $key => $text)
                                <option
                                    value="{{ $key }}" {{ !empty($dataSession['search']) && $dataSession['search'][$i]['condition'] == $key ? 'selected' : '' }}>{{ $text }}</option>
                            @endforeach
                        @else
                            @foreach($conditionsAll as $key => $text)
                                <option
                                    value="{{ $key }}" {{ !empty($dataSession['search']) && $dataSession['search'][$i]['condition'] == $key ? 'selected' : '' }}>{{ $text }}</option>
                            @endforeach
                        @endif
                    @else
                        @foreach($conditionsAll as $key => $text)
                            <option
                                value="{{ $key }}">{{ $text }}</option>
                        @endforeach
                    @endif

                </select>
            </td>
        </tr>
    @endfor
</table>
<p class="eol">
    <input type="hidden" name="page" value="{{ Request::get('page') ?? '' }}"/>
    <input type="submit" value="{{ __('labels.payment_all.search') }}" class="btn_a submit-search"/>
</p>

