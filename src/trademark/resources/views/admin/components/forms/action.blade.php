@php
    /**
     * @include('admin.components.forms.action', [])
     * @param string        $type
     * @param string        $labelClass EG: 'col-12 col-lg-3 col-xl-2 col-form-label'
     * @param string        $bodyClass EG: 'col-12 col-lg-9 col-xl-10'
     */
    if (isset($fixed) && $fixed == true) {
        $labelClass = $labelClass ?? 'd-none';
        $bodyClass = $bodyClass ?? 'w-100 d-flex justify-content-end pt-1 pb-0';
    }
@endphp
<div class="form-group row {{ isset($fixed) && $fixed == true ? 'form-submit-fixed' : '' }}">
    <label class="{{ $labelClass ?? 'col-12 col-lg-3 col-xl-2 col-form-label' }}"></label>
    <div class="{{ $bodyClass ?? 'col-12 col-lg-9 col-xl-10' }}">
        @switch($type)

            @case('create')
                <button type="submit" class="btn btn-primary mr-1 mb-1" name="redirect" value="edit">
                    {{ __('labels.save') }}
                </button>
                <button type="submit" class="btn btn-info mr-1 mb-1" name="redirect" value="index">
                    {{ __('labels.save_and_back_to_list') }}
                </button>
                <button type="button" class="btn bg-808080 text-white mr-1 mb-1"
                    onclick="@if(!empty($backUrl)) window.location = '{{ $backUrl }}' @else history.back() @endif"
                >
                    {{ __('labels.back') }}
                </button>
            @break

            @case('update-profile')
                <button type="submit" class="btn btn-primary mr-1 mb-1">
                    {{ __('labels.save') }}
                </button>
            @break

            @case('update-mail-template')
                <button type="submit" class="btn btn-primary mr-1 mb-1">
                    {{ __('labels.save') }}
                </button>
            @break

        @endswitch
    </div>
</div>

@if (isset($fixed) && $fixed == true)
    <script>
        formSubmitHeight = document.getElementsByClassName("form-submit-fixed")[0].offsetHeight;
        document.getElementsByClassName("content-wrapper")[0].style.paddingBottom = formSubmitHeight + 'px';
    </script>
@endif
