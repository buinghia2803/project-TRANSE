<?php

namespace App\Http\Requests\User;

use App\Http\Requests\FormRequest;

class AuthRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required_without:username|email',
            'username' => 'required_without:email',
            'password' => 'required',
        ];
    }
}
