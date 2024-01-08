<?php

namespace App\Http\Requests\User\Profile;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
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
            'info_nation_id' => 'required|integer',
            'info_postal_code' => 'nullable|regex:/^[0-9]{1,7}$/|required_if:info_nation_id,==,' . NATION_JAPAN_ID,
            'info_prefectures_id' => 'nullable|required_if:info_nation_id,==,' . NATION_JAPAN_ID,
            'info_address_second' => 'nullable|required_if:info_nation_id,==,' . NATION_JAPAN_ID,
            'info_address_three' => 'nullable',
            'info_phone' => 'required|regex:/^[0-9]{1,11}$/',
            'info_member_id' => [
                'required',
                'unique:users,info_member_id,' . $this->user()->id,
                'regex:/^(?=.*[A-Za-z])(?=.*[0-9])[A-Za-z0-9-._@]{8,30}$/',
            ],
            'password' => 'nullable|required_with:re_password|regex:/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,16}$/|same:re_password',
            're_password' => '',
            'info_question' => ['required'],
            'info_answer' => ['required'],
            'contact_type_acc' => 'required|integer',
            'contact_name' => [
                'required',
                'string',
                'regex:/^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９－・ー]{1,50}$/',
            ],
            'contact_name_furigana' => [
                'required',
                'string',
                'regex:/^[ぁ-んァ-ン一ａ-ｚＡ-Ｚ０-９－・ー]{1,50}$/',
            ],
            'contact_name_department' => 'nullable|string',
            'contact_name_department_furigana' => 'nullable|string',
            'contact_name_manager' => 'nullable|string|required_if:contact_type_acc,==,' . CONTACT_TYPE_ACC_GROUP,
            'contact_name_manager_furigana' => 'nullable|string|required_if:contact_type_acc,==,' . CONTACT_TYPE_ACC_GROUP,
            'contact_nation_id' => 'required|integer',
            'contact_postal_code' => 'nullable|regex:/^[0-9]{1,7}$/|required_if:contact_nation_id,==,' . NATION_JAPAN_ID,
            'contact_prefectures_id' => 'nullable|required_if:contact_nation_id,==,' . NATION_JAPAN_ID,
            'contact_address_second' => 'nullable|required_if:contact_nation_id,==,' . NATION_JAPAN_ID,
            'contact_address_three' => 'nullable',
            'contact_phone' => ['required', 'regex:/^[0-9]{1,11}$/'],
            'contact_email_second' => [
                'nullable',
                'same:contact_email_second_confirm',
                'required_with:contact_email_second_confirm',
                'regex:/^[\s]{0,60}[a-zA-Z0-9][a-zA-Z0-9_+-\.]{0,60}@[a-zA-Z0-9-\.]{2,}(\.[a-zA-Z0-9]{2,4}){1,2}[\s]{0,60}$/',
            ],
            'contact_email_second_confirm' => [
                'nullable',
                'required_with:contact_email_second_confirm',
                'regex:/^[\s]{0,60}[a-zA-Z0-9][a-zA-Z0-9_+-\.]{0,60}@[a-zA-Z0-9-\.]{2,}(\.[a-zA-Z0-9]{2,4}){1,2}[\s]{0,60}$/',
            ],
            'contact_email_three' => [
                'nullable',
                'required_with:contact_email_three_confirm',
                'regex:/^[\s]{0,60}[a-zA-Z0-9][a-zA-Z0-9_+-\.]{0,60}@[a-zA-Z0-9-\.]{2,}(\.[a-zA-Z0-9]{2,4}){1,2}[\s]{0,60}$/',
            ],
            'contact_email_three_confirm' => '',

        ];
    }

    /**
     * Get the message that apply to the request.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'info_type_acc.required' => __('messages.update_profile.form.Common_E025'),
            'info_name.required' => __('messages.update_profile.form.Common_E001'),
            'info_name.regex' => __('messages.update_profile.form.Common_E016'),
            'info_name_furigana.required' => __('messages.update_profile.form.Common_E001'),
            'info_name_furigana.regex' => __('messages.update_profile.form.Common_E018'),
            'info_corporation_number.regex' => __('messages.update_profile.form.Register_U001_E004'),
            'info_nation_id.required' => __('messages.update_profile.form.Common_E025'),
            'info_postal_code.regex' => __('messages.update_profile.form.Common_E019'),
            'info_postal_code.required_if' => __('messages.update_profile.form.Common_E001'),
            'info_prefectures_id.required_if' => __('messages.update_profile.form.Common_E001'),
            'info_address_second.required_if' => __('messages.update_profile.form.Common_E001'),
            'info_phone.required' => __('messages.update_profile.form.Common_E001'),
            'info_phone.regex' => __('messages.update_profile.form.message_phone'),
            'info_member_id.required' => __('messages.update_profile.form.Common_E001'),
            'info_member_id.unique' => __('messages.update_profile.already_registered'),
            'info_member_id.regex' => __('messages.general.Common_E006'),
            'password.required' => __('messages.update_profile.form.Common_E001'),
            'password.regex' => __('messages.update_profile.form.Common_E005'),
            'password.same' => __('messages.update_profile.form.Register_U001_E002'),
            'info_gender.required_if' => __('messages.update_profile.form.Common_E001'),
            'info_birthday.required_if' => __('messages.update_profile.form.Common_E025'),
            'info_question.required' => __('messages.update_profile.form.Common_E001'),
            'info_question.regex' => __('messages.update_profile.form.Register_U001_E008'),
            'info_answer.required' => __('messages.update_profile.form.Common_E001'),
            'info_answer.regex' => __('messages.update_profile.form.Register_U001_E007'),
            'contact_type_acc.required' => __('messages.update_profile.form.Common_E001'),
            'contact_name.required' => __('messages.update_profile.form.Common_E001'),
            'contact_name.regex' => __('messages.update_profile.form.Common_E016'),
            'contact_name_furigana.required' => __('messages.update_profile.form.Common_E001'),
            'contact_name_furigana.regex' => __('messages.update_profile.form.Common_E016'),
            'contact_name_department.max' => __('messages.update_profile.form.Common_E021'),
            'contact_name_department_furigana.max' => __('messages.update_profile.form.Common_E022'),
            'contact_name_manager.required_if' => __('messages.update_profile.form.Common_E001'),
            'contact_name_manager.max' => __('messages.update_profile.form.Common_E021'),
            'contact_name_manager_furigana.required_if' => __('messages.update_profile.form.Common_E001'),
            'contact_name_manager_furigana.max' => __('messages.update_profile.form.Common_E022'),
            'contact_nation_id.required' => __('messages.update_profile.form.Common_E025'),
            'contact_postal_code.regex' => __('messages.update_profile.form.Common_E019'),
            'contact_postal_code.required_if' => __('messages.update_profile.form.Common_E001'),
            'contact_prefectures_id.required_if' => __('messages.update_profile.form.Common_E001'),
            'contact_address_second.regex' => __('messages.update_profile.form.Common_E020'),
            'contact_address_second.required_if' => __('messages.update_profile.form.Common_E001'),
            'contact_phone.required' => __('messages.update_profile.form.Common_E001'),
            'contact_phone.regex' => __('messages.update_profile.form.message_phone'),
            'contact_email_second.regex' => __('messages.update_profile.form.Common_E002'),
            'contact_email_three.regex' => __('messages.update_profile.form.Common_E002'),
        ];
    }
}
