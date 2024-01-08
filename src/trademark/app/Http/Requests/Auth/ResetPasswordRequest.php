<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'password' => 'required|min:8|max:16|regex:/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,16}$/',
            'password_confirm' => 'required|same:password',
        ];
    }

    /**
     * Set the messages that apply to the request.
     *
     * @return  array
     */
    public function messages(): array
    {
        return [
            'password.regex' => __('messages.valid_password'),
        ];
    }
}
