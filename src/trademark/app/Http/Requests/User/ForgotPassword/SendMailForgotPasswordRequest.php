<?php

namespace App\Http\Requests\User\ForgotPassword;

use Illuminate\Foundation\Http\FormRequest;

class SendMailForgotPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return boolean
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'info_member_id' => 'required|max:30|min:8|regex:/^(?=.*[A-Za-z])(?=.*[0-9])[A-Za-z0-9-._@]{8,30}$/',
            'email' => 'required|max:255|regex:/^\w+([\+.-]?\w+[＿]?)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/u',
        ];
    }
}
