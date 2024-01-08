@extends('admin.layouts.app')
@section('main-content')
    <!-- contents -->
    <div id="contents">
        <!-- contents inner -->
        <div class="wide clearfix">
            @include('admin.components.includes.messages')
            <form id="form_submit_agent" action="{{ route('admin.agent.crud-setting-set') }}" method="POST">
                @csrf
                @method('POST')
                <div id="alert_header"></div>
                <h3>{{ __('labels.agent.combination_settings') }}</h3>
                <p>{{ __('labels.agent.explain_combination_settings') }}</p>
                <input type="hidden" name="redirect" id="redirect_to" value="admin.agent.setting-set">
                <dl class="w12em clearfix">
                    <div id="form_agent_group">
                        @if (count($agentGroups))
                            @foreach ($agentGroups as $key => $agentGroup)
                                <div class="d-flex mt-3">
                                    <input type="hidden" name="id_{{ $agentGroup->id }}" position="{{ $agentGroup->id }}" value="{{ $agentGroup->id }}">
                                    <input type="hidden" name="actionType_{{$agentGroup->id}}" value="edit">
                                    <dt class="mb-1 el_input_name mt-1">
                                        <input type="radio" class="status_choice_cls" {{ $agentGroup->status_choice ? 'checked': '' }} class="" name="statusChoice_{{ $agentGroup->id }}">
                                        <input type="text" name="name_{{ $agentGroup->id }}"class="em08 cls-name" value="{{ $agentGroup->name }}" />
                                    </dt>
                                    <div class="d-flex mb-1 group_select_agent mt-1">
                                        <div class="item_select_agent" position="{{$agentGroup->id}}">
                                            @if ($agentGroup->collectAgent->count())
                                                @foreach ($agentGroup->collectAgent as $index => $item)
                                                    <span class="select-group-box">
                                                        <span class='sp_select_agent select-group show-error'>
                                                            <span>
                                                                @if($item->type == AGENT_SELECTION_TYPE)
                                                                    <select name="select_agent_{{ $agentGroup->id }}[]" value="{{$item->agent_id}}" class="select_agent">
                                                                        <option value="0" selected="true">代理人選択</option>
                                                                        @foreach ($agents as $key => $value)
                                                                            <option {{ $key == $item->agent_id ? 'selected' : '' }} value="{{ $key }}">{{ $value }}</option>
                                                                        @endforeach
                                                                    </select> <span style="{{ $index ? 'opacity: 0;': '' }}" class="red">*</span>　
                                                                @else
                                                                    <span>
                                                                        <select name="select_appoint_agent_{{ $agentGroup->id }}[]" class="select_agent select_appoint_agent">
                                                                            <option value="0">選任代理人選択</option>
                                                                            @foreach ($agents as $key => $value)
                                                                                <option {{ $key == $item->agent_id ? 'selected' : '' }}  value="{{ $key }}">{{ $value }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                        <span style="opacity: 0;" class="red">*</span>　
                                                                    </span>
                                                                @endif
                                                            </span>
                                                            <br>
                                                        </span>
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="select-group-box">
                                                    <select name="select_agent_{{ $agentGroup->id }}[]" value="{{$agentGroup->agent_id}}" class="select_agent">
                                                        <option value="0" selected="true">代理人選択</option>
                                                        @foreach ($agents as $key => $value)
                                                            <option value="{{ $key }}">{{ $value }}</option>
                                                        @endforeach
                                                    </select> <span style="" class="red">*</span>　
                                                </span>
                                            @endif
                                            {{-- <span class="item-next"></span> --}}
                                            <span>
                                                <button type="button" class="btn_delete_select d-none btn_d small" style="display:none" > {{ __('labels.agent.delete') }} </button>　
                                                <a class="add_selection" href="#!">{{ __('labels.agent.agent_add') }} +</a>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="d-flex mt-3">
                                <div class="mb-1 el_input_name mt-1">
                                    <input type="hidden" name="actionType_1" value="new">
                                    <input type="radio" class="status_choice_cls" name="statusChoice_1"> <input type="text" name="name_1" class="em08" value="0" />
                                </div>
                                <div class="d-flex mb-1 group_select_agent mt-1">
                                    <div class="item_select_agent" position="1">
                                        <span class="select-group-box">
                                            <span class='sp_select_agent select-group show-error'>
                                                <span>
                                                    <select name="select_agent_1[]" class="select_agent">
                                                        <option value="0" selected="true">代理人選択</option>
                                                        @foreach ($agents as $key => $value)
                                                            <option value="{{ $key }}">{{ $value }}</option>
                                                        @endforeach
                                                    </select> <span class="red">*</span>　
                                                </span>
                                                <br>
                                            </span>
                                        </span>
                                    </div>
                                    <span>
                                        <button type="button" class="btn_delete_select btn_d small" style="display:none;"> {{ __('labels.agent.delete') }} </button>　
                                        <a class="add_selection" href="#!">{{ __('labels.agent.agent_add') }} +</a>
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex mt-3">
                                <div class="mb-1 el_input_name mt-1">
                                    <input type="hidden" name="actionType_2" value="new">
                                    <input type="radio" class="status_choice_cls" name="statusChoice_2">
                                    <input type="text" name="name_2" class="em08" value="0" />
                                </div>
                                <div class="d-flex mb-1 group_select_agent mt-1">
                                    <div class="item_select_agent" position="2">
                                        <span class="select-group-box">
                                            <span class='sp_select_agent select-group show-error'>
                                                <span>
                                                    <select name="select_agent_2[]" class="select_agent" style="margin-bottom: 10px">
                                                        <option value="0" selected="true">代理人選択</option>
                                                        @foreach ($agents as $key => $value)
                                                            <option value="{{ $key }}">{{ $value }}</option>
                                                        @endforeach
                                                    </select> <span class="red">*</span>　
                                                </span>
                                                <br>
                                            </span>
                                        </span>
                                    </div>
                                    <span>
                                        <button type="button" class="btn_delete_select btn_d small" style="display: none;" > {{ __('labels.agent.delete') }} </button>　
                                        <a class="add_selection" href="#!">{{ __('labels.agent.agent_add') }} +</a>
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex mt-3">
                                <div class="mb-1 el_input_name mt-1">
                                    <input type="hidden" name="actionType_3" value="new">
                                    <input type="radio" class="status_choice_cls" name="statusChoice_3">
                                    <input type="text" name="name_3" class="em08" value="0" />
                                </div>
                                <div class="d-flex mb-1 group_select_agent mt-1" >
                                    <div class="item_select_agent" position="3">
                                        <span class="select-group-box">
                                            <span class='sp_select_agent select-group show-error'>
                                                <span>
                                                    <select name="select_agent_3[]" class="select_agent">
                                                        <option value="0" selected="true">代理人選択</option>
                                                        @foreach ($agents as $key => $value)
                                                            <option value="{{ $key }}">{{ $value }}</option>
                                                        @endforeach
                                                    </select> <span class="red">*</span>　
                                                </span>
                                                <br>
                                            </span>
                                        </span>
                                    </div>
                                    <span>
                                        <button type="button" class="btn_delete_select btn_d small" style="display: none;"> {{ __('labels.agent.delete') }} </button>　
                                        <a class="add_selection" href="#!">{{ __('labels.agent.agent_add') }} +</a>
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                </dl>
                <p><a id="btn_add_agent_group" href="#!">{{ __('labels.agent.addition') }} +</a></p>
                <br />
                <br />

                <ul class="footerBtn2 clearfix">
                    <li><a href="#" id="gonna_dairinin" onclick="window.location='{{ route('admin.agent.index') }}'" class="btn_b" >{{ __('labels.agent.gonna_agent') }}</a></li>
                    <li><button type="button" id="button_submit" class="btn_c cls-btn-submit" >{{ __('labels.agent.save') }}</button></li>
                </ul>
            </form>
        </div><!-- /contents inner -->

    </div><!-- /contents -->
    <style>
        .d-none {
            display: none;
        }
        .error-msg {
            color: #f00;
        }
        #gonna_dairinin {
            height: 31px;
            line-height: 18px;
        }
    </style>
@endsection
@section('footerSection')
<script type="text/JavaScript" src="{{ asset('common/js/validate.js') }}"></script>
<script type="text/javascript">
    const errorMessageRequired = '{{ __('messages.common.errors.Common_E001') }}';
    const errorMessageRequiredRadioOrSelect = '{{ __('messages.common.errors.Common_E025') }}';
    const errorMessageMaxLength255 = '{{ __('messages.common.errors.Common_E031') }}';
    const errorMessageMaxLength = '{{ __('messages.common.errors.Register_U001_E007') }}';
    const errorMessageIsFullWidth = '{{ __('messages.common.errors.Register_U001_E007') }}';

    $('.group_select_agent').each(function (key, item) {
        if($(item).find('.select-group-box').length <= 1) {
            $(item).find('.btn_delete_select').css('display', 'none')
        } else {
            $(item).find('.btn_delete_select').css('display', 'inline-block')
        }
    })
    if($('#form_agent_group').children().length >= 10) {
        $('#btn_add_agent_group').css('display', 'none')
    }

    $('.cls-btn-submit').off('click').on('click', function () {
        let error = false
        let unChecked = true
        $('.cls-name').focusout()
        $('#form_submit_agent').find('select, input[type="radio"]').each(function (key, item) {
            if(item.getAttribute('type') == 'radio' && item.checked) {
                unChecked = false
            }
            if(item.getAttribute('type') != 'radio' && !(+$(item).val()) && !$(item).hasClass('select_appoint_agent')) {
                const element = $(item).closest('.sp_select_agent');
                if(!element.find('.error-input').length) {
                    const selectGroup = $(item).closest('.select-group-box')
                    const exist = $(item).closest('.select-group-box').find('.error-msg');
                    if(!exist.length) {
                        $(item).closest('.select-group-box').append(`<div class="error-msg">${errorMessageRequiredRadioOrSelect}</div>`)
                    }
                }
                error = true
            }
        })
        if($('body').find('.error-msg').length) {
            error = true
        }else {
            error = false
        }

        if($('#form_submit_agent').valid() && !error && !unChecked){
            // if($(this).attr('id') == 'gonna_dairinin') {
            //     $('#redirect_to').attr('value', 'admin.agent.index')
            // }
            $('#form_submit_agent').submit()
        } else {
            // $('#alert_header').html(`
            //     <div class="alert alert-danger message-booking">
            //         <button type="button" style="background-color: transparent;border: none;" class="close" data-dismiss="alert">&times;</button>
            //         <span>選択してください。</span>
            //     </div>
            // `)
            document.querySelector('#alert_header').scrollIntoView({
                behavior: 'smooth'
            });
        }
    })

    $('body').on('click', '.close', function () {
        $(this).closest('.alert').remove()
    })

    $('body').on('focusout', '.cls-name', function() {
        if (!$(this).val()) {
            $(this).closest('.el_input_name').find('.error-msg').remove()
            $(this).closest('.el_input_name').append(`<div class="error-msg">&#xFEFF;入力してください。</div>`);
        } else {
            $(this).closest('.el_input_name').find('.error-msg').remove()
        }

    })

    $('body').on('change','.select_agent' ,function (e) {
        $(this).find('option[selected="selected"]').removeAttr('selected')
        $(this).find(`option[value="${$(this).val()}"]`).attr('selected', 'selected')
        const value = $(this).val()
        let exist = false;
        $(this).closest('.item_select_agent').find('select').each(function(key, item) {
            if(!$(item).is(':focus')) {
                if($(item).val() == value) {
                    exist = true;

                    return
                }
            }
        })
        if(exist) {
            $(this).val('0')
            $(this).find('option[selected]').removeAttr('selected')
            $(this).find(`option[value="0"]`).attr('selected')
            $(this).closest('.sp_select_agent').find('error-input').remove()
            return
        }else {
            $(this).find('option[selected="selected"]').removeAttr('selected')
            $(this).find(`option[value="${$(this).val()}"]`).attr('selected', 'selected')
            $(this).closest('.select-group-box').find('.error-msg').remove()
        }
    })

    /**
     * Add new a selection of agent group
     */
    function addAppointAgentSelection() {
        $('body').off('click').on('click', '.add_selection', function () {
            const position = $(this).closest('.group_select_agent').find('.item_select_agent').attr('position')
            const countChildren = $(this).closest('.group_select_agent').find('.item_select_agent').children().length
            $(this).closest('.group_select_agent').find('.item_select_agent .select-group-box').last().after(`
                <span class="select-group-box">
                    <span class="sp_select_agent select-group show-error">
                        <span>
                            <select name="select_appoint_agent_${position}[]" class="select_agent select_appoint_agent" >
                                <option value="0" selected="true">選任代理人選択</option>
                                @foreach ($agents as $key => $value)
                                    <option value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                            <span style="opacity: 0;" class="red">*</span>　
                        </span>
                        <br>
                    </span>
                </span>
                `
            )

            if($(this).closest('.group_select_agent').find('.item_select_agent .sp_select_agent').length >= 10) {
                $(this).closest('.group_select_agent').find('.add_selection').css('display', 'none')
            } else {
                $(this).closest('.group_select_agent').find('.add_selection').css('display', 'inline-block')
            }

            if($(this).closest('.group_select_agent').find('.item_select_agent').children().length <= 1) {
                $(this).closest('.group_select_agent').find('.btn_delete_select').css('display', 'none')
            } else {
                $(this).closest('.group_select_agent').find('.btn_delete_select').css('display', 'inline-block')
            }

            $(this).closest('.group_select_agent').find('select').each(function(key, item) {
                $(item).find(`option[value="${$(item).val()}"]`).attr('selected', $(item).val())
            })
        })
    }

    /**
     * Add new an agent group
     */
    function addAgentGroup() {
        $('#btn_add_agent_group').click(function() {

            const parentPosition = $('#form_agent_group').children().length
            const countChildren = $(this).closest('.group_select_agent').find('.item_select_agent').children().length
            // Add max 10 element
            if($('.group_select_agent').length + 1  >= 10) {
                $('#btn_add_agent_group').css('display', 'none')
                // return
            } else {
                $('#btn_add_agent_group').css('display', 'inline-block')
                // return
            }


            const rule = {}
            const messages = {}
            rule[`name_${parentPosition + 1}`] = { required: true, maxlength: 255 }
            messages[`name_${parentPosition + 1}`] = { required: errorMessageRequired, maxlength: errorMessageMaxLength255 }
            let max = 1
            $('input[name*=id_]').each(function () {
                if(max < +$(this).attr('position')) {
                    max = +$(this).attr('position')
                }
            })

            $('#form_agent_group').append(`
                <div class="d-flex mt-3">
                    <div class="mb-1 el_input_name mt-1">
                        <input type="hidden" name="id_${max + 1}" position="${max + 1}" value="new">
                        <input type="hidden" name="actionType_${max + 1}" value="new">
                        <input type="radio" class="status_choice_cls" name="statusChoice_${max + 1}">
                        <input type="text" name="name_${max + 1}" class="em08 cls-name" />
                    </div>
                    <div class="d-flex mb-1 group_select_agent mt-1">
                        <div class="item_select_agent" position="${max + 1}">
                            <span class="select-group-box">
                                <span class='sp_select_agent select-group show-error'>
                                    <span>
                                        <select name="select_agent_${max + 1}[]" class="select_agent">
                                            <option value="0" selected="true">代理人選択</option>
                                            @foreach ($agents as $key => $value)
                                                <option value="{{ $key }}">{{ $value }}</option>
                                            @endforeach
                                        </select> <span class="red">*</span>　
                                    </span>
                                    <br>
                                </span>
                            </span>
                            <span>
                                <button type="button" class="btn_delete_select btn_d small" style="margin-bottom: 10px; display: none;"> {{ __('labels.agent.delete') }} </button>　
                                <a class="add_selection" href="#!">{{ __('labels.agent.agent_add') }} +</a>
                            </span>
                        </div>
                    </div>
                </div>
            `)
            // Add new select for agent group
            addAppointAgentSelection(max)
        })
    }
</script>
<script type="text/javascript">
    $('body').on('click change', '.status_choice_cls', function() {
        $('.status_choice_cls').each(function (idx, item) {
            if(!$(item).is(':focus')) {
                item.checked = false
            }
        })
        $(this).closest('.el_input_name').find('input[name*=name]').focus()
    })

    $(this).closest('.group_select_agent').find('select').each(function(key, item) {
        $(item).find(`option[value="${$(item).val()}"]`).attr('selected', $(item).val())
    })
    // Add new select for agent group
    addAppointAgentSelection()
    // Add new an agent group
    addAgentGroup()
    const childFormAgentGroup = $('#form_agent_group').children().each(function (key, item) {

        // Set selected in option select.
        $(this).find('select').each(function(key, item) {
            $(item).find(`option[value="${$(item).val()}"]`).attr('selected', $(item).val())
        })
    });


    $('.btn_delete_select').click(function ()  {
        const element = $(this).closest('.group_select_agent').find('.sp_select_agent')
        if(element.length > 1) {
            element.last().remove();
        }

        if($(this).closest('.group_select_agent').find('.item_select_agent .sp_select_agent').length >= 10) {
            $(this).closest('.group_select_agent').find('.add_selection').css('display', 'none')
        } else {
            $(this).closest('.group_select_agent').find('.add_selection').css('display', 'inline-block')
        }

        if($(this).closest('.group_select_agent').find('.item_select_agent .sp_select_agent').length <= 1) {
            $(this).closest('.group_select_agent').find('.btn_delete_select').css('display', 'none')
        } else {
            $(this).closest('.group_select_agent').find('.btn_delete_select').css('display', 'inline-block')
        }
    })

</script>
@endsection
