class clsU000Taikai02 {
    checked = false
    constructor() {
        const self = this
        this.initValidate()
        window.addEventListener('load', function () {
            self.doLoad()
        })
    }

    /**
     * Do load all action and element.
     */
    doLoad() {
        this.onChangeCheckList()
        this.preSubmit()
    }

    /**
     * Init validate
     */
    initValidate() {
        // paymentRule, paymentMessage is constant in file payer-info.blade.php
        const rules = {
            'info_member_id': {
                required: true,
                minlength: 8,
                maxlength: 30,
                isValidInfoMemberId: true,
            },
            'password': {
                required: true,
                minlength: 8,
                maxlength: 16,
                isValidPassword: true
            },
            'code': {
                required: true
            },
            'reason_withdraw': {
                maxlength: 255
            }
        }
        const  messages = {
            'info_member_id': {
                required: errorMessageIsValidRequired,
                maxlength: errorMessageIsValidInfoMemberId,
                minlength: errorMessageIsValidInfoMemberId,
                isValidInfoMemberId: errorMessageIsValidInfoMemberId
            },
            'password': {
                required: errorMessageIsValidRequired,
                minlength: errorMessagePasswordInvalid,
                maxlength: errorMessagePasswordInvalid,
                isValidPassword: errorMessageValidPassword
            },
            'code': {
                required: errorMessageIsValidRequired
            },
            'reason_withdraw': {
                maxlength: errorMessageMaxLength255
            },
        }

        new clsValidation('#form', { rules: rules, messages: messages })
    }

    /**
     * It checks if at least one checkbox is checked. If not, it displays an error message.
     */
    onChangeCheckList() {
        const self = this
        $('.check_list').on('change', function () {
            self.checked = false
            for (const iterator of $('.check_list')) {
                if($(iterator).is(':checked')) {
                    self.checked = true
                    $('.check_list-error').empty()
                }
            }
            if(!self.checked) {
                $.confirm({
                    title: '',
                    content: messageCheckPlease,
                    buttons: {
                        ok: {
                            text: 'OK',
                            btnClass: 'btn-blue',
                            action: function () { }
                        }
                    }
                });
                // $('.check_list-error').html(errorMessageIsValidRequired);
            }
        })
    }

    /**
     * I'm trying to check if the checkbox is checked or not. If it's not checked, I want to display an
     * error message. If it's checked, I want to remove the error message.
     *
     * The problem is that the error message is not being displayed.
     *
     * I've tried to use the following code to check if the checkbox is checked or not
     */
    preSubmit() {
        const self = this
        $('.btn_withdraw').on('click', function (e) {
            const valid = $('#form').valid()
            if(!self.checked) {
                // $('.check_list-error').html(errorMessageIsValidRequired);
                $.confirm({
                    title: '',
                    content: messageCheckPlease,
                    buttons: {
                        ok: {
                            text: 'OK',
                            btnClass: 'btn-blue',
                            action: function () { }
                        }
                    }
                });
                e.preventDefault()
                return
            } else {
                $('.check_list-error').empty()
            }
            if(valid && self.checked) {
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: routePreConfirm,
                    method: 'POST',
                    data_type: 'json',
                    data: {
                        info_member_id: $('input[name=info_member_id]').val(),
                        password: $('input[name=password]').val(),
                        code: $('input[name=code]').val(),
                        reason_withdraw: $('textarea[name=reason_withdraw]').val(),
                        token: token
                    },
                    success: function(res) {
                        if(res && res.status) {
                            $.confirm({
                                title: '',
                                content: '本当に退会しますか？',
                                buttons: {
                                    ok: {
                                        text: '退会します',
                                        btnClass: 'btn-blue',
                                        action: function () {
                                            $('#form').submit()
                                        }
                                    },
                                    cancel: {
                                        text: 'キャンセル',
                                        btnClass: 'btn-default',
                                        action: function () { }
                                    }
                                }
                            })
                        } else {
                            $('#msg_box').removeClass('d-none')
                            $('#msg_error').text(res.message)
                        }
                    },
                    error: function(data) {}
                })
                // $('#form').submit()
            }
        })
    }

}

new clsU000Taikai02()