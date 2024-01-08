@extends('admin.layouts.app')

@section('main-content')
    <!-- contents -->
    <div id="contents">
        @include('compoments.messages')
        <!-- contents inner -->
        <div class="wide clearfix">
            <form id="form" action="{{ route('admin.update.trademark', ['id' => $id]) }}" method="POST">
                @csrf
                <div class="info mb20">
                    {{-- Trademark table --}}
                    @include('admin.components.includes.trademark-table', [
                        'table' => $trademarkTable,
                    ])
                </div>
                <!-- ↑↑管理画面共通【基本情報】とボタン↑↑ -->
                <h3>{{ __('labels.apply_trademark.title') }}</h3>
                <dl class="w16em clearfix">

                    <dt class="nm">{{ __('labels.apply_trademark.text_1') }}</dt>
                    <dd class="nm">{{ __('labels.apply_trademark.text_2') }}
                    </dd>

                    <dt class="nm">{{ __('labels.apply_trademark.trademark_number') }}</dt>
                    <dd class="nm">{{ isset($trademark['trademark_number']) ? $trademark['trademark_number'] : '' }}
                    </dd>

                    <dt class="nm">{{ __('labels.apply_trademark.text_3') }}</dt>
                    <dd class="nm">{{ __('labels.apply_trademark.text_4') }}
                    </dd>
                    <dt class="nm">{{ __('labels.apply_trademark.name_trademark') }}</dt>
                    <dd class="nm">
                        @if (empty($trademark['name_trademark']) && isset($trademark['image_trademark']))
                            <img src="{{ asset($trademark['image_trademark']) }} " class="css-class" alt=""
                                style="width: 100px; height: 100px;">
                        @else
                            {{ isset($trademark['name_trademark']) ? $trademark['name_trademark'] : '' }}
                        @endif
                    </dd>
                </dl>
                <h5>{{ __('labels.apply_trademark.text_11') }}</h5>
                <h5>【区分、商品・サービス名】</h5>
                @if (isset($trademark['data']))
                    @foreach ($trademark['data'] as $key => $mDistinct)
                        <p class="mb00">　 {{ __('labels.apply_trademark.name_distinct', ['attr' => $key]) }}</p>
                        <dl class="w16em clearfix">
                            <dt class="nm">{{ __('labels.apply_trademark.name_product') }}</dt>
                            <dd class="nm">
                                @php
                                    $productNameArray = $mDistinct->pluck('product_name')->toArray();
                                    $productNameStr = implode('，', $productNameArray);
                                @endphp
                                {{ $productNameStr ?? '' }}
                            </dd>
                        </dl>
                    @endforeach
                    {{-- @else --}}
                @endif
                <h5>{{ __('labels.apply_trademark.text_5') }}</h5>
                <dl class="w16em clearfix">
                    <dt class="nm">{{ __('labels.apply_trademark.address') }}</dt>
                    <dd class="nm">{{ $trademark['prefecture_name'] ?? '' }}{{ $trademark['address_second'] ?? '' }}{{ $trademark['address_three'] ?? '' }}</dd>

                    <dt class="nm">{{ __('labels.apply_trademark.trademark_info_name') }}</dt>
                    <dd class="nm">
                        {{ isset($trademark['trademark_info_name']) ? $trademark['trademark_info_name'] : '' }}
                    </dd>
                </dl>
                <h5>{{ __('labels.apply_trademark.text_7') }}</h5>
                <dl class="w16em clearfix">
                    <dt class="nm">{{ __('labels.apply_trademark.identification_number_first') }}</dt>
                    <dd class="nm">
                        {{ isset($agentIdentifierCodeNominated->identification_number) ? $agentIdentifierCodeNominated->identification_number : '' }}
                    </dd>
                    <dt class="nm">{{ __('labels.apply_trademark.text_8') }}</dt>
                    <dd class="nm"><br /></dd>

                    <dt class="nm">{{ __('labels.apply_trademark.name_agent') }}</dt>
                    <dd class="nm">
                        {{ isset($agentIdentifierCodeNominated->name) ? $agentIdentifierCodeNominated->name : '' }}
                    </dd>
                </dl>
                @if(isset($agentIdentifierCodeNotNominated->identification_number) || isset($agentIdentifierCodeNotNominated->name))
                <h5>{{ __('labels.apply_trademark.text_9') }}</h5>
                <dl class="w16em clearfix">
                    <dt class="nm">{{ __('labels.apply_trademark.identification_number_second') }}</dt>
                    <dd class="nm">
                        {{ isset($agentIdentifierCodeNotNominated->identification_number) ? $agentIdentifierCodeNotNominated->identification_number : '' }}
                    </dd>
                    <dt class="nm">{{ __('labels.apply_trademark.text_8') }}</dt>
                    <dd class="nm"><br /></dd>

                    <dt class="nm">{{ __('labels.apply_trademark.name_agent') }}</dt>
                    <dd class="nm">
                        {{ isset($agentIdentifierCodeNotNominated->name) ? $agentIdentifierCodeNotNominated->name : '' }}
                    </dd>
                </dl>
                @endif
                <h5>{{ __('labels.apply_trademark.text_10') }}</h5>
                <dl class="w16em clearfix">
                    @if (isset($agentIdentifierCodeNominated) && $agentIdentifierCodeNominated->deposit_type == \App\Models\Agent::DEPOSIT_TYPE_ADVENCE)
                        <dt class="nm">{{ __('labels.apply_trademark.deposit_account_number') }}</dt>
                        <dd class="nm">
                            {{ isset($agentIdentifierCodeNominated->deposit_account_number) ? $agentIdentifierCodeNominated->deposit_account_number : '' }}
                        </dd>
                    @elseif (isset($agentIdentifierCodeNominated) && $agentIdentifierCodeNominated->deposit_type == \App\Models\Agent::DEPOSIT_TYPE_CREDIT)
                        <dt class="nm">{{ __('labels.apply_trademark.deposit_account_number_v2') }}</dt>
                        <dd class="nm">　</dd>
                    @endif
                    <dt class="nm">{{ __('labels.apply_trademark.cost_print') }}</dt>
                    <dd class="nm">
                        {{ isset($trademark['cost_print_application_one_distintion']) ? mb_convert_kana($trademark['cost_print_application_one_distintion'] + $trademark['cost_print_application_add_distintion'] * (count($trademark['data']) - 1), 'KVN') : 0 }}
                    </dd>
                </dl>
                <p>{{ __('labels.apply_trademark.comment_office') }}<br />
                    <textarea class="normal" name="comment_office">{{ isset($trademark['comment_office']) ? $trademark['comment_office'] : '' }}</textarea>
                </p>
                <ul class="footerBtn clearfix">
                    <li><input type="submit" data-submit="{{ BACK_URL }}"
                            value="{{ __('labels.apply_trademark.btn_back') }}" class="btn_a">
                    </li>
                    <li><input type="submit" data-submit="{{ SUBMIT }}"
                            value="{{ __('labels.apply_trademark.btn_submit') }}" class="btn_c" /></li>
                </ul>
                <input type="hidden" name="submit_type">

            </form>
        </div>
        <!-- /contents inner -->
    </div>
    <!-- /contents -->
@endsection
@section('footerSection')
    <script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
    <script type="text/javascript">
        const errorMessageMaxLength = '{{ __('messages.common.errors.Common_E024') }}';
        const messageModal = '{{ __('messages.general.Common_E035') }}';
        const trademark = @json($trademark);
        const authRole = @json($authRole);
        const role_admin = {{ ROLE_OFFICE_MANAGER }};
        const routeTop = '{{ route('admin.home') }}';

        validation('#form', {
            'comment_office': {
                maxlength: 500,
            },
        }, {
            'comment_office': {
                maxlength: errorMessageMaxLength,
            },
        });
    </script>
    <script type="text/JavaScript" src="{{ asset('end-user/trademark/apply-trademark.js') }}"></script>
    @if (Request::get('type') == 'view'
        || $trademark['app_trademark_status'] == STATUS_WAITING_FOR_USER_CONFIRM
        || $trademark['app_trademark_status'] == STATUS_ADMIN_CONFIRM)
        <script>disabledScreen();</script>
    @endif
    @include('compoments.readonly', ['only' => [ROLE_OFFICE_MANAGER]])
@endsection
