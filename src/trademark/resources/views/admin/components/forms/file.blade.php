@php
    /**
     * @include('admin.components.forms.file', [])
     * @param string        $labelClass EG: 'col-12 col-lg-3 col-xl-2 col-form-label'
     * @param string        $bodyClass EG: 'col-12 col-lg-9 col-xl-10'
     * @param string        $name
     * @param string        $slug
     * @param string        $value
     * @param string        $label
     * @param string        $placeholder
     * @param bool          $required
     * @param string        $buttonText
     */
    $slug = $slug ?? Str::slug($name);

    $fileName = null;
    if (!empty($value)) {
        $explore = explode('/', $value);
        $fileName = $explore[count($explore)-1];
    }
@endphp

<div class="form-group row fFile">
    <label class="{{ $labelClass ?? 'col-12 col-lg-3 col-xl-2 col-form-label' }}">
        @lang($label ?? '')
        @if(isset($required) && $required == true) <span class="text-red">*</span> @endif
    </label>
    <div class="{{ $bodyClass ?? 'col-12 col-lg-9 col-xl-10' }}">

        <div class="file-group">
            <input type="hidden" name="{{ $name }}_input" class="file-preview-input" value="{{ $value ?? '' }}" />
            <input type="file" accept="image/*,.xls,.xlsx,pdf" class="file-preview-file"
                   name="{{ $name }}"
                   id="{{ $slug ?? \Str::slug($name ?? '') }}"
                   value="{{ $value ?? '' }}"
            />

            <div class="file-button d-flex flex-wrap align-items-center">
                <label class="btn btn-primary text-white mb-2"
                       for="{{ $slug ?? \Str::slug($name ?? '') }}"
                >{{ __($buttonText ?? '') }}</label>
            </div>

            <div class="file-preview mt-2 mb-2 {{ empty($value) ? 'd-none' : '' }}">
                <div class="d-flex align-items-center">
                    <a href="{{ !empty($value) ? $value : '' }}" class="file-preview-src">{{ $fileName ?? '' }}</a>
                    <div class="file-preview-remove cursor-pointer"><i class="fa fa-times"></i></div>
                </div>
            </div>
        </div>

        @error($name ?? '') <span class="error">{{ $message ?? '' }}</span> @enderror
    </div>
</div>
