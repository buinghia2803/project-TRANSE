<?php

namespace App\Http\Requests\User\ComparisonTrademarkResult;

use Illuminate\Foundation\Http\FormRequest;

class PostReReplyRefusalPreQuestionRequest extends FormRequest
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
            'data.*.answer' => 'required|max:500',
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
            'data.*.answer.required' => __('messages.general.Common_E001'),
            'data.*.answer.max' => __('messages.general.Common_E024'),
        ];
    }
}
