@php
    /**
     * @include('admin.components.forms.textarea', [])
     * @param string        $labelClass EG: 'col-12 col-lg-3 col-xl-2 col-form-label'
     * @param string        $bodyClass EG: 'col-12 col-lg-9 col-xl-10'
     * @param string        $type text|number|password|email
     * @param string        $name
     * @param string        $slug
     * @param string        $value
     * @param string        $label
     * @param string        $placeholder
     * @param bool          $required
     * @param bool          $disabled
     * @param integer       $rows
     */
    $slug = $slug ?? Str::slug($name);
@endphp
<div class="form-group row fText">
    <label for="{{ $slug ?? '' }}" class="{{ $labelClass ?? 'col-12 col-lg-3 col-xl-2 col-form-label' }}">
        @lang($label ?? '')
        @if(isset($required) && $required == true) <span class="text-red">*</span> @endif
    </label>
    <div class="{{ $bodyClass ?? 'col-12 col-lg-9 col-xl-10' }}">
        <textarea
            type="{{ $type ?? 'text' }}"
            class="form-control @error($name ?? '') is-invalid @enderror"
            autocomplete="off"
            name="{{ $name ?? '' }}"
            id="{{ $slug ?? '' }}"
            rows="{{ $rows ?? '5' }}"
            placeholder="@lang($placeholder ?? $label ?? $name ?? '')"
            @if (isset($required) && $required == true) required="required" @endif
            @if (isset($disabled) && $disabled == true) disabled="disabled" @endif
        >{{ old($name, $value ?? '') }}</textarea>
        @error($name ?? '') <span class="error">{{ $message ?? '' }}</span> @enderror
    </div>
</div>
