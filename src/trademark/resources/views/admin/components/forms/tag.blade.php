@php
    /**
     * @include('admin.components.forms.tag', [])
     * @param string        $labelClass EG: 'col-12 col-lg-3 col-xl-2 col-form-label'
     * @param string        $bodyClass EG: 'col-12 col-lg-9 col-xl-10'
     * @param string        $name
     * @param string        $slug
     * @param array|string  $value
     * @param string        $label
     * @param bool          $required
     * @param bool          $select2
     * @param array         $options
     * @param bool          $isMultiple
     * @param bool          $disabled
     * @param array         $disableKey
     */
    $slug = $slug ?? Str::slug($name);
    $value = old($name, isset($value) ? ((is_array($value)) ? $value : [ $value ]) : []);

    $options = array_unique(array_merge($options ?? [], $value));
@endphp

<div class="form-group row tTag {{ (isset($select2) && $select2 == true) ? 'tSelect2' : '' }}">
    <label for="{{ $slug ?? '' }}" class="{{ $labelClass ?? 'col-12 col-lg-3 col-xl-2 col-form-label' }}">
        @lang($label ?? '')
        @if(isset($required) && $required == true) <span class="text-red">*</span> @endif
    </label>
    <div class="{{ $bodyClass ?? 'col-12 col-lg-9 col-xl-10' }}">
        <div class="select-group">
            <select
                class="form-control @if(isset($select2) && $select2 == true) tag-box @endif @error($name ?? '') is-invalid @enderror"
                name="{{ $name ?? '' }}[]"
                id="{{ $slug ?? '' }}"
                @if ($required == true) required="required" @endif
                @if (isset($disabled) && $disabled == true) disabled="disabled" @endif
                @if (isset($isMultiple) && $isMultiple == true) multiple @endif
            >
                @foreach ($options ?? [] as $item)
                    <option value="{{ $item ?? '' }}"
                            @if(in_array($item, $value)) selected @endif
                            @if(isset($disableKey) && in_array($item, $disableKey)) disabled="disabled" @endif
                    >@lang($item ?? '')</option>
                @endforeach
            </select>
        </div>
        @error($name ?? '') <span class="error">{{ $message ?? '' }}</span> @enderror
    </div>
</div>
