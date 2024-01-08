@extends('user.layouts.app')

@section('headerSection')
<link rel="stylesheet" href="{{ asset('common/css/custom-css.css') }}">
@endsection

@section('main-content')
<div id="contents" class="normal">

@include('user.modules.draft.comming-soon')

</div>
@endsection

@section('footerSection')
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        const errorMessageInfoMemberIdRequired = '{{ __('validation.required', ['attribute' => __('messages.common.errors.Common_E001')]) }}';
        const errorMessageInfoMemberIdRequiredMaxCharacter = '{{ __('validation.max.string', ['attribute' => __('messages.common.errors.Common_E006'), 'max' => '30']) }}';
        const errorMessageInfoMemberIdRequiredMinCharacter = '{{ __('validation.min.string', ['attribute' => __('messages.common.errors.Common_E006'), 'min' => '8']) }}';
        const errorMessagePasswordRequired = '{{ __('validation.required', ['attribute' => __('messages.common.errors.Common_E001')]) }}';
        const errorMessagePasswordMaxCharacter = '{{ __('validation.max.string', ['attribute' => __('messages.common.errors.Common_E005'), 'max' => '16']) }}';
        const errorMessagePasswordMinCharacter = '{{ __('validation.min.string', ['attribute' => __('messages.common.errors.Common_E005'), 'min' => '8']) }}';

        function validate() {
            validation('#form', {
            'info_member_id': {
                required: true,
                minlength : 8,
                maxlength : 30,
            },
            'password': {
                required: true,
                minlength : 8,
                maxlength : 16,
            }
            }, {
                'info_member_id': {
                    required: errorMessageInfoMemberIdRequired,
                    minlength: errorMessageInfoMemberIdRequiredMinCharacter,
                    maxlength: errorMessageInfoMemberIdRequiredMaxCharacter
                },
                'password': {
                    required: errorMessagePasswordRequired,
                    minlength: errorMessagePasswordMinCharacter,
                    maxlength: errorMessagePasswordMaxCharacter
                },
            })
        }
        validate()
        $('#btn-clear').click(function() {
            $('#password').val('')
            $('#info_member_id').val('')
            $('#form').validate().destroy()
            validate()
        })
    </script>
@endsection
