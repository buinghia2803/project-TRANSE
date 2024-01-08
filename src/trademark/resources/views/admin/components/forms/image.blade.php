@php
    /**
     * @include('admin.components.forms.image', [])
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
    $defaultImage = asset('admin_assets/images/default_image.png');
@endphp
<div class="form-group row fImage">
    <label class="{{ $labelClass ?? 'col-12 col-lg-3 col-xl-2 col-form-label' }}">
        @lang($label ?? '')
        @if(isset($required) && $required == true) <span class="text-red">*</span> @endif
    </label>
    <div class="{{ $bodyClass ?? 'col-12 col-lg-9 col-xl-10' }}">

        <div class="image-group">
            <input type="hidden" name="{{ $name }}_input" class="image-preview-input" value="{{ $value ?? '' }}" />
            <input type="file" accept="image/*" class="image-preview-file"
                name="{{ $name }}"
                id="{{ $slug ?? \Str::slug($name ?? '') }}"
                value="{{ $value ?? '' }}"
            />

            <div class="image-button d-flex flex-wrap align-items-center">
                <label class="btn btn-primary text-white mb-2"
                       for="{{ $slug ?? \Str::slug($name ?? '') }}"
                >{{ __($buttonText ?? '') }}</label>
                <p class="ml-3 mb-2">{!! $note ?? '' !!}</p>
            </div>

            @error($name ?? '') <span class="error">{{ $message ?? '' }}</span> @enderror

            <div class="image-preview position-relative d-inline-block mt-2 mb-2">
                <img src="{{ !empty($value) ? $value : $defaultImage }}" class="image-preview-src object-fit-contain" alt="" width="150px" height="150px" />
                <div class="image-preview-remove position-absolute cursor-pointer {{ empty($value) ? 'd-none' : '' }}"><i class="fa fa-times"></i></div>
            </div>
        </div>
    </div>
</div>
