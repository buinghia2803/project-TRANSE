<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginUserRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'info_member_id' => 'required|min:8|max:16',
            'password' => 'required|min:8|max:16'
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
            'info_member_id.required' => __('messages.common.errors.Common_E001'),
            'info_member_id.min' => __('messages.common.errors.Common_E006'),
            'info_member_id.max' => __('messages.common.errors.Common_E006'),
            'password.required' => __('messages.common.errors.Common_E001'),
            'password.min' => __('messages.common.errors.Common_E005'),
            'password.max' => __('messages.common.errors.Common_E005'),
        ];
    }
}
