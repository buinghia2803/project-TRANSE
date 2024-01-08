<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;

class SignUpRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return  array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email',
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
            'email.required' => __('messages.signup.form.required'),
            'email.email' => __('messages.signup.form.email_format'),
        ];
    }

    /**
     * Get the attributes that apply to the request.
     *
     * @return  array
     */
    public function attributes(): array
    {
        return [];
    }
}
