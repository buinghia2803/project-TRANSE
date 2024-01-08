<?php

namespace App\Http\Requests\Precheck;

use App\Http\Requests\FormRequest;

class createPrecheckResultRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return  array
     */
    public function rules(): array
    {
        return [
            'content' => 'max:1000',
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
            'content' => __('messages.Common_E026'),
        ];
    }
}
