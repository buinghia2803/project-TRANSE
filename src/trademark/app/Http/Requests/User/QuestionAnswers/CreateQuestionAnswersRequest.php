<?php

namespace App\Http\Requests\User\QuestionAnswers;

use Illuminate\Foundation\Http\FormRequest;

class CreateQuestionAnswersRequest extends FormRequest
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
            'question_content' => 'required|max:1000',
            'question_attaching_file' => 'max:3000',
        ];
    }

    public function messages()
    {
        $errorMessageRequired = __('messages.required');
        $errorMessageInvalid = __('messages.question_answers.QA_U000_E001');
        $errorMessageMaxSize = __('messages.question_answers.max_size_image');
        return [
            'question_content.required'                 => $errorMessageRequired,
            'question_content.max'                 => $errorMessageInvalid,
            'question_attaching_file.max'          => $errorMessageMaxSize
        ];
    }
}
