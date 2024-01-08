@php
    /**
     * @include('admin.components.forms.checkbox', [])
     * @param string        $labelClass EG: 'col-12 col-lg-3 col-xl-2 col-form-label'
     * @param string        $bodyClass EG: 'col-12 col-lg-9 col-xl-10'
     * @param string        $name
     * @param string        $slug
     * @param array|string  $value
     * @param string        $label
     * @param bool          $required
     * @param array         $options
     * @param bool          $inline
     */
    $slug = $slug ?? Str::slug($name);

    $value = old($name, $value ?? []);
    $value = (is_array($value)) ? $value : [ $value ];
@endphp
<div class="form-group row fCheckbox">
    <label for="{{ $slug ?? '' }}" class="{{ $labelClass ?? 'col-12 col-lg-3 col-xl-2 col-form-label' }}">
        @lang($label ?? '')
        @if(isset($required) && $required == true) <span class="text-red">*</span> @endif
    </label>
    <div class="{{ $bodyClass ?? 'col-12 col-lg-9 col-xl-10' }}">
        <div class="checkbox-group">
            @foreach ($options as $key => $item)
                <div class="icheck-primary @if(isset($inline) && $inline == true) icheck-inline @endif">
                    <input
                        type="checkbox"
                        name="{{ $name }}[]"
                        id="{{ $slug ?? '' }}_{{ $loop->index }}"
                        value="{{ $key ?? 0 }}"
                        {{ (in_array($key, $value)) ? 'checked' : '' }}
                    />
                    <label for="{{ $slug ?? '' }}_{{ $loop->index }}">{{ __($item) }}</label>
                </div>
            @endforeach
        </div>
        @error($name ?? '') <span class="error">{{ $message ?? '' }}</span> @enderror
    </div>
</div>
