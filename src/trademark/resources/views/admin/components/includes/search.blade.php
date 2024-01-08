<form action="" method="get">
    <input type="hidden" name="search" value="{{ SEARCH }}">
    <div class="card">
        <div class="card-body pb-0">
            <div class="row">
                @foreach($fields as $field)
                    @php
                        $fieldName = $field['name'] ?? '';
                        $label = $field['label'] ?? '';
                        $placeholder = $field['placeholder'] ?? $field['label'] ?? '';
                        $options = $field['options'] ?? '';
                    @endphp

                    @switch($field['type'])

                        @case(SEARCH_TEXT)
                            <div class="col-12 col-md-6 col-lg-3 mb-3">
                                <label for="id">{{ __($label) }}</label>
                                <input
                                    type="text"
                                    name="{{ $fieldName }}"
                                    id="{{ $fieldName }}"
                                    class="form-control"
                                    placeholder="{{ __($placeholder) }}"
                                    value="{{ request()->$fieldName ?? '' }}"
                                    autocomplete="off"
                                >
                            </div>
                        @break

                        @case(SEARCH_SELECT)
                            <div class="col-12 col-md-6 col-lg-3 mb-3">
                                <label for="status">{{ __($label) }}</label>
                                <select name="{{ $fieldName }}" id="{{ $fieldName }}" class="form-control" autocomplete="off">
                                    <option value="">{{ __('labels.all') }}</option>
                                    @foreach($options as $key => $labels)
                                        <option value="{{ $key ?? '' }}"
                                            {{ ((string) $key == request()->$fieldName) ? 'selected' : '' }}
                                        >{{ $labels ?? '' }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @break

                        @case(SEARCH_DATERANGE)
                            @php
                                $fieldStart = $fieldName.'_start';
                                $fieldEnd = $fieldName.'_end';
                            @endphp
                            <div class="col-12 col-md-6 col-lg-3 mb-3">
                                <label for="{{ $fieldStart }}">{{ __($label) }}</label>
                                <div class="d-flex align-items-center">
                                    <input
                                        type="text"
                                        name="{{ $fieldStart }}"
                                        id="{{ $fieldStart }}"
                                        class="form-control datepicker"
                                        value="{{ request()->$fieldStart ?? '' }}"
                                        autocomplete="off"
                                    >
                                    <span class="mr-1 ml-1">~</span>
                                    <input
                                        type="text"
                                        name="{{ $fieldEnd }}"
                                        id="{{ $fieldEnd }}"
                                        class="form-control datepicker"
                                        value="{{ request()->$fieldEnd ?? '' }}"
                                        autocomplete="off"
                                    >
                                </div>
                            </div>
                        @break

                    @endswitch
                @endforeach
            </div>
        </div>

        <div class="card-footer clearfix">
            <button type="submit" class="btn btn-primary btn-sm float-right">
                {{ __('labels.search') }}
            </button>
            @foreach($buttons ?? [] as $button)
                @php
                    $btnAttr = '';
                    foreach ($button['attr'] ?? [] as $attr => $attrValue) {
                        $btnAttr .= $attr . '="' . $attrValue . '" ';
                    }
                @endphp
                @switch($button['type'])
                    @case('button')
                        <button {!! $btnAttr ?? '' !!}>
                            {{ $button['label'] ?? '' }}
                        </button>
                    @break

                    @case('link')
                        <a {!! $btnAttr ?? '' !!}>
                            {{ $button['label'] ?? '' }}
                        </a>
                    @break
                @endswitch
            @endforeach
        </div>
    </div>
</form>
