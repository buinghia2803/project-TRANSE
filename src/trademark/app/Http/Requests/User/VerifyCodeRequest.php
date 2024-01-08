<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;

class VerifyCodeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return  array
     */
    public function rules(): array
    {
        return [
            'code' => 'required',
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
            'code.required' => __('messages.signup.form.required'),
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
