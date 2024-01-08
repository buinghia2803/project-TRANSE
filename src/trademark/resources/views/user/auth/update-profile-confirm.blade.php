@extends('user.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <h2>{{ __('labels.update_profile.update_profile_confirm_title') }}</h2>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close-alert" data-dismiss="alert" aria-label="close">&times;</button>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <!-- contents inner -->
        <div class="normal">
            <form action="{{ route('auth.update-profile-confirm') }}" method="POST">
                @csrf
                <input type="hidden" value="{{ $params['token_authen'] ?? '' }}" name="token_authen">
                @php
                    $s = $_GET['s'];
                @endphp
                <input type="hidden" value="{{ $s }}" name="s">
                <dl class="w18em clearfix">
                    <dt>{{ __('labels.update_profile.form.info_type_acc') }} <span class="red">*</span></dt>
                    @if (!empty($params['info_type_acc']) && $params['info_type_acc'] == $typeAccGroup)
                        <dd>{{ __('labels.update_profile.form.info_type_acc_1') }}</dd>
                    @elseif (!empty($params['info_type_acc']) && $params['info_type_acc'] == $typeAccIndividual)
                        <dd>{{ __('labels.update_profile.form.info_type_acc_2') }}</dd>
                    @endif
                    <input type="hidden" name="info_type_acc" value="{{ $params['info_type_acc'] ?? '' }}">

                    @if (!empty($params['info_type_acc']) && $params['info_type_acc'] == $typeAccGroup)
                        <dt>{{ __('labels.profile_edit.form.company_name_type_group') }}<span class="red">*</span></dt>
                    @elseif (!empty($params['info_type_acc']) && $params['info_type_acc'] == $typeAccIndividual)
                        <dt>{{ __('labels.profile_edit.form.company_name_type_individual') }}<span class="red">*</span></dt>
                    @endif

                    <dd>{{ $params['info_name'] ?? '' }}</dd>
                    <input type="hidden" name="info_name" value="{{ $params['info_name'] ?? '' }}">

                    @if (!empty($params['info_type_acc']) && $params['info_type_acc'] == $typeAccGroup)
                        <dt>{{ __('labels.profile_edit.form.company_name_furigana') }}<span class="red">*</span></dt>
                    @elseif (!empty($params['info_type_acc']) && $params['info_type_acc'] == $typeAccIndividual)
                        <dt>{{ __('labels.profile_edit.form.company_name_furigana_type_individual') }}<span class="red">*</span></dt>
                    @endif
                    <dd>{{ $params['info_name_furigana'] ?? '' }}</dd>
                    <input type="hidden" name="info_name_furigana" value="{{ $params['info_name_furigana'] ?? '' }}">
                    <input type="hidden" name="info_corporation_number" value="{{ $params['info_corporation_number'] ?? '' }}">
                    <dt>{{ __('labels.update_profile.form.info_nation_id_confirm') }} <span class="red">*</span></dt>
                    @foreach ($nations as $item)
                        @if (!empty($params['info_nation_id']) && $item['id'] == $params['info_nation_id'])
                            <dd>{{ $item['name'] }}</dd>
                        @endif
                    @endforeach
                    <input type="hidden" name="info_nation_id" value="{{ $params['info_nation_id'] ?? '' }}">

                    @if($params['info_nation_id'] && $params['info_nation_id'] == NATION_JAPAN_ID)
                    <dt>{{ __('labels.update_profile.form.info_postal_code_confirm') }} <span class="red">*</span></dt>
                    <dd>{{ $params['info_postal_code'] ?? '' }}</dd>
                    @endif
                    <input type="hidden" name="info_postal_code" value="{{ $params['info_postal_code'] ?? '' }}">

                    @if($params['info_nation_id'] && $params['info_nation_id'] == NATION_JAPAN_ID)
                        <dt>{{ __('labels.update_profile.form.info_prefectures_id') }} <span class="red">*</span></dt>
                        @if ($params['info_prefectures_id'] != null)
                            @foreach ($prefectures as $item)
                                @if (!empty($params['info_prefectures_id']) && $item['id'] == $params['info_prefectures_id'])
                                    <dd>{{ $item['name'] }}</dd>
                                @endif
                            @endforeach
                        @else
                            <dd></dd>
                        @endif
                    @endif

                    <input type="hidden" name="info_prefectures_id" value="{{ $params['info_prefectures_id'] ?? '' }}">

                    @if($params['info_nation_id'] && $params['info_nation_id'] == NATION_JAPAN_ID)
                        <dt>{{ __('labels.update_profile.form.info_address_second') }} <span class="red">*</span></dt>
                        <dd>{{ $params['info_address_second'] ?? '' }}</dd>
                    @endif

                    <input type="hidden" name="info_address_second" value="{{ $params['info_address_second'] ?? '' }}">

                    @if($params['info_nation_id'] && $params['info_nation_id'] == NATION_JAPAN_ID)
                        <dt>{{ __('labels.update_profile.form.info_address_three') }}</dt>
                    @else
                        <dt> {{ __('labels.update_profile.form.location_or_address') }}</dt>
                    @endif

                    <dd>{{ $params['info_address_three'] ?? '' }}</dd>
                    <input type="hidden" name="info_address_three" value="{{ $params['info_address_three'] ?? '' }}">

                    <dt>{{ __('labels.update_profile.form.info_phone') }}<span class="red">*</span></dt>
                    <dd>{{ $params['info_phone'] ?? '' }}</dd>
                    <input type="hidden" name="info_phone" value="{{ $params['info_phone'] ?? '' }}">

                    <dt>{{ __('labels.update_profile.form.email_confirm2') }}</dt>
                    <dd>{{ $email ?? '' }}</dd>
                </dl>

                <dl class="w18em eol clearfix">
                    <dt>{{ __('labels.update_profile.form.info_member_id_confirm') }} <span class="red">*</span></dt>
                    <dd>{{ $params['info_member_id'] ?? '' }}</dd>
                    <input type="hidden" name="info_member_id" value="{{ $params['info_member_id'] ?? '' }}">

                    <dt>{{ __('labels.update_profile.form.password') }} <span class="red">*</span></dt>
                    <dd>{{ __('labels.update_profile.form.password_confirm_x') }}</dd>
                    <input type="hidden" name="password" value="{{ $params['password'] ?? '' }}">
                    <input type="hidden" name="password_confirm" value="{{ $params['password_confirm'] ?? '' }}">
                    @if (!empty($params['info_type_acc']) && $params['info_type_acc'] == $typeAccIndividual)
                        <dt>{{ __('labels.update_profile.form.info_gender') }}<span class="red">*</span></dt>
                        @if (!empty($params['info_gender']) && $params['info_gender'] == 1)
                            <dd>{{ __('labels.update_profile.form.info_gender_1') }}</dd>
                        @elseif (!empty($params['info_gender']) && $params['info_gender'] == 2)
                            <dd>{{ __('labels.update_profile.form.info_gender_2') }}</dd>
                        @endif
                        <input type="hidden" name="info_gender" value="{{ $params['info_gender'] ?? '' }}">
                        <dt>{{ __('labels.update_profile.form.info_birthday') }} <span class="red">*</span></dt>
                        <dd>{{ $params['year'] ?? '' }} / {{ $params['month'] ?? '' }} / {{ $params['day'] ?? '' }}</dd>
                        <input type="hidden" name="info_birthday" value="{{ $params['info_birthday'] ?? '' }}">
                    @endif

                    <dt>{{ __('labels.update_profile.form.info_question') }} <span class="red">*</span></dt>
                    <dd>{{ $params['info_question'] ?? '' }}</dd>
                    <input type="hidden" name="info_question" value="{{ $params['info_question'] ?? '' }}">
                    <dt>{{ __('labels.update_profile.form.info_answer') }} <span class="red">*</span></dt>
                    <dd>{{ $params['info_answer'] ?? '' }}</dd>
                    <input type="hidden" name="info_answer" value="{{ $params['info_answer'] ?? '' }}">
                </dl>

                <hr />

                <h3>{{ __('labels.update_profile.register_finish_title_contact') }}</h3>

                <dl class="w18em eol clearfix">
                    <dt>{{ __('labels.update_profile.form.contact_type_acc') }} <span class="red">*</span></dt>
                    @if (!empty($params['contact_type_acc']) && $params['contact_type_acc'] == $typeAccGroup)
                        <dd>{{ __('labels.update_profile.form.info_type_acc_1') }}</dd>
                    @elseif (!empty($params['contact_type_acc']) && $params['contact_type_acc'] == $typeAccIndividual)
                        <dd>{{ __('labels.update_profile.form.info_type_acc_2') }}</dd>
                    @endif
                    <input type="hidden" name="contact_type_acc" value="{{ $params['contact_type_acc'] ?? '' }}">

                    @if (!empty($params['contact_type_acc']) && $params['contact_type_acc'] == $typeAccGroup)
                        <dt>{{ __('labels.profile_edit.form.company_name_type_group') }}<span class="red">*</span></dt>
                    @elseif (!empty($params['contact_type_acc']) && $params['contact_type_acc'] == $typeAccIndividual)
                        <dt>{{ __('labels.profile_edit.form.company_name_type_individual') }}<span class="red">*</span></dt>
                    @endif

                    <dd>{{ $params['contact_name'] ?? '' }}</dd>
                    <input type="hidden" name="contact_name" value="{{ $params['contact_name'] ?? '' }}">

                    @if (!empty($params['contact_type_acc']) && $params['contact_type_acc'] == $typeAccGroup)
                        <dt>{{ __('labels.profile_edit.form.company_name_furigana') }}<span class="red">*</span></dt>
                    @elseif (!empty($params['contact_type_acc']) && $params['contact_type_acc'] == $typeAccIndividual)
                        <dt>{{ __('labels.profile_edit.form.company_name_furigana_type_individual') }}<span class="red">*</span></dt>
                    @endif

                    <dd>{{ $params['contact_name_furigana'] ?? '' }}</dd>
                    <input type="hidden" name="contact_name_furigana" value="{{ $params['contact_name_furigana'] ?? '' }}">

                    @if (!empty($params['contact_type_acc']))
                        <dt>{{ __('labels.update_profile.form.contact_name_department') }}</dt>
                        <dd>{{ $params['contact_name_department'] ?? '' }}</dd>
                        <input type="hidden" name="contact_name_department" value="{{ $params['contact_name_department'] ?? '' }}">

                        <dt>{{ __('labels.update_profile.form.contact_name_department_furigana') }}</dt>
                        <dd>{{ $params['contact_name_department_furigana'] ?? '' }}</dd>
                        <input type="hidden" name="contact_name_department_furigana" value="{{ $params['contact_name_department_furigana'] ?? '' }}">

                        <dt>{{ __('labels.update_profile.form.contact_name_manager') }} @if($params['contact_type_acc'] == $typeAccGroup) <span class="red">*</span> @endif</dt>
                        <dd>{{ $params['contact_name_manager'] ?? '' }}</dd>
                        <input type="hidden" name="contact_name_manager" value="{{ $params['contact_name_manager'] ?? '' }}">

                        <dt>{{ __('labels.update_profile.form.contact_name_manager_furigana') }} @if($params['contact_type_acc'] == $typeAccGroup) <span class="red">*</span> @endif</dt>
                        <dd>{{ $params['contact_name_manager_furigana'] ?? '' }}</dd>
                        <input type="hidden" name="contact_name_manager_furigana" value="{{ $params['contact_name_manager_furigana'] ?? ''}}">
                    @endif

                    <dt>{{ __('labels.update_profile.form.info_nation_id_confirm') }} <span class="red">*</span></dt>
                    @foreach ($nations as $item)
                        @if (!empty($params['contact_nation_id']) && $item['id'] == $params['contact_nation_id'])
                            <dd>{{ $item['name'] }}</dd>
                        @endif
                    @endforeach
                    <input type="hidden" name="contact_nation_id" value="{{ $params['contact_nation_id'] ?? '' }}">

                    @if($params['contact_nation_id'] && $params['contact_nation_id'] == NATION_JAPAN_ID)
                        <dt>{{ __('labels.update_profile.form.info_postal_code_confirm') }} <span class="red">*</span></dt>
                        <dd>{{ $params['contact_postal_code'] ?? '' }}</dd>
                    @endif
                    <input type="hidden" name="contact_postal_code" value="{{ $params['contact_postal_code'] ?? '' }}">

                    @if($params['contact_nation_id'] && $params['contact_nation_id'] == NATION_JAPAN_ID)
                        <dt>{{ __('labels.update_profile.form.info_prefectures_id') }} <span class="red">*</span></dt>
                        @if ($params['contact_prefectures_id'] != null)
                            @foreach ($prefectures as $item)
                                @if (!empty($params['contact_prefectures_id']) && $item['id'] == $params['contact_prefectures_id'])
                                    <dd>{{ $item['name'] }}</dd>
                                @endif
                            @endforeach
                        @else
                            <dd></dd>
                        @endif
                    @endif

                    <input type="hidden" name="contact_prefectures_id" value="{{ $params['contact_prefectures_id'] ?? '' }}">

                    @if($params['contact_nation_id'] && $params['contact_nation_id'] == NATION_JAPAN_ID)
                        <dt>{{ __('labels.update_profile.form.info_address_second') }} <span class="red">*</span></dt>
                        <dd>{{ $params['contact_address_second'] ?? '' }}</dd>
                    @endif
                    <input type="hidden" name="contact_address_second" value="{{ $params['contact_address_second'] ?? '' }}">

                    <input type="hidden" name="contact_phone" value="{{ $params['contact_phone'] ?? '' }}">
                    @if($params['contact_nation_id'] && $params['contact_nation_id'] == NATION_JAPAN_ID)
                        <dt>{{ __('labels.update_profile.form.info_address_three') }}</dt>
                    @else
                        <dt> {{ __('labels.update_profile.form.location_or_address') }}</dt>
                    @endif
                    <dd>{{ $params['contact_address_three'] ?? '' }}</dd>
                    <input type="hidden" name="contact_address_three" value="{{ $params['contact_address_three'] ?? '' }}">

                    <dt>{{ __('labels.update_profile.form.info_phone') }}<span class="red">*</span></dt>
                    <dd>{{ $params['contact_phone'] ?? '' }}</dd>
                    <input type="hidden" name="contact_phone" value="{{ $params['contact_phone'] ?? '' }}">

                    <dt>{{ __('labels.update_profile.form.contact_email') }}</dt>
                    <dd>{{ $email ?? '' }}</dd>

                    <dt>{{ __('labels.update_profile.form.contact_email_second') }}</dt>
                    <dd>{{ $params['contact_email_second'] ?? '' }}</dd>
                    <input type="hidden" name="contact_email_second" value="{{ $params['contact_email_second'] ?? '' }}">
                    <input type="hidden" name="contact_email_second_confirm" value="{{ $params['contact_email_second_confirm'] ?? '' }}">

                    <dt>{{ __('labels.update_profile.form.contact_email_three') }}</dt>
                    <dd>{{ $params['contact_email_three'] ?? '' }}</dd>
                    <input type="hidden" name="contact_email_three" value="{{ $params['contact_email_three'] ?? '' }}">
                    <input type="hidden" name="contact_email_three_confirm" value="{{ $params['contact_email_three_confirm'] ?? '' }}">
                </dl>
                <div class="privacy">
                    <h3>{{ __('labels.update_profile.form.contact_privacy_title') }}</h3>
                    <p class="eol">{{ __('labels.update_profile.form.contact_privacy_content') }}</p>
                </div>
    
                <p class="eol"><label><input type="checkbox" name="agree" checked="{{ $params['agree'] }} ? 'checked' ? ''" onclick="return false;"> {{ __('labels.update_profile.form.contact_checkbox_agree') }} </label></p>
                <ul class="footerBtn clearfix">
                    <a href="{{ route('auth.form-update-profile', ['token' => $params['token_authen'], 's' => $s]) }}" style="font-size: 1.3em; padding: 3.2px 2em;" class="btn_a" >{{ __('labels.back') }}</a>
                    <li><input type="submit" value="{{ __('labels.update_profile.update_profile_confirm_btn') }}" class="btn_b" /></li>
                </ul>
            </form>
        </div>
        <!-- /contents inner -->
    </div>
    <!-- /contents -->
@endsection
