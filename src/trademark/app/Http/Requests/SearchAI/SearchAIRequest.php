<?php

namespace App\Http\Requests\SearchAI;

use Illuminate\Foundation\Http\FormRequest;

class SearchAIRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name_trademark' => [
                'nullable',
                'max:30',
                'regex:/^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９　～！＠＃＄％＾＆＊（）＿＋ー＝￥」「；’・。、＜＞？”：｝｛｜]+$/',
            ],
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
            'name_trademark.max' => __('messages.common.errors.Register_U001_E006'),
            'name_trademark.regex' => __('messages.common.errors.Register_U001_E006'),
        ];
    }
}
