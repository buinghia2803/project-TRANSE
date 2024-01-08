<?php

namespace App\Http\Requests\MailTemplate;

use App\Http\Requests\FormRequest;
use Illuminate\Support\Facades\Validator;

class UpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return  array
     */
    public function rules(): array
    {
        Validator::extend("emails", function ($attribute, $value) {
            foreach ($value as $email) {
                $validator = Validator::make([
                    'email' => $email,
                ], [
                    'email' => 'required|email',
                ]);
                if ($validator->fails()) {
                    return false;
                }
            }
            return true;
        });

        return [
            'cc' => 'nullable|emails',
            'bcc' => 'nullable|emails',
            'subject' => 'required',
            'content' => 'required',
            'attachment' => 'nullable',
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
            'cc' => __('labels.mail_template_cc'),
            'bcc' => __('labels.mail_template_bcc'),
            'subject' => __('labels.mail_template_subject'),
            'content' => __('labels.mail_template_content'),
            'attachment' => __('labels.mail_template_attachment'),
        ];
    }
}
