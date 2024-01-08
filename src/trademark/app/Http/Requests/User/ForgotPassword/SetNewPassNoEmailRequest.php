<?php

namespace App\Http\Requests\User\ForgotPassword;

use Illuminate\Foundation\Http\FormRequest;

class SetNewPassNoEmailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'password' => 'required|required_with:password_confirmation|regex:/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,16}$/|same:password_confirmation',
            'password_confirmation' => '',
        ];
    }

    public function messages()
    {
        $errorMessageRequired = __('messages.common.errors.Common_E001');
        $errorMessagePasswordLength = __('messages.forgot_password.password_length');
        $errorMessagePasswordConfirm = __('messages.forgot_password.password_confirm');
        return [
            'password.required'                 => $errorMessageRequired,
            'password.min'                 => $errorMessagePasswordLength,
            'password.max'                 => $errorMessagePasswordLength,
            'password.regex'                 => $errorMessagePasswordLength,
            'password_confirmation.required'                 => $errorMessageRequired,
            'password_confirmation.min'                 => $errorMessagePasswordLength,
            'password_confirmation.max'                 => $errorMessagePasswordLength,
            'password_confirmation.regex'                 => $errorMessagePasswordLength,
            'password_confirmation.same'                 => $errorMessagePasswordConfirm,
        ];
    }
}
