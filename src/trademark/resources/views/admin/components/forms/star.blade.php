@php
    /**
     * @include('admin.components.forms.text', [])
     * @param string        $labelClass EG: 'col-12 col-lg-3 col-xl-2 col-form-label'
     * @param string        $bodyClass EG: 'col-12 col-lg-9 col-xl-10'
     * @param string        $type text|number|password|email
     * @param string        $name
     * @param string        $slug
     * @param string        $value
     * @param string        $label
     * @param bool          $required
     */
    $slug = $slug ?? Str::slug($name);
    $value = (int) old($name, $value ?? 0);
@endphp
<div class="form-group row fStar">
    <label for="{{ $slug ?? '' }}" class="{{ $labelClass ?? 'col-12 col-lg-3 col-xl-2 col-form-label' }}">
        @lang($label ?? '')
        @if(isset($required) && $required == true) <span class="text-red">*</span> @endif
    </label>

    <div class="{{ $bodyClass ?? 'col-12 col-lg-9 col-xl-10' }}">
        <input
            type="hidden"
            name="{{ $name ?? '' }}"
            id="{{ $slug ?? '' }}"
            value="{{ $value ?? 0 }}"
        >

        <div class="star-group d-flex align-items-center">
            @for($i = 0; $i < floor($value); $i++)
                <i class="fa fa-star fz-30px cursor-pointer text-yellow" data-value="{{ $i + 1 }}"></i>
            @endfor

            @for($i = 0; $i < 5 - ceil($value); $i++)
                <i class="far fa-star fz-30px cursor-pointer text-yellow" data-value="{{ floor($value) + $i + 1 }}"></i>
            @endfor
        </div>

        @error($name ?? '') <span class="error">{{ $message ?? '' }}</span> @enderror
    </div>
</div>
