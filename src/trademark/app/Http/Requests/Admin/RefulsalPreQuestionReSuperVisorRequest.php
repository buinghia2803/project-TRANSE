<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RefulsalPreQuestionReSuperVisorRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
            'user_response_deadline' => 'required',
            'question.*' => 'required|max:500',
            'content' => 'nullable|max:500',
        ];
    }

    /**
     * Messages
     *
     * @return array
     */
    public function messages()
    {
        return [
            'user_response_deadline.required' => __('messages.general.Common_E001'),
            'question.*.required' => __('messages.general.Common_E001'),
            'question.*.max' => __('messages.general.Common_E024'),
            'content.max' => __('messages.general.Common_E024'),
        ];
    }
}
