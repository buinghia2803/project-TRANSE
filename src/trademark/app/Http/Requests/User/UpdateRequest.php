<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;

class UpdateRequest extends FormRequest
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
            'email' => 'required|email|unique:users,email,' . $this->segment(3),
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
        ];
    }
}
