@php
    $currentUser = \Auth::guard('web')->user();
    
    if (empty($information)) {
        $tradeMarkService = app(\App\Services\TrademarkService::class);
        $information = $tradeMarkService->getTradeMarkRegister();
    }
@endphp
<div class="trademark-info">
    <h3>{{ __('labels.registrant_information.title') }}</h3>
    <p class="eol">{{ __('labels.registrant_information.desc') }}</p>
    <dl class="w16em eol clearfix">
        <dt>{{ __('labels.registrant_information.action') }}</dt>
        <dd>
            <ul>
                <li>
                    <input type="button" id="btn-click-copy" class="btn_b"
                       value="{{ __('labels.registrant_information.button_1') }}"
                        data-copy_info="{{ json_encode([
                            'type_acc' => $currentUser['info_type_acc'] ?? '',
                            'name' => $currentUser->info_name,
                            'nation_id' => $currentUser->info_nation_id,
                            'prefectures_id' => $currentUser->info_prefectures_id,
                            'address_second' => $currentUser->info_address_second,
                            'address_three' => $currentUser->info_address_three,
                        ]) }}"
                    />
                </li>
                <li>{{ __('labels.registrant_information.text_b') }}</li>
                <li>
                    <a class="btn_b dialog-btn" href="javascript:;" onclick="openModal('#information')">{{ __('labels.registrant_information.button_2') }}</a>
                </li>
            </ul>
        </dd>
    </dl>

    <div id="information" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-body">
                    <button type="button" class="close-alert" data-dismiss="modal">&times;</button>
                    <div class="content">
                        <p>
                            <button type="button" class="btn_a copy-trademark-info">{{ __('labels.registrant_information.button_3') }}</button>
                        </p>

                        <table class="normal_b mb10 w-100">
                            <tr>
                                <th>{{ __('labels.registrant_information.modal_1') }}</th>
                                <th>{{ __('labels.registrant_information.modal_2') }}</th>
                                <th class="em04">{{ __('labels.registrant_information.modal_3') }}</th>
                            </tr>
                            @if(isset($information->appTrademark))
                                @foreach ($information->appTrademark->trademarkInfo as $item)
                                    @if(count($information->appTrademark))
                                    <tr>
                                        <td>{{ $item->name }}</td>
                                        <td>{{ $item->nations_name }}-{{ $item->prefectures_name }}-{{ $item->address_second }}-{{ $item->address_three }}
                                        </td>
                                        <td class="center">
                                            <input type="radio" name="check"
                                                data-copy_info="{{ json_encode([
                                                    'type_acc' => $item->type_acc ??null,
                                                    'name' => $item->name ??null,
                                                    'nation_id' => $item->m_nation_id ??null,
                                                    'prefectures_id' => $item->m_prefecture_id ??null,
                                                    'address_second' => $item->address_second ??null,
                                                    'address_three' => $item->address_three ??null,
                                                ]) }}"
                                            />
                                        </td>
                                    </tr>
                                    @else
                                        <tr>
                                            <td colspan="100%" class="center">{{ __('labels.no_data') }}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @else
                                @if (isset($information) && count($information))
                                    @foreach ($information as $item)
                                        <tr>
                                            <td>{{ $item['name'] ?? null }} </td>
                                            <td>{{ $item['full_address'] ?? null }}
                                            </td>
                                            <td class="center">
                                                <input type="radio" name="check"
                                                    data-copy_info="{{ json_encode([
                                                        'type_acc' => $item['type_acc'] ?? null,
                                                        'name' => $item['name'] ?? null,
                                                        'nation_id' => $item['m_nation_id'] ?? null,
                                                        'prefectures_id' => $item['m_prefecture_id'] ?? null,
                                                        'address_second' => $item['address_second'] ?? null,
                                                        'address_three' => $item['address_three'] ?? null,
                                                    ]) }}"
                                                />
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="100%" class="center">{{ __('labels.no_data') }}</td>
                                    </tr>
                                @endif
                            @endif
                        </table>

                        <p class="eol">
                            <button type="button" class="btn_a copy-trademark-info">{{ __('labels.registrant_information.button_3') }}</button>
                        </p>

                        <p class="center fs12">
                            <a href="#" data-dismiss="modal" class="btn_b">{{ __('labels.registrant_information.button_close_modal') }}</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="append_html">
        @if (!empty($trademarkInfos) && count($trademarkInfos))
            @foreach ($trademarkInfos as $key => $trademarkInfo)
                <dl class="w16em clearfix registrant_information" data-delete_box>
                    @if ($key)
                        <hr>
                    @endif
                    <dt>{{ __('labels.registrant_information.form.type_acc') }} <span class="red">*</span></dt>
                    <dd class="eTypeAcc">
                        <ul class="r_c fTypeAcc">
                            <li>
                                <label>
                                    <input type="radio" class="data-type_acc" {{ $trademarkInfo && isset($trademarkInfo->type_acc) && $trademarkInfo->type_acc == App\Models\TrademarkInfo::TYPE_ACC_COMPANY ? 'checked' : '' }} name="data[{{$key}}][type_acc]"  value="1" />
                                    {{ __('labels.registrant_information.form.type_acc_1') }}
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" {{ $trademarkInfo && isset($trademarkInfo->type_acc) && $trademarkInfo->type_acc == App\Models\TrademarkInfo::TYPE_ACC_SINGLE ? 'checked' : '' }} class="data-type_acc" name="data[{{$key}}][type_acc]" value="2" />
                                    {{ __('labels.registrant_information.form.type_acc_2') }}
                                </label>
                            </li>
                            <br />
                        </ul>
                    </dd>

                    <dt>{{ __('labels.registrant_information.form.name') }} <span class="red">*</span></dt>
                    <dd><input type="text" class="data-name" name="data[{{$key}}][name]" value="{{ $trademarkInfo && isset($trademarkInfo->name) ? $trademarkInfo->name : '' }}"/></dd>

                    <dt>{{ __('labels.registrant_information.form.m_nation_id') }}<span class="red">*</span></dt>
                    <dd class="eNation">
                        <select name="data[{{$key}}][m_nation_id]" class="data-m_nation_id">
                            <option value="">{{ __('labels.registrant_information.select_default') }}</option>
                            @if (isset($nations) && count($nations))
                                @if (isset($trademarkInfo) && isset($trademarkInfo->m_nation_id))
                                    @foreach ($nations as $k => $nation)
                                    <option value="{{ $k }}" {{ $trademarkInfo->m_nation_id == $k ? 'selected' : '' }}>
                                        {{ $nation ?? '' }}</option>
                                    @endforeach
                                @else
                                    @foreach ($nations as $k => $nation)
                                        <option value="{{ $k }}" {{ old('m_nation_id') == $k ? 'selected' : '' }}>
                                            {{ $nation ?? '' }}</option>
                                    @endforeach
                                @endif
                            @endif
                        </select>
                    </dd>

                    <div class="showIfJapan hidden">
                        <dt>{{ __('labels.registrant_information.form.m_prefecture_id') }}<span class="red">*</span></dt>
                        <input type="hidden" name="data[{{$key}}][id]" value="{{ $trademarkInfo->id ?? ''}}">
                        <dd class="ePerfecture">
                            <select name="data[{{$key}}][m_prefecture_id]" class="data-m_prefecture_id">
                                <option value="">{{ __('labels.registrant_information.select_default2') }}</option>
                                @if (isset($prefectures))
                                    @foreach ($prefectures as $k => $item)
                                        <option value="{{ $k }}" {{ old('m_prefecture_id', $trademarkInfo->m_prefecture_id ?? '') == $k ? 'selected' : '' }}>
                                            {{ $item }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </dd>

                        <dt>{{ __('labels.registrant_information.form.address_second') }}<span class="red">*</span></dt>
                        <dd>
                            <input type="text" class="data-address_second em30" name="data[{{$key}}][address_second]" value="{{ $trademarkInfo && isset($trademarkInfo->address_second) ? $trademarkInfo->address_second : '' }}" /><br />
                            <span class="input_note">{{ __('labels.registrant_information.form.note_address_second') }}</span>
                        </dd>
                    </div>

                    <dt>
                        {{ __('labels.registrant_information.form.address_three_1') }}<br />
                        {{ __('labels.registrant_information.form.address_three_2') }}
                    </dt>
                    <dd>
                        <input type="text" class="data-address_three em30" name="data[{{$key}}][address_three]"  value="{{ $trademarkInfo && isset($trademarkInfo->address_three) ? $trademarkInfo->address_three : '' }}" /><br />
                        <span class="input_note">{{ __('labels.registrant_information.form.note_address_three') }}</span>
                    </dd>
                    @if ($key)
                        <input type="button" value="{{ __('labels.delete') }}" class="small btn_d eol" data-delete_btn>
                    @endif
                </dl>
            @endforeach
        @else
            <dl class="w16em clearfix registrant_information">
                <dt>{{ __('labels.registrant_information.form.type_acc') }} <span class="red">*</span></dt>
                <dd class="eTypeAcc">
                    <ul class="r_c fTypeAcc">
                        <li>
                            <label>
                                <input type="radio" class="data-type_acc"  name="data[0][type_acc]"  value="1" />
                                {{ __('labels.registrant_information.form.type_acc_1') }}
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="radio" class="data-type_acc" name="data[0][type_acc]" value="2" />
                                {{ __('labels.registrant_information.form.type_acc_2') }}
                            </label>
                        </li>
                        <br />
                    </ul>
                </dd>

                <dt>{{ __('labels.registrant_information.form.name') }} <span class="red">*</span></dt>
                <dd><input type="text" class="data-name" name="data[0][name]" value=""/></dd>

                <dt>{{ __('labels.registrant_information.form.m_nation_id') }}<span class="red">*</span></dt>
                <dd class="eNation">
                    <select name="data[0][m_nation_id]" class="data-m_nation_id">
                        <option value="">{{ __('labels.registrant_information.select_default') }}</option>
                        @if (isset($nations) && count($nations))
                            @foreach ($nations as $k => $nation)
                                <option value="{{ $k }}" {{ old('m_nation_id') == $k ? 'selected' : '' }}>
                                    {{ $nation ?? '' }}</option>
                            @endforeach
                        @endif
                    </select>
                </dd>

                <div class="showIfJapan hidden">
                    <dt>{{ __('labels.registrant_information.form.m_prefecture_id') }}<span class="red">*</span></dt>
                    <input type="hidden" name="data[0][id]" value="">
                    <dd class="ePerfecture">
                        <select name="data[0][m_prefecture_id]" class="data-m_prefecture_id">
                            <option value="">{{ __('labels.registrant_information.select_default2') }}</option>
                            @if (isset($prefectures))
                                @foreach ($prefectures as $k => $item)
                                    <option value="{{ $k }}" {{ old('m_prefecture_id') == $k ? 'selected' : '' }}>
                                        {{ $item }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </dd>

                    <dt>{{ __('labels.registrant_information.form.address_second') }}<span class="red">*</span></dt>
                    <dd>
                        <input type="text" class="data-address_second em30" name="data[0][address_second]" value="" /><br />
                        <span class="input_note">{{ __('labels.registrant_information.form.note_address_second') }}</span>
                    </dd>
                </div>

                <dt>
                    {{ __('labels.registrant_information.form.address_three_1') }}<br />
                    {{ __('labels.registrant_information.form.address_three_2') }}
                </dt>
                <dd>
                    <input type="text" class="data-address_three em30" name="data[0][address_three]"  value="" /><br />
                    <span class="input_note">{{ __('labels.registrant_information.form.note_address_three') }}</span>
                </dd>
            </dl>
        @endif
    </div>
    <p class="eol">
        <a href="" class="click_append">{{ __('labels.registrant_information.form.submit') }}</a>
    </p>
</div>
<style>
    @media only screen and (max-width: 640px)  {
        .data-m_nation_id {
            width: 100%;
        }
    }
</style>
<script type="text/javascript">
    const NATIONS = @json($nations);
    const PREFECTURES = @json($prefectures);
    const TrademarkInfoJapanID = '{{ NATION_JAPAN_ID }}';

    const errorMessageTrademarkRequired = '{{ __('messages.general.Common_E001') }}';
    const errorMessageTrademarkNameRegex = '{{ __('messages.general.Common_E016') }}';
    const errorMessageTrademarkNameMaxLengthText = '{{ __('messages.general.Common_E016') }}';
    const errorMessageTrademarkAddressRegex = '{{ __('messages.general.Common_E020') }}';

    const label_type_acc = '{{ __('labels.registrant_information.form.type_acc') }}';
    const label_type_acc_1 = '{{ __('labels.registrant_information.form.type_acc_1') }}';
    const label_type_acc_2 = '{{ __('labels.registrant_information.form.type_acc_2') }}';
    const label_name = '{{ __('labels.registrant_information.form.name') }}';
    const label_m_nation_id = '{{ __('labels.registrant_information.form.m_nation_id') }}';
    const label_select_default = '{{ __('labels.registrant_information.select_default') }}';
    const label_m_prefecture_id = '{{ __('labels.registrant_information.form.m_prefecture_id') }}';
    const label_select_default2 = '{{ __('labels.registrant_information.select_default2') }}';
    const label_address_second = '{{ __('labels.registrant_information.form.address_second') }}';
    const label_note_address_second = '{{ __('labels.registrant_information.form.note_address_second') }}';
    const label_address_three_1 = '{{ __('labels.registrant_information.form.address_three_1') }}';
    const label_address_three_2 = '{{ __('labels.registrant_information.form.address_three_2') }}';
    const label_note_address_three = '{{ __('labels.registrant_information.form.note_address_three') }}';
    const label_delete = '{{ __('labels.delete') }}';
</script>
<script src="{{ asset('end-user/common/js/trademark-info.js') }}"></script>
