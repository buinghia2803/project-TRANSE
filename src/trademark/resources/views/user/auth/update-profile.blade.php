@extends('user.layouts.app')
@section('css')
    <link href="{{ asset('common/css/custom-css.css') }}" rel="stylesheet" type="text/css" />
    <style>
    .agree {
        position: relative;
    }

    #agree-error {
        position: absolute;
    }
</style>
@endsection

@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal update_profile">
        <h2>{{ __('labels.update_profile.update_profile_title') }}</h2>

        <form id="form" action="{{ route('auth.form-update-profile-post') }}" method="POST">
            @csrf
            @php
                $token = $_GET['token'];
            @endphp
            <input type="hidden" value="{{ $token }}" name="token_authen">
            <input type="hidden" value="{{ NATION_JAPAN_ID }}" id="nation-japan-id" />

            <p>
                {{ __('messages.update_profile.note_1') }}<br />
                {{ __('messages.update_profile.note_2') }}<br />
                <span class="red">*</span>{{ __('messages.update_profile.note_dot_err') }}
            </p>

            <h3>{{ __('labels.update_profile.update_profile_sub_title') }}</h3>

            <dl class="w18em eol clearfix">
                <dt>{{ __('labels.update_profile.form.info_type_acc') }}<span class="red">*</span></dt>
                <dd class="eInfoTypeAcc">
                    <ul class="r_c fInfoTypeAcc">
                        <li>
                            <label><input type="radio" name="info_type_acc" value="1"
                                    {{ old('info_type_acc', $params['info_type_acc'] ?? '' ) == 1 ? 'checked' : '' }} />{{ __('labels.update_profile.form.info_type_acc_1') }}</label>
                        </li>
                        <li>
                            <label><input type="radio" name="info_type_acc" value="2"
                                    {{ old('info_type_acc', $params['info_type_acc'] ?? '' ) == 2 ? 'checked' : '' }} />{{ __('labels.update_profile.form.info_type_acc_2') }}</label>
                        </li>
                        <br />
                    </ul>
                    @error('info_type_acc')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                </dd>

                <dt><span id="changeName">{{ __('labels.update_profile.form.info_name') }}</span> <span
                        class="red">*</span></dt>
                <dd>
                    <input type="text" name="info_name" class="trimSpace" value="{{ old('info_name', $params['info_name'] ?? '' ) }}" />
                    @error('info_name')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                    <p><span class="red" id="changeMessage">{{ __('messages.update_profile.note_name') }}</span></p>
                </dd>

                <dt><span
                        id="changeNameFurigana">{{ __('labels.update_profile.form.info_name_furigana_1') }}</span>{{ __('labels.update_profile.form.info_name_furigana_2') }}
                    <span class="red">*</span>
                </dt>
                <dd>
                    <input type="text" name="info_name_furigana" class="trimSpace"
                        value="{{ old('info_name_furigana', $params['info_name_furigana'] ?? '' ) }}" />
                    @error('info_name_furigana')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                </dd>

                <dt>{{ __('labels.update_profile.form.info_corporation_number') }}</dt>
                <dd>
                    <input type="text" name="info_corporation_number" class="trimSpace"
                        value="{{ old('info_corporation_number', $params['info_corporation_number'] ?? '' ) }}" />
                    @error('info_corporation_number')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                    <p><a href="https://www.houjin-bangou.nta.go.jp/" target="_blank">{{ __('messages.update_profile.text_houjin_bangou') }}</a>{{ __('messages.update_profile.note_houjin_bangou') }}</p>
                </dd>

                <dt>{{ __('labels.update_profile.form.info_nation_id') }} <span class="red">*</span></dt>
                <dd>
                    <select name="info_nation_id" id="info_nation_id">
                        <option value="">{{ __('labels.update_profile.select_default') }}</option>
                        @foreach ($nations as $item)
                            <option value="{{ $item->id }}"
                                {{ old('info_nation_id', $params['info_nation_id'] ?? '' ) == $item->id ? 'selected' : '' }}>
                                {{ $item->name }}</option>
                        @endforeach
                    </select>
                    @error('info_nation_id')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                </dd>
                <div id="a2" class="infoChildNation h-adr">
                    <input type="hidden" class="p-country-name" value="Japan">
                    <dt>{{ __('labels.update_profile.form.info_postal_code') }} <span class="red">*</span></dt>
                    <dd class="eInfoPostalCode">
                        <div class="fInfoPostalCode">
                            <input type="text" name="info_postal_code" id="info_postal_code"
                                class="p-postal-code trimSpace" value="{{ old('info_postal_code', $params['info_postal_code'] ?? '') }}" />
                            <input type="button" id="showInfoPostalCode"
                                value="{{ __('messages.update_profile.value_info_postal_code') }}" class="btn_a" />
                        </div>
                        @error('info_postal_code')
                            <div class="notice">{{ $message ?? '' }}</div>
                        @enderror
                    </dd>

                    <dt>{{ __('labels.update_profile.form.info_prefectures_id') }} <span class="red">*</span></dt>
                    <dd>
                        <input type="hidden" id="hiddenValuePrefectures" class="p-region" />
                        <select name="info_prefectures_id" id="info_prefectures_id">
                            <option value="">{{ __('labels.update_profile.select_default2') }}</option>
                            @foreach ($prefectures as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('info_prefectures_id', $params['info_prefectures_id'] ?? '' ) == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('info_prefectures_id')
                            <div class="notice">{{ $message ?? '' }}</div>
                        @enderror
                    </dd>
                    <dt>{{ __('labels.update_profile.form.info_address_second') }} <span class="red">*</span></dt>
                    <dd>
                        <input type="hidden" id="hiddenValueAddressSecond" class="p-locality p-street-address p-extended-address" />
                        <input type="text" class="em30 trimSpace" name="info_address_second"
                            value="{{ old('info_address_second', $params['info_address_second'] ?? '' ) }}" id="info_address_second" />
                        @error('info_address_second')
                            <div class="notice">{{ $message ?? '' }}</div>
                        @enderror
                        <p><span class="input_note">{{ __('messages.update_profile.note_info_address_second') }}</span></p>
                    </dd>
                </div>

                <dt id="label_info_address_three">{{ __('labels.update_profile.form.info_address_three') }}</dt>
                <dd>
                    <input type="text" class="em30 trimSpace" name="info_address_three"
                        value="{{ old('info_address_three', $params['info_address_three'] ?? '' ) }}" />
                    @error('info_address_three')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                    <p><span class="input_note">{{ __('messages.update_profile.note_info_address_three') }}</span></p>
                </dd>

                <dt>{{ __('labels.update_profile.form.info_phone') }}<span class="red">*</span></dt>
                <dd>
                    <input type="text" name="info_phone" class="trimSpace" value="{{ old('info_phone',  $params['info_phone'] ?? '' ) }}" />
                    @error('info_phone')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                </dd>

                <dt>
                    {{ __('labels.update_profile.form.email1') }}<br />
                    {{ __('labels.update_profile.form.email2') }}
                </dt>
                <dd>
                    {{ $email }} <br />
                </dd>
            </dl>

            <dl class="w18em eol clearfix">
                <dt>{{ __('labels.update_profile.form.info_member_id') }}<span class="red">*</span></dt>
                <dd class="eInfoMemberid">
                    <div class="fInfoMemberid">
                        <input type="text" name="info_member_id" class="trimSpace"
                            value="{{ old('info_member_id', $params['info_member_id'] ?? '' ) }}" />
                        <input type="button" value="{{ __('messages.update_profile.text_value_info_member_id') }}"
                            class="btn_a btn_a_change" id="info_member_id_button" />
                        <span id="check-icon"></span><br />
                        <span id="err_info_member_id" class="red"></span>
                    </div>
                    @error('info_member_id')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                    <span class="input_note">{{ __('messages.update_profile.note_info_member_id') }}</span>
                </dd>

                <dt>{{ __('labels.update_profile.form.password') }} <span class="red">*</span></dt>
                <dd>
                    <input type="password" name="password" id="password" />
                    <p><span class="input_note">{{ __('messages.update_profile.note_password') }}</span></p>
                    @error('password')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                </dd>

                <dt>{{ __('labels.update_profile.form.password_confirm') }} <span class="red">*</span></dt>
                <dd>
                    <input type="password" name="password_confirm" />
                    @error('password_confirm')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                </dd>
                <div id="changeGenderAndBirthday" style="display: block">
                    <dt>{{ __('labels.update_profile.form.info_gender') }} <span class="red">*</span></dt>
                    <dd class="eInfoGender">
                        <ul class="r_c fInfoGender">
                            <li>
                                <label><input type="radio" name="info_gender" value="1"
                                        {{ old('info_gender', $params['info_gender'] ?? '' ) == 1 ? 'checked' : '' }} />{{ __('labels.update_profile.form.info_gender_1') }}</label>
                            </li>
                            <li>
                                <label><input type="radio" name="info_gender" value="2"
                                        {{ old('info_gender', $params['info_gender'] ?? '' ) == 2 ? 'checked' : '' }} />{{ __('labels.update_profile.form.info_gender_2') }}</label>
                            </li>
                            <br />
                        </ul>
                        @error('info_gender')
                            <div class="notice">{{ $message ?? '' }}</div>
                        @enderror
                    </dd>

                    <dt>{{ __('labels.update_profile.form.info_birthday') }} <span class="red">*</span></dt>
                    <dd>
                        <select name="year" id="year" size="1"></select>

                        <select name="month" id="month" size="1"></select>

                        <select name="day" id="day" size="1">
                            <option value="" selected="selected">-- 日 --</option>
                        </select>
                        <input type="hidden" name="info_birthday" id="info_birthday" value="">
                        @error('info_birthday')
                            <div class="notice">{{ $message ?? '' }}</div>
                        @enderror
                        <br />
                        <span class="red">
                            {{ __('messages.update_profile.note_info_birthay_1') }}<br />
                            {{ __('messages.update_profile.note_info_birthay_2') }}
                        </span>
                    </dd>
                </div>

                <dt>{{ __('labels.update_profile.form.info_question') }} <span class="red">*</span></dt>
                <dd>
                    <input type="text" class="em30 trimSpace" name="info_question"
                        value="{{ old('info_question', $params['info_question'] ?? '' ) }}" />
                    @error('info_question')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                </dd>

                <dt>{{ __('labels.update_profile.form.info_answer') }} <span class="red">*</span></dt>
                <dd>
                    <input type="text" class="em30 trimSpace" name="info_answer" value="{{ old('info_answer', $params['info_answer'] ?? '' ) }}" />
                    @error('info_answer')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                    <p><span class="red">{{ __('messages.update_profile.note_info_answer') }}</span></p>
                </dd>
            </dl>

            <hr />

            <h3>{{ __('labels.update_profile.copy') }} <input type="button"
                    value="{{ __('labels.update_profile.btn_copy') }}" class="btn_a" id="clickCopy" /></h3>

            <dl class="w18em eol clearfix">
                <dt>{{ __('labels.update_profile.form.contact_type_acc') }} <span class="red">*</span></dt>
                <dd class="eContactTypeAcc">
                    <ul class="r_c fContactTypeAcc">
                        <li>
                            <label><input type="radio" name="contact_type_acc" value="1"
                                    {{ old('contact_type_acc', $params['contact_type_acc'] ?? '' ) == 1 ? 'checked' : '' }} />{{ __('labels.update_profile.form.contact_type_acc_1') }}</label>
                        </li>
                        <li>
                            <label><input type="radio" name="contact_type_acc" value="2"
                                    {{ old('contact_type_acc', $params['contact_type_acc'] ?? '' ) == 2 ? 'checked' : '' }} />{{ __('labels.update_profile.form.contact_type_acc_2') }}</label>
                        </li>
                        <br />
                    </ul>
                    @error('contact_type_acc')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                </dd>

                <dt><span id="changeNameContact">{{ __('labels.update_profile.form.contact_name') }}</span> <span
                        class="red">*</span></dt>
                <dd>
                    <input type="text" name="contact_name" class="trimSpace" value="{{ old('contact_name', $params['contact_name'] ?? '' ) }}" />
                    @error('contact_name')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                </dd>

                <dt><span
                        id="changeNameContactFurigana">{{ __('labels.update_profile.form.contact_name_furigana_1') }}</span>{{ __('labels.update_profile.form.contact_name_furigana_2') }}<span
                        class="red">*</span></dt>
                <dd>
                    <input type="text" name="contact_name_furigana" class="trimSpace"
                        value="{{ old('contact_name_furigana', $params['contact_name_furigana'] ?? '' ) }}" />
                    @error('contact_name_furigana')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                </dd>
                <div id="changEdepartmentAndManager" style="display: block">
                    <dt>{{ __('labels.update_profile.form.contact_name_department') }}</dt>
                    <dd>
                        <input type="text" name="contact_name_department"
                            value="{{ old('contact_name_department', $params['contact_name_department'] ?? '' ) }}" />
                        @error('contact_name_department')
                            <div class="notice">{{ $message ?? '' }}</div>
                        @enderror
                    </dd>
                    <dt>{{ __('labels.update_profile.form.contact_name_department_furigana') }}</dt>
                    <dd>
                        <input type="text" name="contact_name_department_furigana"
                            value="{{ old('contact_name_department_furigana', $params['contact_name_department_furigana'] ?? '' ) }}" />
                        @error('contact_name_department_furigana')
                            <div class="notice">{{ $message ?? '' }}</div>
                        @enderror
                    </dd>
                    <dt>{{ __('labels.update_profile.form.contact_name_manager') }} <span class="red">*</span></dt>
                    <dd>
                        <input type="text" name="contact_name_manager" value="{{ old('contact_name_manager', $params['contact_name_manager'] ?? '' ) }}" />
                        @error('contact_name_manager')
                            <div class="notice">{{ $message ?? '' }}</div>
                        @enderror
                    </dd>
                    <dt>{{ __('labels.update_profile.form.contact_name_manager_furigana') }} <span
                            class="red">*</span>
                    </dt>
                    <dd>
                        <input type="text" name="contact_name_manager_furigana"
                            value="{{ old('contact_name_manager_furigana', $params['contact_name_manager_furigana'] ?? '' ) }}" />
                        @error('contact_name_manager_furigana')
                            <div class="notice">{{ $message ?? '' }}</div>
                        @enderror
                    </dd>
                </div>
                <dt>{{ __('labels.update_profile.form.contact_nation_id') }} <span class="red">*</span></dt>
                <dd>
                    <select name="contact_nation_id" id="contact_nation_id">
                        <option value="">{{ __('labels.update_profile.select_default') }}</option>
                        @foreach ($nations as $item)
                            <option value="{{ $item->id }}"
                                {{ old('contact_nation_id', $params['contact_nation_id'] ?? '' ) == $item->id ? 'selected' : '' }}>{{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('contact_nation_id')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                </dd>
                <div id="a2" class="contactChildNation h-adr">
                    <input type="hidden" class="p-country-name" value="Japan">
                    <dt>{{ __('labels.update_profile.form.contact_postal_code') }} <span class="red">*</span></dt>
                    <dd class="eContactPostalCode">
                        <div class="fContactPostalCode">
                            <input type="text" name="contact_postal_code" id="contact_postal_code"
                                value="{{ old('contact_postal_code', $params['contact_postal_code'] ?? '' ) }}" class="p-postal-code trimSpace" />
                            <input type="button" id="showContactPostalCode"
                                value="{{ __('messages.update_profile.value_info_postal_code') }}" class="btn_a" />
                        </div>
                        @error('contact_postal_code')
                            <div class="notice">{{ $message ?? '' }}</div>
                        @enderror
                    </dd>

                    <dt>{{ __('labels.update_profile.form.contact_prefectures_id') }} <span class="red">*</span></dt>
                    <dd>
                        <input type="hidden" id="hiddenValueContactPrefectures" class="p-region" />
                        <select name="contact_prefectures_id" id="contact_prefectures_id">
                            <option value="">{{ __('labels.update_profile.select_default2') }}</option>
                            @foreach ($prefectures as $item)
                                <option value="{{ $item->id }}"
                                    {{ old('contact_prefectures_id', $params['contact_prefectures_id'] ?? '') == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('contact_prefectures_id')
                            <div class="notice">{{ $message ?? '' }}</div>
                        @enderror
                    </dd>

                    <dt>{{ __('labels.update_profile.form.contact_address_second') }} <span class="red">*</span></dt>
                    <dd>
                        <input type="hidden" id="hiddenValueContactAddressSecond" class="p-locality p-street-address p-extended-address" />
                        <input type="text" class="em30" name="contact_address_second"
                            value="{{ old('contact_address_second', $params['contact_address_second'] ?? '' ) }}" id="contact_address_second" />
                        @error('contact_address_second')
                            <div class="notice">{{ $message ?? '' }}</div>
                        @enderror
                        <br>
                        <span class="input_note">{{ __('messages.update_profile.note_info_address_second') }}</span>
                    </dd>
                </div>

                <dt id="label_contact_address_three">{{ __('labels.update_profile.form.contact_address_three') }}</dt>
                <dd>
                    <input type="text" class="em30" name="contact_address_three"
                        value="{{ old('contact_address_three', $params['contact_address_three'] ?? '' ) }}" />
                    @error('contact_address_three')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                    <p><span class="input_note">{{ __('messages.update_profile.note_info_address_three') }}</span></p>
                </dd>

                <dt>{{ __('labels.update_profile.form.contact_phone') }}<span class="red">*</span></dt>
                <dd>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $params['contact_phone'] ?? '') }}" />
                    @error('contact_phone')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                </dd>

                <dt>{{ __('labels.update_profile.form.contact_email') }}</dt>
                <dd>
                    {{ $email }}<br />
                    {{ __('messages.update_profile.note_email') }}
                </dd>

                <dt>{{ __('labels.update_profile.form.contact_email_second') }}</dt>
                <dd>
                    <input type="text" name="contact_email_second" id="contact_email_second"
                        value="{{ old('contact_email_second', $params['contact_email_second'] ?? '') }}" />
                    @error('contact_email_second')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                </dd>

                <dt>{{ __('labels.update_profile.form.contact_email_second_confirm') }}</dt>
                <dd>
                    <input type="text" name="contact_email_second_confirm"
                        value="{{ old('contact_email_second_confirm', $params['contact_email_second_confirm'] ?? '' ) }}" />
                    @error('contact_email_second_confirm')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                </dd>

                <dt>{{ __('labels.update_profile.form.contact_email_three') }}</dt>
                <dd>
                    <input type="text" name="contact_email_three" id="contact_email_three"
                        value="{{ old('contact_email_three', $params['contact_email_three'] ?? '' ) }}" />
                    @error('contact_email_three')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                </dd>

                <dt>{{ __('labels.update_profile.form.contact_email_three_confirm') }}</dt>
                <dd>
                    <input type="text" name="contact_email_three_confirm"
                        value="{{ old('contact_email_three_confirm', $params['contact_email_three_confirm'] ?? '' ) }}" />
                    @error('contact_email_three_confirm')
                        <div class="notice">{{ $message ?? '' }}</div>
                    @enderror
                </dd>
            </dl>
            <div class="privacy">
                <h3>{{ __('labels.update_profile.form.contact_privacy_title') }}</h3>
                <p class="eol">{{ __('labels.update_profile.form.contact_privacy_content') }}</p>
            </div>
            <p class="eol agree"><input type="checkbox" name="agree"> <label> {{ __('labels.update_profile.form.contact_checkbox_agree') }} </label></p>

            <ul class="footerBtn clearfix">
                <li><a href="{{ route('auth.signup') }}" class="btn_a" style="padding: 3px 2em; font-size: 1.3em;">{{ __('labels.back') }}</a></li>
                <li><input type="submit" id="submit" value="{{ __('messages.update_profile.btn_submit') }}" class="btn_b" /></li>
            </ul>

        </form>
    </div>
@endsection

@section('footerSection')
    <script src="{{ asset('common/js/yubinbango.js') }}" charset="UTF-8"></script>
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        const errorMessageUniqueEmailSecond = '{{ __('messages.error_unique_email_second') }}';
        const errorMessageRequired = '{{ __('messages.update_profile.form.Common_E001') }}';
        const errorMessageRadioRequired = '{{ __('messages.update_profile.form.Common_E025') }}';
        const errorMessageInfoNameRegex = '{{ __('messages.update_profile.form.Common_E016') }}';
        const errorMessageInfoCorporationNumberRegex = '{{ __('messages.update_profile.form.Register_U001_E004') }}';
        const errorMessageInfoNameFuriganaRegex = '{{ __('messages.general.common_A058') }}';
        const errorMessageInfoPostalCodeRegex = '{{ __('messages.update_profile.form.Common_E019') }}';
        const errorMessageInfoAddressRegex = '{{ __('messages.update_profile.form.Common_E020') }}';
        const errorMessageInfoPhoneRegex = '{{ __('messages.update_profile.form.message_phone') }}';
        const errorMessageInfoMemberIdRegex = '{{ __('messages.update_profile.form.Common_E006') }}';
        const errorMessageInfoPasswordIdRegex = '{{ __('messages.update_profile.form.Common_E005') }}';
        const errorMessageInfoPasswordConfirmIdRegex = '{{ __('messages.update_profile.form.Register_U001_E002') }}';
        const errorMessageInfoQuestionRegex = '{{ __('messages.update_profile.form.Register_U001_E008') }}';
        const errorMessageInfoAnswerRegex = '{{ __('messages.update_profile.form.Register_U001_E007') }}';
        const errorMessageContactNameDepartment = '{{ __('messages.update_profile.form.Common_E021') }}';
        const errorMessageContactNameDepartmentFurigana = '{{ __('messages.update_profile.form.Common_E022') }}';
        const errorMessageEmailFormat = '{{ __('messages.update_profile.form.Common_E002') }}';
        const errorMessageEmailDuplicate = '{{ __('messages.update_profile.form.Register_U001_E003') }}';
        const errorMessageUniqueMemberID = '{{ __('messages.update_profile.already_registered') }}';
        const errorMessageAgreeRequired = '{{ __('messages.common.errors.Common_E025') }}';
        let idOfJapan = $('#nation-japan-id').val();
        const _token = '{{ csrf_token() }}';
        const tokenAuthen = '{{ $token }}';
        const labelInfoAddressThree = '{{ __('labels.update_profile.form.info_address_three') }}';
        const labelLocationOrAddress = '{{ __('labels.update_profile.form.location_or_address') }}';
        const routeCheckId = @json(route('auth.check-member-id'));
        const dataBirthDayOld = @json($dataBirthDayOld);
    </script>
    <script src="{{ asset('end-user/profiles/store/validate.js') }}"></script>
    <script src="{{ asset('end-user/profiles/store/index.js') }}"></script>
@endsection
