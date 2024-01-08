<?php

namespace App\Http\Requests\User\ForgotPassword;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ResetPasswordRequest extends FormRequest
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
    public function rules(Request $request)
    {
        return [
            'info_member_id' => 'required|max:30|min:8',
            'email' => 'required|max:255|regex:/^\w+([\+.-]?\w+[＿]?)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/u',
            'code' =>
            [
                'required',
                Rule::exists('authentications')->where(function ($query) use ($request) {
                    return $query->where('code', $request->code);
                }),
            ],
            'password' => 'required|nullable|min:8|max:32|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,32}$/',
            'password_confirmation' => 'required|nullable|min:8|max:32|same:password|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,32}$/',
        ];
    }

    public function messages()
    {
        $errorMessageRequired = __('messages.CM001_L001');
        $errorMessagePasswordLength = __('messages.forgot_password.password_length');
        $errorMessageMaxLength255 = __('messages.common.Common_Max_Length', ['attr' => 255]);
        return [
            'info_member_id.required'                 => $errorMessageRequired,
            'info_member_id.max'                 => $errorMessageRequired,
            'info_member_id.min'                 => $errorMessageRequired,
            'email.required'                 => $errorMessageRequired,
            'email.max'                 => $errorMessageMaxLength255,
            'email.min'                 => $errorMessageRequired,
            'password.required'                 => $errorMessageRequired,
            'password.min'                 => $errorMessagePasswordLength,
            'password.max'                 => $errorMessagePasswordLength,
            'password.regex'                 => $errorMessagePasswordLength,
            'password_confirmation.required'                 => $errorMessageRequired,
            'password_confirmation.min'                 => $errorMessagePasswordLength,
            'password_confirmation.max'                 => $errorMessagePasswordLength,
            'password_confirmation.regex'                 => $errorMessagePasswordLength,
        ];
    }
}
