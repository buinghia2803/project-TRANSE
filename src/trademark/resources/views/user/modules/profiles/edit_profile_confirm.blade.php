@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <h2>{{ __('labels.profile_edit.title_screen_confirm') }}</h2>
        <!-- contents inner -->
        <div class="normal">
            <form action="{{ route('user.profile.edit.confirm.post') }}" method="POST">
                @csrf
                @php
                    $typeAccGroup = \App\Models\User::INFO_TYPE_ACC_GROUP;
                    $s = $_GET['s'];
                @endphp
                <input type="hidden" value="{{ $s }}" name="s">
                <dl class="w18em clearfix">

                    <dt>{{ __('labels.update_profile.form.info_type_acc') }}<span class="red">*</span></dt>
                    <dd>{{ $listContactTypeAcc[$dataConfirm['info_type_acc']] ?? ''}}</dd>

                    @if ($dataConfirm['info_type_acc'] == CONTACT_TYPE_ACC_GROUP)
                        <dt>{{ __('labels.profile_edit.form.company_name_type_group') }} <span class="red">*</span></dt>
                    @else
                        <dt>{{ __('labels.profile_edit.form.company_name_type_individual') }} <span class="red">*</span></dt>
                    @endif
                    <dd>
                        {{ $dataConfirm['info_name'] }}
                        <input type="hidden" name="info_name" value="{{ $dataConfirm['info_name'] }}" />
                    </dd>

                    @if ($dataConfirm['info_type_acc'] == CONTACT_TYPE_ACC_GROUP)
                        <dt>{{ __('labels.profile_edit.form.company_name_furigana_type_group') }} <span class="red">*</span></dt>
                    @else
                        <dt>{{ __('labels.profile_edit.form.company_name_furigana_type_individual') }} <span class="red">*</span></dt>
                    @endif
                    <dd>
                        {{ $dataConfirm['info_name_furigana'] }}
                        <input type="hidden" name="info_name_furigana" value="{{ $dataConfirm['info_name_furigana'] }}" />
                    </dd>

                    <dt>{{ __('labels.update_profile.form.info_nation_id_confirm') }} <span class="red">*</span></dt>
                    <dd>
                        {{ $nationName }}
                        <input type="hidden" name="info_nation_id" value="{{ $dataConfirm['info_nation_id'] }}" />
                    </dd>
                    @if ($dataConfirm['info_nation_id'] == 1)
                        <dt>{{ __('labels.update_profile.form.info_postal_code_confirm') }}<span class="red">*</span></dt>
                        <dd>
                            {{ $dataConfirm['info_postal_code'] }}
                            <input type="hidden" name="info_postal_code" value="{{ $dataConfirm['info_postal_code'] }}" />
                        </dd>

                        <dt>{{ __('labels.profile_edit.form.address_1') }}<span class="red">*</span></dt>
                        <dd>
                            {{ $prefectureName }}
                            <input type="hidden" name="info_prefectures_id" value="{{ $dataConfirm['info_prefectures_id'] }}" />
                        </dd>

                        <dt>{{ __('labels.profile_edit.form.address_2') }}<span class="red">*</span></dt>
                        <dd>
                            {{ $dataConfirm['info_address_second'] }}
                            <input type="hidden" name="info_address_second" value="{{ $dataConfirm['info_address_second'] }}" />
                        </dd>
                    @endif
                    <dt>{{ __('labels.update_profile.form.info_address_three') }}</dt>
                    <dd>
                        {{ $dataConfirm['info_address_three'] }}
                        <input type="hidden" name="info_address_three" value="{{ $dataConfirm['info_address_three'] }}" />
                    </dd>

                    <dt>{{ __('labels.update_profile.form.info_phone') }}<span class="red">*</span></dt>
                    <dd>
                        {{ $dataConfirm['info_phone'] }}
                        <input type="hidden" name="info_phone" value="{{ $dataConfirm['info_phone'] }}" />
                    </dd>

                    <dt>{{ __('labels.update_profile.form.email_confirm') }}</dt>
                    <dd>
                        {{ $dataConfirm['email'] }}
                        <input type="hidden" name="email" value="{{ $dataConfirm['email'] }}" />
                    </dd>
                </dl>
                <dl class="w18em eol clearfix">
                    <dt>{{ __('labels.update_profile.form.ID') }} <span class="red">*</span></dt>
                    <dd>
                        {{ $dataConfirm['info_member_id'] }}
                        <input type="hidden" name="info_member_id" value="{{ $dataConfirm['info_member_id'] }}" />
                    </dd>

                    <dt>{{ __('labels.update_profile.form.password') }}<span class="red">*</span></dt>
                    <dd>
                        {{ __('messages.profile_edit.password_x') }}
                        <input type="hidden" name="password" value="{{ $dataConfirm['password'] }}" />
                        <input type="hidden" name="re_password" value="{{ $dataConfirm['re_password'] }}" />
                    </dd>

                    <dt>{{ __('labels.update_profile.form.info_gender_1') }}<span class="red">*</span></dt>
                    <dd>
                        @if (!empty($dataConfirm['info_gender']))
                            {{ $listGenderOptions[$dataConfirm['info_gender']] ?? '' }}
                        @endif
                    </dd>

                    <dt>{{ __('labels.update_profile.form.info_birthday') }} <span class="red">*</span></dt>
                    <dd>
                        @if (!empty($dataConfirm['info_birthday']))
                            @php $infoBirthday = \Carbon\Carbon::parse($dataConfirm['info_birthday']) @endphp
                            {{ $infoBirthday->year }}年{{ $infoBirthday->month }}月{{ $infoBirthday->day }}日
                            <input type="hidden" name="info_birthday" value="{{ $dataConfirm['info_birthday'] }}" />
                        @endif
                    </dd>

                    <dt>{{ __('labels.update_profile.form.info_question') }}<span class="red">*</span></dt>
                    <dd>
                        {{ $dataConfirm['info_question'] }}
                        <input type="hidden" name="info_question" value="{{ $dataConfirm['info_question'] }}" />
                    </dd>
                    <dt>{{ __('labels.profile_edit.form.asw_reset_pass') }}<span class="red">*</span></dt>
                    <dd>
                        {{ $dataConfirm['info_answer'] }}
                        <input type="hidden" name="info_answer" value="{{ $dataConfirm['info_answer'] }}" />
                    </dd>
                </dl>
                <hr />
                <h3>{{ __('labels.update_profile.form.info_type_acc') }}</h3>

                <dl class="w18em eol clearfix">

                    <dt>{{ __('labels.profile_edit.form.entity_or_individual') }}<span class="red">*</span></dt>
                    <dd>
                        {{ $listContactTypeAcc[$dataConfirm['contact_type_acc']] }}
                        <input type="hidden" name="contact_type_acc" value="{{ $dataConfirm['contact_type_acc'] }}" />
                    </dd>

                    @if ($dataConfirm['contact_type_acc'] == CONTACT_TYPE_ACC_GROUP)
                        <dt>{{ __('labels.profile_edit.form.company_name_type_group') }} <span class="red">*</span></dt>
                    @else
                        <dt>{{ __('labels.profile_edit.form.company_name_type_individual') }} <span class="red">*</span></dt>
                    @endif
                    <dd>
                        {{ $dataConfirm['contact_name'] }}
                        <input type="hidden" name="contact_name" value="{{ $dataConfirm['contact_name'] }}" />
                    </dd>

                    @if ($dataConfirm['contact_type_acc'] == CONTACT_TYPE_ACC_GROUP)
                        <dt>{{ __('labels.profile_edit.form.company_name_furigana_type_group') }} <span class="red">*</span></dt>
                    @else
                        <dt>{{ __('labels.profile_edit.form.company_name_furigana_type_individual') }} <span class="red">*</span></dt>
                    @endif
                    <dd>
                        {{ $dataConfirm['contact_name_furigana'] }}
                        <input type="hidden" name="contact_name_furigana" value="{{ $dataConfirm['contact_name_furigana'] }}" />
                    </dd>

                    @if ($dataConfirm['contact_type_acc'] == CONTACT_TYPE_ACC_GROUP)
                        <dt>{{ __('labels.update_profile.form.contact_name_department') }}</dt>
                        <dd>
                            {{ $dataConfirm['contact_name_department'] }}
                            <input type="hidden" name="contact_name_department" value="{{ $dataConfirm['contact_name_department'] }}" />
                        </dd>
                        <dt>{{ __('labels.update_profile.form.contact_name_department_furigana') }}</dt>
                        <dd>
                            {{ $dataConfirm['contact_name_department_furigana'] }}
                            <input type="hidden" name="contact_name_department_furigana" value="{{ $dataConfirm['contact_name_department_furigana'] }}" />
                        </dd>
                        <dt>{{ __('labels.update_profile.form.contact_name_manager') }}<span class="red">*</span></dt>
                        <dd>
                            {{ $dataConfirm['contact_name_manager'] }}
                            <input type="hidden" name="contact_name_manager" value="{{ $dataConfirm['contact_name_manager'] }}" />
                        </dd>
                        <dt>{{ __('labels.update_profile.form.contact_name_manager_furigana') }} <span class="red">*</span></dt>
                        <dd>
                            {{ $dataConfirm['contact_name_manager_furigana'] }}
                            <input type="hidden" name="contact_name_manager_furigana" value="{{ $dataConfirm['contact_name_manager_furigana'] }}" />
                        </dd>
                    @endif

                    <dt>{{ __('labels.update_profile.form.info_nation_id_confirm') }}<span class="red">*</span></dt>
                    <dd>
                        {{ $contactNationName }}
                        <input type="hidden" name="contact_nation_id" value="{{ $dataConfirm['contact_nation_id'] }}" />
                    </dd>

                    @if ($dataConfirm['contact_nation_id'] == NATION_JAPAN_ID)
                        <dt>{{ __('labels.update_profile.form.info_postal_code_confirm') }} <span class="red">*</span></dt>
                        <dd>
                            {{ $dataConfirm['contact_postal_code'] }}
                            <input type="hidden" name="contact_postal_code" value="{{ $dataConfirm['contact_postal_code'] }}" />
                        </dd>

                        <dt>{{ __('labels.update_profile.form.info_prefectures_id') }}<span class="red">*</span></dt>
                        <dd>
                            {{ $contactPrefectureName }}
                            <input type="hidden" name="contact_prefectures_id" value="{{ $dataConfirm['contact_prefectures_id'] }}" />
                        </dd>

                        <dt>{{ __('labels.update_profile.form.info_address_second') }} <span class="red">*</span></dt>
                        <dd>
                            {{ $dataConfirm['contact_address_second'] }}
                            <input type="hidden" name="contact_address_second" value="{{ $dataConfirm['contact_address_second'] }}" />
                        </dd>
                    @endif
                    @if ($dataConfirm['contact_nation_id'] == NATION_JAPAN_ID)
                        <dt>{{ __('labels.update_profile.form.info_address_three') }}</dt>
                    @else
                        <dt>{{ __('labels.update_profile.form.location_or_address') }}</dt>
                    @endif
                    <dd>
                        {{ $dataConfirm['contact_address_three'] }}
                        <input type="hidden" name="contact_address_three" value="{{ $dataConfirm['contact_address_three'] }}" />
                    </dd>

                    <dt>{{ __('labels.update_profile.form.contact_phone') }}<span class="red">*</span></dt>
                    <dd>
                        {{ $dataConfirm['contact_phone'] }}
                        <input type="hidden" name="contact_phone" value="{{ $dataConfirm['contact_phone'] }}" />
                    </dd>

                    <dt>{{ __('labels.update_profile.form.email2_contact') }}<br />
                        {{ __('labels.update_profile.form.email2_contact_note') }}</dt>
                    <dd>
                        {{ $dataConfirm['email'] }}
                    </dd>

                    <dt>{{ __('labels.update_profile.form.contact_email_second') }}</dt>
                    <dd>
                        {{ $dataConfirm['contact_email_second'] }}
                        <input type="hidden" name="contact_email_second" value="{{ $dataConfirm['contact_email_second'] }}" />
                        <input type="hidden" name="contact_email_second_confirm" value="{{ $dataConfirm['contact_email_second_confirm'] }}" />
                    </dd>

                    <dt>{{ __('labels.update_profile.form.contact_email_three') }}</dt>
                    <dd>
                        {{ $dataConfirm['contact_email_three'] }}
                        <input type="hidden" name="contact_email_three" value="{{ $dataConfirm['contact_email_three'] }}" />
                        <input type="hidden" name="contact_email_three_confirm" value="{{ $dataConfirm['contact_email_three_confirm'] }}" />
                    </dd>
                </dl>

                <ul class="footerBtn clearfix">
                    <li><input type="button" style="font-size: 1.3em;" onclick="location.href = '{{ route('user.profile.edit', ['s' => \Request::get('s') ]) }}'" value="{{ __('labels.back') }}" class="btn_a btn_a_custom" /></li>
                    <li><input type="submit" value="{{ __('labels.change') }}" class="btn_b" /></li>
                </ul>

            </form>

        </div><!-- /contents inner -->

    </div>
    <!-- /contents -->

@endsection
@section('css')
    <style>
        dd {
            min-height: 22px;
        }
        .btn_a_custom {
            display: inline-block;
            background: #cccccc;
            padding: 5px 2em;
            border: 1px solid #999999;
            border-radius: 5px;
            text-decoration: none;
            color: #000000;
            cursor: pointer;
        }
    </style>
@endsection

