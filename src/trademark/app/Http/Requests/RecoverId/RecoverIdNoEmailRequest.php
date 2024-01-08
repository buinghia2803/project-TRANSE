<?php

namespace App\Http\Requests\RecoverId;

use App\Http\Requests\FormRequest;

class RecoverIdNoEmailRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return  array
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email|max:255',
            'password' => 'required|max:16|min:8|regex:/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,16}$/',
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
            'required' => __('messages.common.errors.Common_E001'),
            'email' => __('messages.common.errors.Common_E002'),
            'password' => __('messages.common.errors.Common_E002'),
        ];
    }
}
