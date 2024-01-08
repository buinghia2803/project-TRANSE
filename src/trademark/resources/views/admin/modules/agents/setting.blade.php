@extends('admin.layouts.app')
@section('main-content')
<!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        @if ($message = Session::get('success'))
            <div class="alert alert-success message-booking">
                <button type="button" class="close" style="margin-top: -5px !important;background-color: transparent;border: none;" data-dismiss="alert">&times;</button>
                <span class="">{{ $message }}</span>
            </div>
        @endif
        @if ($message = Session::get('error'))
            <div class="alert alert-danger message-booking">
                <button type="button" class="close" style="margin-top: -5px !important;background-color: transparent;border: none;" style="background-color: transparent;border: none;" data-dismiss="alert">&times;</button>
                <span class="">{{ $message }}</span>
            </div>
        @endif
        <div class="wide clearfix">
            <form action="{{ route('admin.agent.update-create') }}" method="POST" id="agent_form">
            @csrf
            @method('POST')
            <div id="content_deposit_form">
                <h3>{{ __('labels.agent.agent_setting')}}</h3>
                <input type="hidden" name="agents_data" id="agents_data"/>
                <input type="hidden" name="agents_data_delete" id="agents_data_delete"/>
                @foreach ($agents as $key => $agent)
                    <div class="agent_form_item">
                            <input type="hidden" name="admin_id"  value="{{ $agent->admin_id }}" />
                            <input type="hidden" name="id"  value="{{ $agent->id }}" />
                            <dl class="w14em clearfix">
                                <dt>{{ __('labels.agent.identification_number')}} <span class="red">*</span></dt>
                                <dd><input type="text" class="identification_number" nospace name="identification_number_{{ $agent->id }}" value="{{ $agent->identification_number ?? '' }}" nospace/></dd>

                                <dt>{{ __('labels.agent.agent_name')}}<span class="red">*</span></dt>
                                <dd><input type="text" class="name" name="name_{{ $agent->id }}"  value="{{ $agent->name ?? '' }}" /></dd>

                                <dt>{{ __('labels.agent.deposit_account_number')}}</dt>
                                <dd><input type="text" class="deposit_account_number" name="deposit_account_number_{{ $agent->id }}" value="{{ $agent->deposit_account_number ?? '' }}" nospace/></dd>

                                <dt>{{ __('labels.agent.deposit_type')}} <span class="red">*</span></dt>
                                <dd class="fRadio">
                                    <ul class="r_c clearfix radio-group">
                                        <li >
                                            <label><input type="radio" class="deposit_type" name="deposit_type_{{ $agent->id }}" value="1" {{ isset($agent->deposit_type) && $agent->deposit_type == 1 ? 'checked' : '' }}
                                                  />{{ __('labels.agent.advance_payment')}}</label>
                                        </li>
                                        <li>
                                            <label><input type="radio" class="deposit_type" name="deposit_type_{{ $agent->id }}" value="2" {{ isset($agent->deposit_type) && $agent->deposit_type == 2 ? 'checked' : '' }}
                                                 />{{ __('labels.agent.advance_payment_credit')}}</label>
                                        </li>
                                    </ul>
                                </dd>
                            </dl>
                            @if ($key)
                                <p><button position="{{ $key }}" type="button" class="btn_d btn_delete small" >{{ __('labels.agent.delete') }} </button></p>
                            @endif
                            <hr>
                    </div>
                @endforeach
            </div>
            <p><a id="addition_deposit" href="#!">{{ __('labels.agent.addition')}} +</a></p>
            <ul class="footerBtn clearfix">
                <input type="hidden" name="redirect" id="redirect_to" value="admin.agent.index">
                <li><input type="submit" value="{{ __('labels.agent.save')}}" class="btn_c" /></li>
                <li><input type="button" id="gonna_setting_set" value="{{ __('labels.agent.gonna_agent_combination_setting')}}" class="btn_b" /></li>
            </ul>

            <!-- Trigger/Open The Modal -->
            <div id="delete_confirm_modal" class="modal">
                <div class="modal-content w-30">
                    <span class="close">&times;</span>
                    <input type="hidden" disabled name="id-selected">
                    <p class="modal-message">{{ __('messages.agent.delete_attention')}}</p>
                    <div class="d-flex justify-content-center modal-button">
                        <button type="button" class="btn_b btn_ok" id="btn_ok" >{{ __('labels.agent.btn_ok') }}</button>

                        <button  type="button" class="btn_d ml-2 btn_cancel" id="btn_cancel" >{{ __('labels.agent.btn_cancel') }}</button>
                    </div>
                </div>
            </div>
        </form>
        </div>
    <!-- /contents inner -->
    </div>
    <style>
        .modal {
            background-color: #dcdcdcb8;
        }
        .modal-content {
            margin: 40vh auto;
            padding: 2em 1em;
        }
    </style>

<!-- /contents -->
@endsection

@section('footerSection')
<script src="{{ asset('common/js/validate.js') }}"></script>
<script type="text/javascript">
    /**
     * Delete deposit in DOM
     */
    function deleteDeposit() {
        $('body').on('click', '.btn_delete',function(e) {
            elementDelete = $(this).closest('.agent_form_item')
            selectedId = elementDelete.find('input[name=id]').attr('value')

            $('#delete_confirm_modal').css('display', 'block')
        })
    }

    /**
     * Inject to event
     */
    function custumEventInput() {
        $('body').find('.identification_number, .deposit_account_number', '.name').off('input, focus').on('input, focus', function() {
            const val = $(this).val().replace(/[ぁ-んァ-ン一-龥\w\d\s~!@#$%-`=^\\&*+_\(\)\/<>?/.,':;|{}\[\]]/g, '').trim()
            $(this).val(val)
            $(this).attr('value', val)
        })

        $('body').find('.identification_number, .deposit_account_number', '.name').off('focusout').on('focusout', function() {
            const val = $(this).val()
            if(!val) {
                $("#agent_form").validate().element($(this)[0]);
            }
        })

        $('body').find('input.name').off('focusout').on('focusout', function() {
            const val = $(this).val().replace('　', '').trim()
            $(this).val(val)
            $(this).attr('value', val)
            if(!val) {
                $("#agent_form").validate().element($(this)[0]);
            }
        })

        $('body').find('input.name').off('input').on('input', function() {
            const val = $(this).val().trim()
            if(!val) {
                $("#agent_form").validate().element($(this)[0]);
            }
            $(this).attr('value', val)
            $(this).val(val)
        })
    }

    /**
     * Handling onchange event diposit type
     */
    $('body').on('click', 'input.deposit_type', function () {
        const depositAccNum = $(this).closest('.agent_form_item').find('.deposit_account_number').val()
        if(+$(this).val() == 1 && !depositAccNum) {
            $(this).prop('checked', false)
            $('#agent_form').submit()
        }
    })

    $('body').on('focusout', 'input.deposit_account_number', function () {
        if(!$(this).val()) {
            $(this).closest('.agent_form_item').find('.deposit_type:checked').prop('checked', false)
        }
    })

    /**
     * initial validation for form.
     */
    function initValidationForm() {
        $('#agent_form').validate().destroy()
        $('.agent_form_item').each(function() {
            $(this).find('input').each(function() {
                const name = $(this).attr('name')
                if(name.includes('identification_number')) {
                    ruleValidate[name] = {
                        required: true,
                        defaultLength: 9,
                        isNumberFullWidth: true
                    }
                    messageValidate[name] = {
                        required: errorMessageRequired,
                        defaultLength: errorMessageMaxLength9,
                        isNumberFullWidth: errorMessageMaxLength9,
                    }
                }
                if(name.includes('name')) {
                    ruleValidate[name] = {
                        required: true,
                        invalidCharacter: 50
                    }
                    messageValidate[name] = {
                        required: errorMessageRequired,
                        invalidCharacter: errorMessageInvalidCharacter
                    }
                }
                if(name.includes('deposit_account_number')) {
                    ruleValidate[name] = {
                        maxlength: 6,
                        isNumberFullWidth: true
                    }
                    messageValidate[name] = {
                        maxlength: errorMessageMaxLength6,
                        isNumberFullWidth: errorMessageMaxLength6,
                    }
                }
                if(name.includes('deposit_type')) {
                    ruleValidate[name] = {
                        required: true
                    }
                    messageValidate[name] = {
                        required: errorMessageRequiredRadioOrSelect
                    }
                }
            })
        })

        validation('#agent_form', ruleValidate, messageValidate);
    }
</script>
<script type="text/javascript">
    const errorMessageRequired = '{{ __('messages.common.errors.Common_E001') }}';
    const errorMessageInvalidCharacter = '{{ __('messages.common.errors.Common_E010') }}';
    const errorMessageFullWidth = '{{ __('messages.common.errors.Common_E009', ['attr' => 9]) }}';
    const errorMessageMaxLength9 = '{{ __('messages.common.errors.Common_E009', ['attr' => 9]) }}';
    const errorMessageMaxLength6 = '{{ __('messages.common.errors.Common_E009', ['attr' => 6]) }}';
    const errorMessageRequiredRadioOrSelect = '{{ __('messages.common.errors.Common_E025') }}';

    const agentsData = JSON.parse(@json($agentsJson));
    let maxId = 1;
    let selectedId = 0;
    let elementDelete = null
    if(Array.isArray(agentsData) && agentsData.length) {
        maxId = agentsData[agentsData.length-1].id
    }
    const agentsDataDelete = []
    const ruleValidate = {}
    const messageValidate = {}
    const data = []

    // Call function delete deposit
    initValidationForm()
    validation('#agent_form', ruleValidate, messageValidate);
    custumEventInput()
    deleteDeposit()

    $.validator.addMethod("isNumberFullWidth", function(value, e) {
        const regex = /^[０-９]*$/gu
        $(e).attr('value', value)

        return regex.test(value);
    });

    $.validator.addMethod("invalidCharacter", function(value, e, param) {
        const regex = /^[ぁ-んァ-ン一-龥－・]*$/gu
        if(!value.trim()) {
            return
            $(e).val(value.trim())
            $(e).attr('value', value.trim())
        }
        if(value.length > param) {
            return false
        }

        return regex.test(value);
    });

    $.validator.addMethod("defaultLength", function(value, e, param) {
        const regex = /^[０-９]*$/gu
        if(value.length != param) {
            return false
        }

        $(e).attr('value', value)

        return regex.test(value);
    });

    $('#btn_ok').click(function() {
        const inputId = elementDelete.find('input[name=id]')
        delete agentsData[elementDelete.find('.btn_delete').attr('position')]
        if(inputId.length) {
            agentsDataDelete.push(inputId.attr('value') || inputId.val())
            $('#agents_data_delete').val(JSON.stringify(agentsDataDelete))
            $('#agents_data_delete').attr('value', JSON.stringify(agentsDataDelete))
        }
        elementDelete.remove()
        if(inputId.length) {
            $.ajax({
                url: "setting/" + inputId.val(),
                type: "delete",
                data: {
                    _token: "{{ csrf_token() }}",
                }
            });
        }

        $('#delete_confirm_modal').css('display', 'none')
    })

    $('#btn_cancel').click(function() {
        $('#delete_confirm_modal').css('display', 'none')
        selectedId = 0
    })

    $("#delete_confirm_modal .close").on('click', function () {
        $('#delete_confirm_modal').css('display', 'none')
    });

    $('#gonna_setting_set').click(function() {
        $('#redirect_to').attr('value', 'admin.agent.setting-set')
        $('#agent_form').submit();
    })

    $('#agent_form').submit(function(e) {
        if(!$('#agent_form').valid()) return

        $('.agent_form_item').each(function() {
            const item = {}
            $(this).find('input').each(function() {
                if($(this).attr('type') == 'radio' ) {
                    if ($(this).is(':checked')) {
                        item[$(this).attr('name')] = $(this).attr('value') ||  $(this).val()
                    }
                } else {
                    item[$(this).attr('name')] = $(this).attr('value') ||  $(this).val()
                }
            })
            data.push(item)
        })

        $('#agents_data').val(JSON.stringify(data))
    })

    $('#addition_deposit').click(function () {
        agentsData.push({
            admin_id: '',
            name: '',
            deposit_account_number: '',
            deposit_type: '',
            identification_number: '',
        });
        ++maxId
        $('#content_deposit_form').append(`
            <div class="agent_form_item">
                <dl class="w14em clearfix">
                <dt>識別番号 <span class="red">*</span></dt>
                <dd><input type="text" class="identification_number" name="identification_number_${maxId}" nospace/></dd>
                <dt>代理人氏名 <span class="red">*</span></dt>
                <dd><input type="text" class="name" name="name_${maxId}"/></dd>
                <dt>予納台帳番号</dt>
                <dd><input type="text" class="deposit_account_number" name="deposit_account_number_${maxId}" nospace/></dd>
                <dt>納付方法 <span class="red">*</span></dt>
                <dd class="fRadio">
                    <ul class="r_c clearfix radio-group">
                        <li><label><input type="radio" class="deposit_type" name="deposit_type_${maxId}" value="1" />予納</label></li>
                        <li><label><input type="radio" class="deposit_type" name="deposit_type_${maxId}" value="2" />指定立替納付（クレジット）</label></li>
                    </ul>
                </dd>
                </dl>
                <p><button type="button" position="${agentsData.length - 1}" class="btn_delete btn_d small"> {{ __('labels.agent.delete') }} </button></p>
                <hr>
            </div>
        `)

        // Call function delete deposit
        custumEventInput()
        deleteDeposit()
        initValidationForm()
    })
</script>
@endsection
