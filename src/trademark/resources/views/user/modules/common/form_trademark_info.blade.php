<!-- information trademark -->
<h3>{{ __('labels.form_trademark_information.title') }}</h3>
<dl class="w16em clearfix">

    <dt>{{ __('labels.form_trademark_information.trademark_type') }} <span class="red">*</span></dt>
    <dd class="fRadio">
        <ul class="r_c radio-group show-error">
            <li>
                <label>
                    <input type="radio" name="type_trademark" class="type_trademark_letter"
                        {{ old('type_trademark', $trademark->type_trademark ?? null) == 1 ? 'checked' : '' }}
                        value="1" />{{ __('labels.form_trademark_information.type_letter') }}
                </label>
            </li>
            <li>
                <label>
                    <input type="radio" name="type_trademark" class="type_trademark_other"
                        {{ old('type_trademark', $trademark->type_trademark ?? null) == 2 ? 'checked' : '' }}
                        value="2" />{{ __('labels.form_trademark_information.type_other') }}
                </label>
            </li>
            <br>
        </ul>
        @error('type_trademark')
            <div class="notice">{{ $message ?? '' }}</div>
        @enderror
    </dd>

    <dt class="dd_name_trademark">{{ __('labels.form_trademark_information.trademark_name') }} <span
            class="red">*</span>{{ __('labels.form_trademark_information.trademark_name_note') }}</dt>
    <dd class="dd_name_trademark">
        <input type="text" name="name_trademark" id="trademark_name"
               value="{{ isset($trademark->name_trademark) ? $trademark->name_trademark : old('name_trademark', request()->name) }}"/>
        @error('name_trademark')
            <div class="notice">{{ $message ?? '' }}</div>
        @enderror
    </dd>

    <dt class="dd_image_trademark">{{ __('labels.form_trademark_information.trademark_image') }}<span class="red">*</span></dt>
    <dd class="dd_image_trademark">
        <input type="file" name="image_trademark" /><span class="image_trademark_note">{{ __('labels.form_trademark_information.trademark_image_note') }}</span><br />
        @if (isset($trademark->image_trademark))
            @php
                $convertItem = explode('/', $trademark->image_trademark);
            @endphp
            <a id="image_trademark_a" href="{{ asset($trademark->image_trademark) }}" target="_blank">{{ isset($trademark->image_trademark) ? $convertItem[count($convertItem) - 1] : '' }}</a>
            <input type="hidden" name="image_trademark_old" id="image_trademark_old" value="{{ $trademark->image_trademark }}">
        @endif
    </dd>
    @error('image_trademark')
        <div class="notice">{{ $message ?? '' }}</div>
    @enderror
    <dt>{{ __('labels.form_trademark_information.trademark_reference_number') }}</dt>
    <dd>
        <input type="text" name="reference_number" id="reference_number" value="{{ old('reference_number', $trademark->reference_number ?? null) }}" />
        @error('reference_number')
            <div class="notice">{{ $message ?? '' }}</div>
        @enderror
    </dd>
</dl>
@section('footerSection')
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        const errorMessageRequired = '{{ __('messages.common.errors.Common_E001') }}';
        const errorMessageTrademarkNameInvalid = '{{ __('messages.general.Register_U001_E006') }}';
        const errorMessageTrademarkNameMaxLength = '{{ __('messages.general.Register_U001_E006') }}';
        const errorMessageReferenceNumberMaxLength = '{{ __('messages.general.support_U011_E002') }}';
        const errorMessageHalfWidth = '{{ __('messages.common.errors.Common_E006') }}';
        const errorMessageTrademarkImageInvalid = '{{ __('messages.trademark_form_information.errors.trademark_image_invalid') }}';
        const errorMessageContentMaxLength = '{{ __('messages.common.errors.Common_E031') }}';
        const errorMessageIsValidRequired = '{{ __('messages.profile_edit.validate.Common_E001') }}';
        const trademarkInfoRules = {
            'type_trademark': {
                required: true,
            },
            'name_trademark': {
                required: () => {
                    return +$("input[name='type_trademark']:checked").val() == 1;
                },
                maxlength: 30,
                isFullwidthSpecial: true,
            },
            'reference_number': {
                maxlength: 20,
                isFullwidthSpecial: true,
            },
            'image_trademark': {
                required: () => {
                    return +$("input[name='type_trademark']:checked").val() == 2 && !$('#image_trademark_a').length;
                },
                formatFile: true,
                checkFileSize: $('meta[name=max-filesize]').attr('content'),
            },
            'product_name[]': {
                required: true,
                maxlength: 255
            }
        }

        const trademarkInfoMessages = {
            type_trademark: {
                required: errorMessageSelectRequired,
            },
            name_trademark: {
                required: errorMessageRequired,
                maxlength: errorMessageTrademarkNameMaxLength,
                isFullwidthSpecial: errorMessageTrademarkNameInvalid,
            },
            reference_number: {
                maxlength: errorMessageReferenceNumberMaxLength,
                isFullwidthSpecial: errorMessageHalfWidth,
            },
            image_trademark: {
                required: errorMessageRequired,
                formatFile: errorMessageTrademarkImageInvalid,
                checkFileSize: errorMessageTrademarkImageInvalid
            },
            'product_name[]': {
                required: errorMessageRequired,
                maxlength: errorMessageContentMaxLength
            }
        }
    </script>
    <script src="{{ asset('end-user/common/form_trademark/js/index.js') }}"></script>
@endsection
