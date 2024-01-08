<?php

namespace App\Http\Requests\Admin\A203S;

use Illuminate\Foundation\Http\FormRequest;

class PostRefusalResponsePlaneSupervisorRequest extends FormRequest
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
            'content' => 'nullable|max:1000',
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
            'content.max' => __('messages.general.Common_E026'),
        ];
    }
}
