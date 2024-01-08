<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;

class ForgotPasswordRequest extends FormRequest
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
     * Get the attributes that apply to the request.
     *
     * @return  array
     */
    public function attributes(): array
    {
        return [
            'email' => __('labels.email'),
        ];
    }
}
