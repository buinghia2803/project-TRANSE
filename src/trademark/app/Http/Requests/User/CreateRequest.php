<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;

class CreateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return  array
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|max:32|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,32}$/',
            'password_confirm' => 'required|same:password',
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
            'first_name' => __('labels.user_first_name'),
            'last_name' => __('labels.user_last_name'),
            'email' => __('labels.email'),
            'password' => __('labels.password'),
            'password_confirm' => __('labels.password_confirm'),
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
