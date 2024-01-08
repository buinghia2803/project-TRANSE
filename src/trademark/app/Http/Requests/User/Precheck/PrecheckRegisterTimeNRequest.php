<?php

namespace App\Http\Requests\User\Precheck;

use Illuminate\Foundation\Http\FormRequest;

class PrecheckRegisterTimeNRequest extends FormRequest
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
            'type_precheck' => 'required',
//            'm_product_ids.*' => 'required|array',
            'payment_type' => 'required',
            'cost_bank_transfer' => 'required',
            'payer_name' => 'required',
            'payer_type' => 'required|integer',
            'payer_name_furigana' => 'required',
            'm_nation_id' => 'required',
            'postal_code' => ['nullable','regex:/^[0-9]{1,7}$/','required_if:m_nation_id,==,' . NATION_JAPAN_ID],
            'm_prefecture_id' => 'nullable|required_if:info_nation_id,==,' . NATION_JAPAN_ID,
            'address_second' => 'nullable|required_if:m_nation_id,==,' . NATION_JAPAN_ID,
            'address_three' => '',
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
            'type_precheck.required' => __('messages.update_profile.form.Common_E001'),
//            'm_product_ids.*.required' => __('messages.update_profile.form.Common_E001'),
            'payment_type.required' => __('messages.update_profile.form.Common_E001'),
            'cost_bank_transfer.required' => __('messages.update_profile.form.Common_E001'),
            'payer_name.required' => __('messages.update_profile.form.Common_E001'),
            'payer_name_furigana.required' => __('messages.update_profile.form.Common_E001'),
            'payer_type.required' => __('messages.update_profile.form.Common_E001'),
            'm_nation_id.required' => __('messages.update_profile.form.Common_E001'),
            'postal_code.regex' => __('messages.update_profile.form.Common_E019'),
            'postal_code.required_if' => __('messages.update_profile.form.Common_E001'),
            'm_prefecture_id.required_if' => __('messages.update_profile.form.Common_E001'),
            'address_second.required_if' => __('messages.update_profile.form.Common_E001'),
            'address_three.required' => __('messages.update_profile.form.Common_E001'),
        ];
    }
}
