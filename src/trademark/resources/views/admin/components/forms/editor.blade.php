@php
    /**
     * @include('admin.components.forms.editor', [])
     * @param string        $labelClass EG: 'col-12 col-lg-3 col-xl-2 col-form-label'
     * @param string        $bodyClass EG: 'col-12 col-lg-9 col-xl-10'
     * @param string        $name
     * @param string        $slug
     * @param string        $value
     * @param string        $label
     * @param string        $placeholder
     * @param bool          $required
     */
    $slug = $slug ?? Str::slug($name);
@endphp
<div class="form-group row fEditor">
    <label for="{{ $slug ?? '' }}" class="{{ $labelClass ?? 'col-12 col-lg-3 col-xl-2 col-form-label' }}">
        @lang($label ?? '')
        @if(isset($required) && $required == true) <span class="text-red">*</span> @endif
    </label>
    <div class="{{ $bodyClass ?? 'col-12 col-lg-9 col-xl-10' }}">
        <textarea
            class="form-control tinymce @error($name ?? '') is-invalid @enderror"
            name="{{ $name ?? '' }}"
            id="{{ $slug ?? '' }}"
        >{{ old($name, $value ?? '') }}</textarea>
        @error($name ?? '') <span class="error">{{ $message ?? '' }}</span> @enderror
    </div>
</div>
