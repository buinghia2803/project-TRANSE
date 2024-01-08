@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents" class="normal">

        @include('admin.components.includes.messages')

        <h2>{{ __('labels.profile_edit.title_edit_profle') }}</h2>
        <form id="form-edit-profile" action="{{ route('user.profile.edit') }}" method="POST">
            @csrf
            <p>{{ __('labels.profile_edit.des_edit_profle') }}</p>
            <h3>{{ __('labels.profile_edit.info_member') }}</h3>
            <dl class="w18em clearfix">
                <dt>{{ __('labels.profile_edit.form.company_type') }}</dt>
                <dd>
                    {{ $listContactTypeAcc[$user->info_type_acc] ?? '' }}
                    <input type="hidden" id="info_type_acc" name="info_type_acc" value="{{ $user->info_type_acc }}" />
                </dd>

                @if ($user->info_type_acc == CONTACT_TYPE_ACC_GROUP)
                    <dt>{{ __('labels.profile_edit.form.company_name_type_group') }}</dt>
                @else
                    <dt>{{ __('labels.profile_edit.form.company_name_type_individual') }}</dt>
                @endif
                <dd>
                    {{ $user->info_name }}<br>
                    <input type="hidden" name="info_name" id="info_name" value="{{ $user->info_name }}" />
                    @if ($user->info_type_acc == CONTACT_TYPE_ACC_GROUP)
                        <span class="red">{{ __('labels.profile_edit.form.company_name_note_1') }}</span>
                    @else
                        <span class="red">{{ __('labels.profile_edit.form.company_name_note_2') }}</span>
                    @endif
                </dd>

                @if ($user->info_type_acc == CONTACT_TYPE_ACC_GROUP)
                    <dt>{{ __('labels.profile_edit.form.company_name_furigana_type_group') }}</dt>
                @else
                    <dt>{{ __('labels.profile_edit.form.company_name_furigana_type_individual') }}</dt>
                @endif
                <dd>
                    {{ $user->info_name_furigana }}
                    <input type="hidden" name="info_name_furigana" id="info_name_furigana"
                        value="{{ $user->info_name_furigana }}" />
                </dd>

                @if ($user->info_type_acc == CONTACT_TYPE_ACC_GROUP)
                    <dt>{{ __('labels.profile_edit.form.company_number') }}</dt>
                    <dd>
                        <span>{{ $user->info_corporation_number }}</span><br />
                        <span class="red">{{ __('labels.profile_edit.form.company_number_note') }}</span>
                    </dd>
                @endif

                <dt>{{ __('labels.profile_edit.form.country') }} <span class="red">*</span></dt>
                <dd>
                    <input type="hidden" value="{{ NATION_JAPAN_ID }}" id="nation-japan-id" />
                    <select name="info_nation_id" id="info_nation_id">
                        <option value="">{{ __('labels.update_profile.select_default') }}</option>
                        @foreach ($nations as $k => $nation)
                            <option
                                value="{{ $k }}"
                                {{ old('info_nation_id', !empty($params['info_nation_id']) ? $params['info_nation_id'] : $user->info_nation_id) == $k ? 'selected' : '' }}
                            >
                                {{ $nation }}
                            </option>
                        @endforeach
                    </select>
                    @error('info_nation_id')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                </dd>
                <div id="a1" class="infoChildNation h-adr">
                    <dt>{{ __('labels.profile_edit.form.zip_code') }}<span class="red">*</span></dt>
                    <dd>
                        <div class="wp_info_postal_code">
                            <input
                                type="text"
                                class="p-postal-code remove_space_input"
                                name="info_postal_code"
                                id="info_postal_code"
                                value="{{ old('info_postal_code', !empty($params['info_postal_code']) ? $params['info_postal_code'] : $user->info_postal_code) }}"
                            />
                            <input type="button" id="showInfoPostalCode"
                                value="{{ __('labels.profile_edit.form.zip_code_submit') }}" class="btn_a" />
                            @error('info_postal_code')
                                <div class="notice">{{ $message }}</div>
                            @enderror
                        </div>
                    </dd>

                    <input type="hidden" class="p-country-name" value="Japan">
                    <dt>{{ __('labels.profile_edit.form.position_1') }}<span class="red">*</span></dt>
                    <dd>
                        <input type="hidden" id="hiddenValuePrefectures" class="p-region" />
                        <select name="info_prefectures_id" id="info_prefectures_id">
                            <option value="">{{ __('labels.profile_edit.please_select_prefectures') }}</option>
                            @foreach ($prefectures as $k => $item)
                                <option
                                    value="{{ $k }}"
                                    {{ old('info_prefectures_id', !empty($params['info_prefectures_id']) ? $params['info_prefectures_id'] : $user->info_prefectures_id) == $k ? 'selected' : '' }}
                                >
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('info_prefectures_id')
                            <div class="notice">{{ $message }}</div>
                        @enderror
                    </dd>
                    <dt>{{ __('labels.profile_edit.form.position_2') }}<span class="red">*</span></dt>
                    <dd>
                        <input type="hidden" id="hiddenValueAddressSecond" class="p-locality p-street-address p-extended-address" />
                        <input
                            type="text"
                            name="info_address_second"
                            id="info_address_second"
                            class="em30 remove_space_input"
                            value="{{ old('info_address_second', !empty($params['info_address_second']) ? $params['info_address_second'] : $user->info_address_second) }}"
                        />
                        <br />
                        <span class="input_note">{{ __('labels.profile_edit.form.position_2_note') }}</span>
                    </dd>
                </div>

                <dt id="label_info_address_three">{{ __('labels.profile_edit.form.position_3') }}</dt>
                <dd>
                    <input
                        type="text"
                        name="info_address_three"
                        id="info_address_three"
                        class="em30 remove_space_input"
                        value="{{ old('info_address_three', !empty($params) ? $params['info_address_three'] : $user->info_address_three) }}"
                    />
                    <br />
                    @error('info_address_three')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                    <span class="input_note">{{ __('labels.profile_edit.form.position_3_note') }}</span>
                </dd>

                <dt>{{ __('labels.profile_edit.form.phone') }}<span class="red">*</span></dt>
                <dd>
                    <input
                        type="text"
                        name="info_phone"
                        id="info_phone"
                        value="{{ old('info_phone', !empty($params['info_phone']) ? $params['info_phone'] : $user->info_phone) }}"
                    />
                    @error('info_phone')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                </dd>

                <dt>{{ __('labels.profile_edit.form.email') }}<br />{{ __('labels.profile_edit.form.email_note') }}</dt>
                <dd>
                    {{ $user->email }}
                    <input type="hidden" name="email" value="{{ $user->email }}" />
                    <a href="{{ route('user.profile.change-email.index') }}" class="btn_c">{{ __('labels.profile_edit.form.change_email_button') }}</a>
                </dd>

            </dl>
            <dl class="w18em eol clearfix">
                <dt>{{ __('labels.profile_edit.form.id_member') }}<span class="red">*</span></dt>
                <dd>
                    <div class="wp_info_member_id">
                        <input
                            type="text"
                            name="info_member_id"
                            id="info_member_id"
                            class="remove_space_input"
                            value="{{ old('info_member_id', !empty($params['info_member_id']) ? $params['info_member_id'] : $user->info_member_id) }}"
                            data-route="{{ route('user.profile.check-exists-member-id') }}"
                        />
                        <input type="button" value="{{ __('labels.profile_edit.form.member_id') }}" class="btn_a btn-check-member-id"
                            data-route="{{ route('user.profile.check-exists-member-id') }}" />
                        <input type="hidden" id="res-ajax-check-member-id" value="false" />
                        @if (!$errors->has('info_member_id'))
                            <span class="id-member-done">✔</span>
                        @endif
                    </div>
                    @error('info_member_id')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                    <span class="input_note"
                        style="display: block">{{ __('labels.profile_edit.form.id_member_note') }}</span>
                </dd>

                <dt>{{ __('labels.profile_edit.form.password') }}</dt>
                <dd>
                    <input type="password" name="password" id="password" value="" /><br />
                    @error('password')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                    <span class="input_note">{{ __('labels.profile_edit.form.password_note') }}</span>
                </dd>

                <dt>{{ __('labels.profile_edit.form.re_password') }}</dt>
                <dd>
                    <input type="password" name="re_password" id="re_password" value="" />
                    @error('re_password')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                </dd>
                @if ($user->info_type_acc == CONTACT_TYPE_ACC_INDIVIDUAL)
                    <dt>{{ __('labels.profile_edit.form.gender') }}</dt>
                    <dd>
                        @if ($user->info_gender)
                            {{ \App\Models\User::listGenderOptions()[$user->info_gender] }}
                        @endif
                    </dd>
                    <input type="hidden" name="info_gender" value="{{ $user->info_gender }}">
                    <dt>{{ __('labels.profile_edit.form.birthday') }}</dt>
                    <dd>
                        @if ($user->info_birthday)
                            <input type="hidden" name="info_birthday" id="info_birthday"
                                value="{{ $user->info_birthday }}" />
                            @php $infoBirthday = \Carbon\Carbon::parse($user->info_birthday) @endphp
                            {{ $infoBirthday->year }}{{ __('labels.profile_edit.form.year') }}{{ $infoBirthday->month }}{{ __('labels.profile_edit.form.month') }}{{ $infoBirthday->day }}{{ __('labels.profile_edit.form.day') }}
                        @endif
                        <br /><span class="red">{{ __('labels.profile_edit.form.birthday_note') }}</span>
                    </dd>
                @endif

                <dt>{{ __('labels.profile_edit.form.qa_reset_pass') }} <span class="red">*</span></dt>
                <dd>
                    <input
                        type="text"
                        name="info_question"
                        id="info_question"
                        value="{{ old('info_question', !empty($params['info_question']) ? $params['info_question'] : $user->info_question) }}"
                        class="em30 remove_space_input"
                    />
                    @error('info_question')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                </dd>

                <dt>{{ __('labels.profile_edit.form.asw_reset_pass') }} <span class="red">*</span></dt>
                <dd>
                    <input
                        type="text"
                        name="info_answer"
                        id="info_answer"
                        value="{{ old('info_answer', !empty($params['info_answer']) ? $params['info_answer'] : $user->info_answer) }}"
                        class="em30 remove_space_input"
                    />
                    <br />
                    @error('info_answer')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                    <span class="red">{{ __('labels.profile_edit.form.asw_reset_pass_note') }} </span>
                </dd>

            </dl>
            <hr />
            <!---INFO CONTACT-->
            <h3>
                {{ __('labels.profile_edit.form.contact') }}
                <input type="button" class="handleCopyInfoMember btn_a"
                    value="{{ __('labels.profile_edit.form.copy_info_member') }}" />
            </h3>

            <dl class="w18em eol clearfix">
                <dt>
                    {{ __('labels.profile_edit.form.entity_or_individual') }}<span class="red">*</span>
                </dt>
                <dd>
                    <input type="hidden" id="contact_type_acc_group" value="{{ CONTACT_TYPE_ACC_GROUP }}" />
                    <ul class="r_c clearfix ul_contact_type_acc">
                            <li>
                                <label>
                                    <input
                                        type="radio"
                                        name="contact_type_acc"
                                        value="{{ CONTACT_TYPE_ACC_GROUP }}"
                                        class="contact_type_acc contact_type_acc_1"
                                        {{ old('contact_type_acc', !empty($params['contact_type_acc']) ? $params['contact_type_acc'] : $user->contact_type_acc) == CONTACT_TYPE_ACC_GROUP ? 'checked' : '' }}
                                    />
                                    {{ __('labels.update_profile.form.contact_type_acc_1') }}
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input
                                        type="radio"
                                        name="contact_type_acc"
                                        value="{{ CONTACT_TYPE_ACC_INDIVIDUAL }}"
                                        class="contact_type_acc contact_type_acc_2"
                                        {{ old('contact_type_acc', !empty($params['contact_type_acc']) ? $params['contact_type_acc'] : $user->contact_type_acc) == CONTACT_TYPE_ACC_INDIVIDUAL ? 'checked' : '' }}
                                    />
                                    {{ __('labels.update_profile.form.contact_type_acc_2') }}
                                </label>
                            </li>
                        {{-- @endforeach --}}
                    </ul>
                    @error('contact_type_acc')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                </dd>

                @if ($user->contact_type_acc == CONTACT_TYPE_ACC_GROUP)
                    <dt>
                        <span
                            class="label_contact_name">{{ __('labels.profile_edit.form.company_name_type_group') }}</span>
                        <span class="red">*</span>
                    </dt>
                @else
                    <dt>
                        <span class="label_contact_name">
                            {{ __('labels.profile_edit.form.company_name_type_individual') }}
                        </span>
                        <span class="red">*</span>
                    </dt>
                @endif

                <dd>
                    <input
                        type="text"
                        name="contact_name"
                        id="contact_name"
                        class="remove_space_input"
                        value="{{ old('contact_name', !empty($params['contact_name']) ? $params['contact_name'] : $user->contact_name) }}"
                    />
                    @error('contact_name')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                </dd>

                @if ($user->contact_type_acc == CONTACT_TYPE_ACC_GROUP)
                    <dt>
                        <span
                            class="label_contact_name_furigana">{{ __('labels.profile_edit.form.company_name_furigana_type_group') }}
                        </span>
                        <span class="red">*</span>
                    </dt>
                @else
                    <dt>
                        <span
                            class="label_contact_name_furigana">{{ __('labels.profile_edit.form.company_name_furigana_type_individual') }}
                        </span>
                        <span class="red">*</span>
                    </dt>
                @endif
                <dd>
                    <input
                        type="text"
                        name="contact_name_furigana"
                        id="contact_name_furigana"
                        class="remove_space_input"
                        value="{{ old('contact_name_furigana', !empty($params['contact_name_furigana']) ? $params['contact_name_furigana'] : $user->contact_name_furigana) }}"
                    />
                    @error('contact_name_furigana')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                </dd>

                <div class="hideShowInfoDetailGroupContact">
                    <dt>{{ __('labels.profile_edit.form.department_name') }}</dt>
                    <dd>
                        <input
                            type="text"
                            name="contact_name_department"
                            id="contact_name_department"
                            value="{{ old('contact_name_department', !empty($params) ? $params['contact_name_department'] : $user->contact_name_department) }}"
                        />
                        @error('contact_name_department')
                            <div class="notice">{{ $message }}</div>
                        @enderror
                    </dd>
                    <dt>{{ __('labels.profile_edit.form.department_name_furigana') }}</dt>
                    <dd>
                        <input
                            type="text"
                            name="contact_name_department_furigana"
                            id="contact_name_department_furigana"
                            value="{{ old('contact_name_department_furigana', !empty($params) ? $params['contact_name_department_furigana'] : $user->contact_name_department_furigana) }}"
                        />
                        @error('contact_name_department_furigana')
                            <div class="notice">{{ $message }}</div>
                        @enderror
                    </dd>
                    <dt>
                        {{ __('labels.profile_edit.form.name_of_person_in_change') }} <span class="red">@if($user->contact_type_acc && $user->contact_type_acc == CONTACT_TYPE_ACC_GROUP) * @endif</span>
                    </dt>
                    <dd>
                        <input
                            type="text"
                            name="contact_name_manager"
                            id="contact_name_manager"
                            value="{{ old('contact_name_manager', !empty($params['contact_name_manager']) ? $params['contact_name_manager'] : $user->contact_name_manager) }}"
                        />
                        @error('contact_name_manager')
                            <div class="notice">{{ $message }}</div>
                        @enderror
                    </dd>
                    <dt>{{ __('labels.profile_edit.form.name_of_person_in_change_furigana') }}<span class="red">@if($user->contact_type_acc && $user->contact_type_acc == CONTACT_TYPE_ACC_GROUP) * @endif</span>
                    <dd>
                        <input
                            type="text"
                            name="contact_name_manager_furigana"
                            id="contact_name_manager_furigana"
                            value="{{ old('contact_name_manager_furigana', !empty($params['contact_name_manager_furigana']) ? $params['contact_name_manager_furigana'] : $user->contact_name_manager_furigana) }}"
                        />
                        @error('contact_name_manager_furigana')
                            <div class="notice">{{ $message }}</div>
                        @enderror
                    </dd>
                </div>

                <dt>{{ __('labels.profile_edit.form.country_or_region_name') }} <span class="red">*</span></dt>
                <dd>
                    <select name="contact_nation_id" id="contact_nation_id">
                        <option value="">{{ __('labels.update_profile.select_default') }}</option>
                        @foreach ($nations as $k => $nation)
                            <option
                                value="{{ $k }}"
                                {{ old('contact_nation_id', !empty($params['contact_nation_id']) ? $params['contact_nation_id'] : $user->contact_nation_id) == $k ? 'selected' : '' }}
                            >
                                {{ $nation }}
                            </option>
                        @endforeach
                    </select>
                    @error('contact_nation_id')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                </dd>
                <div id="a2" class="infoChildContactNation h-adr">
                    <input type="hidden" class="p-country-name" value="Japan">
                    <dt>{{ __('labels.profile_edit.form.post_code_half_width_no_hyphens') }}<span class="red">*</span>
                    </dt>
                    <dd>
                        <div class="wp_contact_postal_code">
                            <input
                                type="text"
                                id="contact_postal_code"
                                class="p-postal-code remove_space_input"
                                name="contact_postal_code"
                                value="{{ old('contact_postal_code', !empty($params['contact_postal_code']) ? $params['contact_postal_code'] : $user->contact_postal_code) }}"
                            />
                            <input type="button" id="handleButtonContactPostalCode" value="郵便番号から住所を入力" class="btn_a" />
                        </div>
                        @error('contact_postal_code')
                            <div class="notice">{{ $message }}</div>
                        @enderror
                    </dd>
                    <input type="hidden" class="p-country-name" value="Japan">

                    <dt>{{ __('labels.profile_edit.form.address_1') }}<span class="red">*</span></dt>
                    <dd>
                        <input type="hidden" id="hiddenValueContactPrefectures" class="p-region" />
                        <select name="contact_prefectures_id" id="contact_prefectures_id">
                            <option value="">{{ __('labels.profile_edit.please_select_prefectures') }}</option>
                            @foreach ($prefectures as $k => $item)
                                <option
                                    value="{{ $k }}"
                                    {{ old('contact_prefectures_id', !empty($params['contact_prefectures_id']) ? $params['contact_prefectures_id'] : $user->contact_prefectures_id) == $k ? 'selected' : '' }}
                                >
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('contact_prefectures_id')
                            <div class="notice">{{ $message }}</div>
                        @enderror
                    </dd>

                    <dt>{{ __('labels.profile_edit.form.address_2') }}<span class="red">*</span></dt>
                    <dd>
                        <input type="hidden" id="hiddenValueContactAddressSecond" class="p-locality p-street-address p-extended-address" />
                        <input
                            type="text"
                            name="contact_address_second"
                            id="contact_address_second"
                            value="{{ old('contact_address_second', !empty($params['contact_address_second']) ? $params['contact_address_second'] : $user->contact_address_second) }}"
                            class="em30 remove_space_input"
                        />
                        <br />
                        @error('contact_address_second')
                            <div class="notice">{{ $message }}</div>
                        @enderror
                        <span class="input_note">{{ __('labels.profile_edit.form.position_2_note') }}</span>
                    </dd>
                </div>
                <dt id="label_contact_address_three">{{ __('labels.update_profile.form.info_address_three') }}</dt>
                <dd>
                    <input
                        type="text"
                        name="contact_address_three"
                        id="contact_address_three"
                        value="{{ old('contact_address_three', !empty($params) ? $params['contact_address_three'] : $user->contact_address_three) }}"
                        class="em30 remove_space_input"
                    />
                    <br />
                    @error('contact_address_three')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                    <span class="input_note">{{ __('labels.profile_edit.form.position_3_note') }}</span>
                </dd>

                <dt>{{ __('labels.profile_edit.form.phone') }}<span class="red">*</span></dt>
                <dd>
                    <input
                        type="text"
                        name="contact_phone"
                        id="contact_phone"
                        class="remove_space_input"
                        value="{{ old('contact_phone', !empty($params['contact_phone']) ? $params['contact_phone'] : $user->contact_phone) }}"
                    />
                    @error('contact_phone')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                </dd>
                <dt>{{ __('labels.update_profile.form.email2_contact') }}<br />
                    {{ __('labels.update_profile.form.email2_contact_note') }}</dt>

                <dd> {{ $user->email }} <a href="{{ route('user.profile.change-email.index') }}" class="btn_c">{{ __('labels.profile_edit.form.change_email_button') }}</a><br />
                    {{ __('labels.profile_edit.form.change_email_des') }}
                </dd>

                <dt>{{ __('labels.update_profile.form.contact_email_second') }}</dt>
                <dd>
                    <input
                        type="text"
                        name="contact_email_second"
                        id="contact_email_second"
                        class="remove_space_input"
                        value="{{ old('contact_email_second', !empty($params) ? $params['contact_email_second'] : $user->contact_email_second) }}"
                    />
                    @error('contact_email_second')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                </dd>

                <dt>{{ __('labels.update_profile.form.contact_email_second_confirm') }}</dt>
                <dd>
                    <input
                        type="text"
                        name="contact_email_second_confirm"
                        id="contact_email_second_confirm"
                        class="remove_space_input"
                        value="{{ old('contact_email_second', !empty($params) ? $params['contact_email_second_confirm'] : $user->contact_email_second) }}"
                    />
                    @error('contact_email_second_confirm')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                </dd>

                <dt>{{ __('labels.update_profile.form.contact_email_three') }}</dt>
                <dd>
                    <input
                        type="text"
                        name="contact_email_three"
                        id="contact_email_three"
                        class="remove_space_input"
                        value="{{ old('contact_email_three', !empty($params) ? $params['contact_email_three'] : $user->contact_email_three) }}"
                    />
                    @error('contact_email_three')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                </dd>

                <dt>{{ __('labels.update_profile.form.contact_email_three_confirm') }}</dt>
                <dd>
                    <input
                        type="text"
                        name="contact_email_three_confirm"
                        id="contact_email_three_confirm"
                        class="remove_space_input"
                        value="{{ old('contact_email_three', !empty($params) ? $params['contact_email_three_confirm'] : $user->contact_email_three) }}"
                    />
                    @error('contact_email_three_confirm')
                        <div class="notice">{{ $message }}</div>
                    @enderror
                </dd>
            </dl>

            <ul class="footerBtn clearfix">
                <li><input type="submit" onclick="history.back()" value="{{ __('labels.back') }}" class="btn_a" /></li>
                <li><input type="submit" value="{{ __('labels.precheck.confirm') }}" class="btn_b" /></li>
            </ul>
        </form>

    </div><!-- /contents -->
@endsection
@section('css')
    <style>
        .wp_info_member_id {
            position: relative;
            display: inline-block;
        }

        .id-member-done {
            font-size: 24px;
            color: green;
            position: absolute;
            top: -5px;
            right: -30px;
        }

        .handleCopyInfoMember {
            cursor: pointer;
        }
    </style>
@endsection
@section('script')
    <script src="{{ asset('common/js/yubinbango.js') }}" charset="UTF-8"></script>
    <script src="{{ asset('common/js/validate.js') }}"></script>
    <script>
        const errorMessageUniqueEmailSecond = '{{ __('messages.error_unique_email_second') }}';
        const errorMessageNationRequired = '{{ __('messages.profile_edit.validate.Common_E001') }}';
        const errorMessageInfoPostalCodeRequired = '{{ __('messages.profile_edit.validate.Common_E001') }}';
        const errorMessageIsValidInfoPostalCode = '{{ __('messages.profile_edit.validate.Common_E019') }}';
        const errorMessageIsValidInfoMemberIdRequired = '{{ __('messages.profile_edit.validate.Common_E001') }}';
        const errorMessageIsValidInfoMemberIdFormat = '{{ __('messages.profile_edit.validate.Common_E006') }}';
        const errorMessageIsValidPasswordFormat = '{{ __('messages.general.Common_E005') }}';
        const errorMessageIsValidRequired = '{{ __('messages.profile_edit.validate.Common_E001') }}';
        const errorMessageIsValidPasswordEqualTo = '{{ __('messages.profile_edit.validate.Register_U001_E002') }}';
        const errorMessageIsValidInfoQuestionFormat = '{{ __('messages.profile_edit.validate.Register_U001_E008') }}';
        const errorMessageIsValidInfoAnswerFormat = '{{ __('messages.profile_edit.validate.Register_U001_E007') }}';
        const errorMessageIsValidInfoAddressFormat = '{{ __('messages.profile_edit.validate.Common_E020') }}';
        const errorMessageIsValidInfoPhoneFormat = '{{ __('messages.profile_edit.validate.Common_E014') }}';
        const errorMessageIsValidContactNameFormat = '{{ __('messages.profile_edit.validate.Common_E016') }}';
        const errorMessageIsValidContactNameFuriganaFormat = '{{ __('messages.profile_edit.validate.Common_E018') }}';
        const errorMessageIsValidMaxLength255 = '{{ __('messages.profile_edit.validate.Common_E022') }}';
        const errorMessageIsValidEmailMaxLength255 = '{{ __('messages.profile_edit.validate.Common_E021') }}';
        const errorMessageIsValidEmailFormat = '{{ __('messages.profile_edit.validate.Common_E002') }}';
        const errorMessageIsValidEmailConfirmEqualTo = '{{ __('messages.profile_edit.validate.Register_U001_E003') }}';
        const errorMessageIsValidInfoMemberIdExists =
            '{{ __('messages.profile_edit.validate.memberinfo_Edit_U001_E001') }}';
        const contactTypeAccGroup = '{{ CONTACT_TYPE_ACC_GROUP }}';
        const contactTypeAccIndividual = '{{ CONTACT_TYPE_ACC_INDIVIDUAL }}';
        const labelCompanyNameTypeGroup = '{{ __('labels.profile_edit.form.company_name_type_group') }}';
        const labelCompanyNameTypeIndividual = '{{ __('labels.profile_edit.form.company_name_type_individual') }}';
        const labelCompanyNameFuriganaTypeGroup = '{{ __('labels.profile_edit.form.company_name_furigana_type_group') }}';
        const labelCompanyNameFuriganaTypeIndividual =
            '{{ __('labels.profile_edit.form.company_name_furigana_type_individual') }}';
        const errorMessageRadioSelectCheckboxRequired = '{{ __('messages.profile_edit.validate.Common_E025') }}';
        const labelInfoAddressThree = '{{ __('labels.update_profile.form.info_address_three') }}';
        const labelLocationOrAddress = '{{ __('labels.update_profile.form.location_or_address') }}';
        const errorMessageInfoNameFuriganaRegex = '{{ __('messages.general.common_A058') }}';
    </script>
    <script src="{{ asset('end-user/profiles/js/validate.js') }}"></script>
    <script src="{{ asset('end-user/profiles/js/edit_profile.js') }}"></script>
@endsection
