<?php

namespace App\Http\Requests\User\Withdraw;

use Illuminate\Foundation\Http\FormRequest;

class ConfirmVerifyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'info_member_id' => 'required',
            'password' => 'required|min:8|max:32|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,32}$/',
            'code' => 'required',
            'reason_withdraw' => 'max:255',
        ];
    }

    /**
     * Get the message that apply to the request.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'password.regex' => __('messages.valid_password'),
        ];
    }
}
