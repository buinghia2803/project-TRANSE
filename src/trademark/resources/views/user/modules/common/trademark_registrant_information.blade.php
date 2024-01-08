
@section('css')
    <style>
        .custom-input {
            border: none;
            background-color: white;
            text-decoration-line: underline;
        }

        .custom-input:hover {
            color: #359ce0;
            text-decoration: none;
            cursor: pointer;
        }

        .btn_b {
            display: inline-block;
            background: #359ce0;
            padding: 5px 2em;
            border: 1px solid #999999;
            border-radius: 5px;
            text-decoration: none;
            color: #ffffff;
            cursor: pointer;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .dialog {
            position: fixed;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            visibility: hidden;
            opacity: 0;
            transition: opacity linear 0.2s;
        }

        .overlay-close {
            position: absolute;
            width: 100%;
            height: 100%;
            cursor: default;
        }

        .dialog:target {
            visibility: visible;
            opacity: 1;
        }


        .overlay {
            background-color: rgba(0, 0, 0, 0.3);
        }

        .dialog-body {
            max-width: 400px;
            position: relative;
            padding: 16px;
            background-color: #fff;
        }

        .dialog-close-btn {
            position: absolute;
            top: 2px;
            right: 6px;
            text-decoration: none;
            color: #333;
        }
    </style>
@endsection
    <h3>{{ __('labels.registrant_information.title') }}</h3>
    <p class="eol">{{ __('labels.registrant_information.desc') }}</p>

    <dl class="w16em eol clearfix">
        <dt>{{ __('labels.registrant_information.action') }}</dt>
        <dd>
            <ul>
                <li><input type="button" value="{{ __('labels.registrant_information.button_1') }}" id="btn-click-copy"
                        class="btn_b" /></li>
                <li>{{ __('labels.registrant_information.text_b') }}</li>
                <li><a class="btn_b dialog-btn" href="#my-dialog">{{ __('labels.registrant_information.button_2') }}</a>
                </li>
            </ul>
        </dd>
    </dl>
    <div class="dialog overlay" id="my-dialog">
        <a href="#" class="overlay-close"></a>
        <div class="dialog-body">
            <a class="dialog-close-btn" href="#">&times;</a>
            @if ($information && $information->appTrademark && $information->appTrademark->trademarkInfo->count())
                <p><input type="submit" value="{{ __('labels.registrant_information.button_3') }}" class="btn_a" />
                </p>
                <table class="normal_b mb10">
                    <tr>
                        <th>{{ __('labels.registrant_information.modal_1') }}</th>
                        <th>{{ __('labels.registrant_information.modal_2') }}</th>
                        <th class="em04">{{ __('labels.registrant_information.modal_3') }}</th>
                    </tr>
                    @foreach ($information->appTrademark->trademarkInfo as $item)
                        <tr>
                            <td>{{ $item->trademark_infos_name }}</td>
                            <td>{{ $item->nations_name }}-{{ $item->prefectures_name }}-{{ $item->address_second }}-{{ $item->address_three }}
                            </td>
                            <td class="center"><input type="radio" name="check" /></td>
                        </tr>
                    @endforeach
                </table>
                <p class="eol"><button class="btn_a">{{ __('labels.registrant_information.button_3') }}</button>
                </p>
                <p class="center fs12"><a href="#" onClick="window.close(); return false;"
                        class="btn_b">{{ __('labels.registrant_information.button_close_modal') }}</a>
                </p>
            @endif
        </div>
    </div>
    {{-- <form id="form" action="{{ route('user.update-information', ['trademark_id' => $information->id]) }}" method="POST"> --}}
    {{-- @csrf --}}
    <div class="append_html">
        <dl class="w16em clearfix registrant_information">
            <dt>{{ __('labels.registrant_information.form.type_acc') }} <span class="red">*</span></dt>
            <dd class="eTypeAcc">
                <ul class="r_c clearfix fTypeAcc">
                    <li>
                        <label><input type="radio" class="data-type_acc" name="data[0][type_acc]" value="1" />
                            {{ __('labels.registrant_information.form.type_acc_1') }}</label>
                    </li>
                    <li>
                        <label><input type="radio" class="data-type_acc" name="data[0][type_acc]" value="2" />
                            {{ __('labels.registrant_information.form.type_acc_2') }}</label>
                    </li>
                </ul>
            </dd>

            <dt>{{ __('labels.registrant_information.form.name') }} <span class="red">*</span></dt>
            <dd><input type="text" class="data-name" name="data[0][name]" /></dd>

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

            <dt>{{ __('labels.registrant_information.form.m_prefecture_id') }}<span class="red">*</span></dt>
            <dd class="ePerfecture">
                <select name="data[0][m_prefecture_id]" class="data-m_prefecture_id" id="m_prefecture_id">
                    <option value="">{{ __('labels.registrant_information.select_default2') }}</option>
                    @if (isset($prefectures))
                        @foreach ($prefectures as $k => $item)
                            <option value="{{ $k }}" {{ old('m_prefecture_id') == $k ? 'selected' : '' }}>
                                {{ $item }}</option>
                        @endforeach
                    @endif
                </select>
            </dd>

            <dt>{{ __('labels.registrant_information.form.address_second') }}<span class="red">*</span></dt>
            <dd>
                <input type="text" class="data-address_second em30" name="data[0][address_second]" /><br />
                <span class="input_note">{{ __('labels.registrant_information.form.note_address_second') }}</span>
            </dd>

            <dt>
                {{ __('labels.registrant_information.form.address_three_1') }}<br />
                {{ __('labels.registrant_information.form.address_three_2') }}
            </dt>
            <dd>
                <input type="text" class="data-address_three em30" name="data[0][address_three]" /><br />
                <span class="input_note">{{ __('labels.registrant_information.form.note_address_three') }}</span>
            </dd>
        </dl>
    </div>
    <p class="eol">
        <input type="button" value="{{ __('labels.registrant_information.form.submit') }}"
            class="custom-input click_append">
    </p>
    {{-- </form> --}}
