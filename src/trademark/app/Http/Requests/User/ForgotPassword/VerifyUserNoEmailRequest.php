<?php

namespace App\Http\Requests\User\ForgotPassword;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class VerifyUserNoEmailRequest extends FormRequest
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
            ]
        ];
    }

    public function messages()
    {
        $errorMessageRequired = __('messages.required');
        $errorMessageExists = __('messages.forgot_password.Forgot_Password_U000_E010');
        return [
            'info_member_id.required'                 => $errorMessageRequired,
            // 'info_member_id.max'                 => $errorMessageRequired,
            // 'info_member_id.min'                 => $errorMessageRequired,
            'email.required'                 => $errorMessageRequired,
            'code.required'                 => $errorMessageRequired,
            'code.exists'                      => $errorMessageExists,
        ];
    }
}
