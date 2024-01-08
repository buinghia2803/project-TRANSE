<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\FormRequest;

class CheckUserInAnswerRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return  array
     */
    public function rules()
    {
        return [
            'info_answer' => 'required|string|between:1,100',
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
            'info_answer.required' => __('messages.common.errors.Common_E001'),
        ];
    }
}
