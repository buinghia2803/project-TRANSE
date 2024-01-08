@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <h2> {{ __('labels.qa.kaiin.title') }}</h2>
        <p class="eol"><a class="btn_b"
                href="{{ route('admin.question.answers.from.ams', $userInfo->id) }}">{{ __('labels.qa.kaiin.text_1') }}</a>
        </p>
        <!-- contents inner -->
        <div class="normal">
            <form id="form">
                <dl class="w18em eol clearfix">
                    <dt>{{ __('labels.qa.kaiin.text_2') }} <span class="red">*</span></dt>
                    <dd>{{ $userInfo->info_type_acc == $infoTypeAcc[0] ? '法人' : '個人' }}</dd>

                    <dt>{{ __('labels.qa.kaiin.text_3') }} <span class="red">*</span> </dt>
                    <dd class="addTextFieldInfoName">
                        <div class="removeDisplayTextField">
                            {{ $userInfo->info_name }}
                            @if (\Auth::guard('admin')->user()->role != 2)
                                <button type="button"
                                    class="btn_a small btnDisplayTextField">{{ __('labels.qa.kaiin.text_4') }}</button>
                            @endif
                        </div>
                    </dd>

                    <dt>{{ __('labels.qa.kaiin.text_5') }} <span class="red">*</span></dt>
                    <dd class="addTextFieldInfoNameFurigana">
                        <div class="removeDisplayTextField">
                            {{ $userInfo->info_name_furigana }}
                        </div>
                    </dd>

                    <dt>{{ __('labels.qa.kaiin.text_6') }} <span class="red">*</span></dt>
                    <dd>{{ $userInfo->nation->name }}</dd>

                    @if ($userInfo->info_nation_id == 1)
                        <dt>{{ __('labels.qa.kaiin.text_7') }} <span class="red">*</span></dt>
                        <dd>{{ CommonHelper::formatPostalCode($userInfo->info_postal_code ?? 0) }}</dd>

                        <dt>{{ __('labels.qa.kaiin.text_8') }}<span class="red">*</span></dt>
                        <dd>{{ $userInfo->prefecture->name ?? '' }}</dd>

                        <dt>{{ __('labels.qa.kaiin.text_9') }}<span class="red">*</span></dt>
                        <dd>{{ $userInfo->info_address_second }}</dd>
                    @endif
                    <dt>{{ __('labels.qa.kaiin.text_10') }}</dt>
                    <dd>{{ $userInfo->info_address_three }}</dd>

                    <dt>{{ __('labels.qa.kaiin.text_11') }}<span class="red">*</span></dt>
                    <dd>{{ $userInfo->info_phone }}</dd>

                    <dt>{{ __('labels.qa.kaiin.text_12') }}</dt>
                    <dd>{{ $userInfo->email }}</dd>

                </dl>
                <dl class="w18em eol clearfix">

                    <dt>{{ __('labels.qa.kaiin.text_13') }} <span class="red">*</span></dt>
                    <dd>{{ $userInfo->info_member_id }}</dd>

                    <dt>{{ __('labels.qa.kaiin.text_14') }} <span class="red">*</span></dt>
                    <dd>{{ isset($userInfo->password) ? '*********' : '' }}</dd>

                    @if ($userInfo->info_type_acc != $infoTypeAcc[0])
                        <dt>{{ __('labels.qa.kaiin.text_15') }}<span class="red">*</span></dt>
                        <dd>
                            {{ $userInfo->getGender() }}
                        </dd>

                        <dt>{{ __('labels.qa.kaiin.text_19') }} <span class="red">*</span></dt>
                        <dd>
                            {{ $userInfo->info_birthday }}
                        </dd>
                    @endif

                    <dt>{{ __('labels.qa.kaiin.text_16') }} <span class="red">*</span></dt>
                    <dd>{{ isset($userInfo->info_question) ? '*********' : '' }}</dd>
                </dl>
                <hr />
                <h3>{{ __('labels.qa.kaiin.text_17') }}</h3>
                <dl class="w18em eol clearfix">

                    <dt>{{ __('labels.qa.kaiin.text_18') }} <span class="red">*</span></dt>
                    <dd>
                        {{ $userInfo->contact_type_acc == $contactTypeAcc[0] ? '法人' : '個人' }}
                    </dd>

                    <dt>{{ __('labels.qa.kaiin.text_3') }} <span class="red">*</span> </dt>
                    <dd class="addTextFieldContactName">
                        <div class="removeDisplayTextField2">
                            {{ $userInfo->contact_name }}
                            @if (\Auth::guard('admin')->user()->role != 2)
                                <button type="button" class="btn_a small btnDisplayTextField2">編集</button>
                            @endif
                        </div>
                    </dd>

                    <dt>{{ __('labels.qa.kaiin.text_5') }} <span class="red">*</span></dt>
                    <dd class="addTextFieldContactNameFurigana">
                        <div class="removeDisplayTextField2">
                            {{ $userInfo->contact_name_furigana }}
                        </div>
                    </dd>

                    <dt>{{ __('labels.qa.kaiin.text_20') }} </dt>
                    <dd>{{ $userInfo->contact_type_acc == $contactTypeAcc[1] ? '' : $userInfo->contact_name_department }}
                    </dd>
                    <dt>{{ __('labels.qa.kaiin.text_21') }}</dt>
                    <dd>{{ $userInfo->contact_type_acc == $contactTypeAcc[1] ? '' : $userInfo->contact_name_department_furigana }}
                    </dd>
                    <dt>{{ __('labels.qa.kaiin.text_22') }} <span class="red">*</span></dt>
                    <dd>{{ $userInfo->contact_type_acc == $contactTypeAcc[1] ? '' : $userInfo->contact_name_manager }}</dd>
                    <dt>{{ __('labels.qa.kaiin.text_23') }} <span class="red">*</span></dt>
                    <dd>{{ $userInfo->contact_type_acc == $contactTypeAcc[1] ? '' : $userInfo->contact_name_manager_furigana }}
                    </dd>
                    <dt>{{ __('labels.qa.kaiin.text_24') }} <span class="red">*</span></dt>
                    <dd>{{ $userInfo->contactNation->name ?? '' }}</dd>
                    @if ($userInfo->contactNation->id == 1)
                        <dt>{{ __('labels.qa.kaiin.text_25') }} <span class="red">*</span></dt>
                        <dd>{{ CommonHelper::formatPostalCode($userInfo->contact_postal_code ?? 0) }}</dd>

                        <dt>{{ __('labels.qa.kaiin.text_8') }}<span class="red">*</span></dt>
                        <dd>{{ $userInfo->contactPrefecture->name ?? '' }}</dd>
                        <dt>{{ __('labels.qa.kaiin.text_9') }}<span class="red">*</span></dt>
                        <dd>{{ $userInfo->contact_address_second }}</dd>
                    @endif
                    <dt>{{ __('labels.qa.kaiin.text_10') }}</dt>
                    <dd>{{ $userInfo->contact_address_three }}</dd>

                    <dt>{{ __('labels.qa.kaiin.text_26') }}<span class="red">*</span></dt>
                    <dd>{{ $userInfo->contact_phone }}</dd>

                    <dt>{{ __('labels.qa.kaiin.text_27') }}</dt>
                    <dd>{{ $userInfo->email }}</dd>

                    <dt>{{ __('labels.qa.kaiin.text_28') }}</dt>
                    <dd>{{ $userInfo->contact_email_second }}</dd>

                    <dt>{{ __('labels.qa.kaiin.text_29') }}</dt>
                    <dd>{{ $userInfo->contact_email_three }}</dd>

                </dl>

                <ul class="footerBtn clearfix">
                    <li><a class="btn_a" style="padding: 5px 2em !important;"
                            href="javascript:close_window();">{{ __('labels.qa.kaiin.text_30') }}</a></li>
                </ul>
            </form>
        </div>
        <!-- /contents inner -->
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        const userId = @JSON($userInfo->id) ?? '';
        const infoName = @JSON($userInfo->info_name) ?? '';
        const infoNameFurigana = @JSON($userInfo->info_name_furigana) ?? '';
        const contactName = @JSON($userInfo->contact_name) ?? '';
        const contactNameFurigana = @JSON($userInfo->contact_name_furigana) ?? '';
        const url = '{{ route('admin.ajax-edit-name-detail') }}'

        const errorMessageRequired = '{{ __('messages.general.Common_E001') }}';
        const errorMessageInfoNameRegex = '{{ __('messages.general.Common_E016') }}';
        const errorMessageInfoNameFuriganaRegex = '{{ __('messages.general.Common_E018') }}';
    </script>
    <script type="text/JavaScript" src="{{ asset('admin_assets/pages/qa/index.js') }}"></script>
@endsection
