<?php

namespace App\Http\Requests\Refusal\Select;

use App\Models\Payment;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrCreateSelect02Request extends FormRequest
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
            'm_nation_id' => 'nullable',
            'payment_type' => ['nullable', Rule::in([Payment::CREDIT_CARD, Payment::BANK_TRANSFER])],
            'payer_type' => ['nullable', Rule::in([PAYER_TYPE_TAX_AGENT, PAYER_TYPE_REGIS_ADDRESS_OVERSEAS])],
            'payer_name' => 'nullable',
            'payer_name_furigana' => 'nullable|regex:/^[ぁ-ん－・]+$/',
            'is_choice.*' => 'required',
            'from_page' => 'required',
            'trademark_id' => 'required',
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
            'm_nation_id.required' => __('messages.general.Common_E001'),
            'trademark_id.required' => __('messages.general.Common_E001'),
            'from_page.required' => __('messages.general.Common_E001'),
            'payment_type.required' => __('messages.general.Common_E001'),
            'payer_type.required' => __('messages.general.Common_E001'),
            'payer_name.required' => __('messages.general.Common_E001'),
            'payer_name_furigana.required' => __('messages.general.Common_E001'),
            'is_choice.*.required' => __('messages.general.Common_E001'),
            'payer_name_furigana.regex' => __('messages.common.errors.Common_E018'),
        ];
    }
}
