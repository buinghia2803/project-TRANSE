class clsHajimeSupportCustomer {
    constructor() {
        const self = this
        this.clsCart = new clsCartProduct()
        window.addEventListener('load', function () {
            self.doLoad();
        })
    }

    /**
    * Do load all action and element.
    */
    doLoad() {
        this.initDefault()
        this.closeModal()
        this.onChangeProduct()
        this.onChangeCheckboxAll()
        this.onChangeCheckboxSingle()
    }

    /**
     * Close modal.
     */
    closeModal() {
        $('#close-modal').on('click', function () {
            $('#mySizeChartModal').find('span.ebcf_close').trigger('click')
        })
    }

    /**
     * Handling change event of product input.
     */
    onChangeProduct() {
        $('.m_product_ids').on('change', function () {
            const totalChoose = $('input[name="m_product_ids[]"]:checked').length
            $('#total_choose').text(totalChoose)
        })
    }

    /**
     * Handling change event checkbox all of class
     */
    onChangeCheckboxAll() {
        const self = this
        $('.checkAllCheckBox').on('change', function () {
            const status = $(this).is(':checked')

            $('input[name="m_product_ids[]"]').each(function (key, item) {
                $(item).attr('checked', status)
            })

            self.setAllCheckBoxDefault()
        })
    }

    //Set check all default
    setAllCheckBoxDefault() {
        if ($('.single-checkbox:checked').length == $('.single-checkbox').length) {
            $('#check-all').prop('checked', true);
        } else {
            $('#check-all').prop('checked', false);
        }

        $('#error-msg').empty();
        if(!$('input[class*=is_choice_user_]:checked').length){
            if(!$('#error-msg').find('.error').length) {
                $('#error-msg').append('<div class="error mb15">選択してください。</div>')
            }
        }
    }

    onChangeCheckboxSingle() {
        const sef = this
        $('body').on('click', '.single-checkbox', function() {
            sef.setAllCheckBoxDefault()
        });
    }

    /**
     * Send Request ajax to server.
     */
    sendRequestAjax(data) {
        const dataForm = {}
        $('#contents form').find('input[type=text], input[type=checkbox]:checked, input[type=radio]:checked, input[type=hidden],  select, checkbox').each(function (key, item) {
            if (item.getAttribute('name') !== 'mDistrintions[]') {
                dataForm[item.getAttribute('name')] = $(item).val()
            }
        })
        //set product is checked
        dataForm.productIdsChecked = data.productIds
        const params = { ...dataForm, ...data }
        loadAjaxPost(urlSubmit, params, {
            beforeSend: function () { },
            success: function (result) {
                window.location.href = result
            },
            error: function (error) {
            }
        }, 'loading');
    }

    /**
     * Init default variable
     */
    initDefault() {
        const totalChoose = $('input[name="m_product_ids[]"]:checked').length
        $('#total_choose').text(totalChoose)

        var obj = {};
        const self = this
        $('body').on('click', '#register_precheck',function (e) {
            // if registered trademark with image then can't go to precheck
            if (trademark.type_trademark == REGISTER_TRADEMARK_TEXT) {
                // $(this).prop('href', routeRegisterPrecheck);
                const arrayProductSelect = []
                const isChoiUser = []
                const data = {
                    redirect_to: redirectEntry.u021,
                    id: trademark_id,
                    productIds: arrayProductSelect,
                    isChoiUser
                }
                $('input[name*=is_choice_user_]:checked').each(function (idx, item) {
                    arrayProductSelect.push($(item).data('product_id'))
                })
                data.productIds = arrayProductSelect;

                let arrayProduct = [];
                $('input[name*=is_choice_user_]').each(function (idx, item) {
                    arrayProduct.push({
                        'id': $(this).data('product_id'),
                        'is_apply': $(this).prop('checked'),
                    });
                })
                data.products = arrayProduct;

                if(!$('input[class*=is_choice_user_]:checked').length){
                    if(!$('#error-msg').find('.notice').length) {
                        $('#error-msg').append('<div class="notice mb15">選択してください。</div>')
                    }
                    e.stopPropagation();
                    e.preventDefault();
                    document.querySelector('.js-scrollable').scrollIntoView({
                        behavior: 'smooth'
                    });

                    return
                }
                self.sendRequestAjax(data)
            } else {
                $.confirm({
                    title: '',
                    content: errorMessageRegisterPrecheck,
                    buttons: {
                        cancel: {
                            text: 'OK',
                            btnClass: 'btn-default',
                            action: function () { }
                        },
                    }
                });
            }
        })


        // To do , Redirect to u032_cancel
        $('#stop_applying').on('click', function () {
            $(this).prop('href', routeCancel);
        })

        // Ajax Send Session
        $('.rollback_suggest_ai').on('click', function (e) {
            const arrayProductSelect = []
            const isChoiUser = []
            const data = {
                redirect_to: redirectEntry.u020b,
                id: sft_id,
                productIds: arrayProductSelect,
                isChoiUser
            }
            $('input[name*=is_choice_user_]:checked').each(function (idx, item) {
                arrayProductSelect.push($(item).data('product_id'))
            })
            data.productIds = arrayProductSelect;
            if(!$('input[class*=is_choice_user_]:checked').length){
                if(!$('#error-msg').find('.notice').length) {
                    $('#error-msg').append('<div class="notice mb15">選択してください。</div>')
                }
                e.stopPropagation();
                e.preventDefault();
                document.querySelector('.js-scrollable').scrollIntoView({
                    behavior: 'smooth'
                });

                return
            }
            self.sendRequestAjax(data)
        })
        $('#redirect_apply_trademark').on('click', function (e) {
            const arrayProductSelect = []
            const isChoiUser = []
            const data = {
                redirect_to: confirmRedirectToApplyTradermark,
                id: sft_id,
                productIds: arrayProductSelect,
                isChoiUser
            }

            $('input[name*=is_choice_user_]:checked').each(function (idx, item) {
                arrayProductSelect.push($(item).data('product_id'))
            })

            data.productIds = arrayProductSelect;
            if(!$('input[class*=is_choice_user_]:checked').length){
                if(!$('#error-msg').find('.notice').length) {
                    $('#error-msg').append('<div class="notice mb15">選択してください。</div>')
                }
                e.stopPropagation();
                e.preventDefault();

                document.querySelector('.js-scrollable').scrollIntoView({
                    behavior: 'smooth'
                });

                return
            }
            self.sendRequestAjax(data)
        })

        $('#redirect_to_precheck').on('click', function (e) {
            const arrayProductSelect = []
            const isChoiUser = []
            const data = {
                redirect_to: redirectEntry.u021c,
                id: trademark_id,
                productIds: arrayProductSelect,
                isChoiUser
            }
            $('input[name*=is_choice_user_]:checked').each(function (idx, item) {
                arrayProductSelect.push($(item).data('product_id'))
            })

            data.productIds = arrayProductSelect;
            if(!$('input[class*=is_choice_user_]:checked').length){
                if(!$('#error-msg').find('.notice').length) {
                    $('#error-msg').append('<div class="notice mb15">選択してください。</div>')
                }
                e.stopPropagation();
                e.preventDefault();
                document.querySelector('.js-scrollable').scrollIntoView({
                    behavior: 'smooth'
                });

                return
            }
            self.sendRequestAjax(data)
        })
        $('#redirect_to_u031pass').on('click', function () {
            arrayProductSelect = []
            isChoiUser = []
            openModal('#u031pass-modal');
        })

        $('#redirec_to_anken_top').on('click', function (e) {
            triggerChangeInput()
            const arrayProductSelect = []
            const isChoiUser = []
            const data = {
                redirect_to: redirectEntry.anken_top,
                id: sft_id,
                productIds: arrayProductSelect,
                isChoiUser
            }

            $('input[name*=is_choice_user_]:checked').each(function (idx, item) {
                arrayProductSelect.push($(item).data('product_id'))
            })

            data.productIds = arrayProductSelect;
            $('#error-msg').empty();
            if(!$('input[class*=is_choice_user_]:checked').length){
                if(!$('#error-msg').find('.error').length) {
                    $('#error-msg').append('<div class="error mb15">選択してください。</div>')
                }
            }

            let form = $('#form');
            form.valid();
            let hasError = form.find('.error-validate:visible,.notice:visible,.error:visible');
            if (hasError.length == 0 && form.valid()) {
                self.sendRequestAjax(data);
            } else {
                let firstError = hasError.first();
                scrollToElement(firstError, -100);
                return false;
            }
        })

        $('#redirect_to_quote').on('click', function (e) {
            triggerChangeInput()
            $('#form input[name=redirect_to]').attr('value', redirectEntry.quote)
            $('#form input[name=redirect_to]').val(redirectEntry.quote)

            $('#error-msg').empty();
            if(!$('input[class*=is_choice_user_]:checked').length){
                if(!$('#error-msg').find('.error').length) {
                    $('#error-msg').append('<div class="error mb15">選択してください。</div>')
                }
            }

            let form = $('#form');
            form.valid();
            let hasError = form.find('.error-validate:visible,.notice:visible,.error:visible');
            if (hasError.length == 0 && form.valid()) {
                $('#form').attr('target' ,'_blank');
                $('#form').submit()
                loadingBox('close');
                $('#form').attr('target' ,'_self');
            } else {
                let firstError = hasError.first();
                scrollToElement(firstError, -100);
                return false;
            }
        })

        $('.redirect_to_common_payment').on('click', function (e) {
            triggerChangeInput()
            $('#form input[name=redirect_to]').attr('value', redirectEntry.common_payment)
            $('#form input[name=redirect_to]').val(redirectEntry.common_payment)

            $('#error-msg').empty();
            if(!$('input[class*=is_choice_user_]:checked').length){
                if(!$('#error-msg').find('.error').length) {
                    $('#error-msg').append('<div class="error mb15">選択してください。</div>')
                }
            }

            let form = $('#form');
            form.valid();
            let hasError = form.find('.error-validate:visible,.notice:visible,.error:visible');
            if (hasError.length == 0 && form.valid()) {
                form.submit();
            } else {
                let firstError = hasError.first();
                scrollToElement(firstError, -100);
                return false;
            }
        })

        $('#addProduct').on('click', function () {
            const route = $(this).data('route');
            const arrayProductSelect = []

            $('input[name*=is_choice_user_]:checked').each(function (idx, item) {
                arrayProductSelect.push({
                    'id': $(item).data('product_id'),
                    'is_choice_user': $(item).prop('checked'),
                })
            })

            if(!arrayProductSelect.length){
                if(!$('#error-msg').find('.notice').length) {
                    $('#error-msg').append('<div class="notice mb15">選択してください。</div>')
                }
                e.stopPropagation();
                e.preventDefault();
                document.querySelector('.js-scrollable').scrollIntoView({
                    behavior: 'smooth'
                });

                return
            }

            if($('#form .error').length) {
                document.querySelector('.error').scrollIntoView({
                    behavior: 'smooth'
                });
            }

            if($('.notice, div[id*="-error"]').not('.d-none').length) {
                $('.notice, div[id*="-error"]').not('.d-none')[0].scrollIntoView({
                    behavior: 'smooth'
                });
            }

            $.confirm({
                title: '',
                content: labelModal,
                buttons: {
                    cancel: {
                        text: NO,
                        btnClass: 'btn-default',
                        action: function () {
                        }
                    },
                    ok: {
                        text: YES,
                        btnClass: 'btn-blue',
                        action: function () {
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                url: route,
                                method: 'POST',
                                data_type: 'json',
                                data: {
                                    m_product_ids: arrayProductSelect,
                                    trademark_id: trademark_id,
                                    sft_id: sft_id,
                                },
                            }).done(function(res) {
                                if(res?.status) {
                                    window.location.href = res.router_redirect
                                }
                            });
                        }
                    }
                }
            });
        });
    }
}
var classHajimeSupportCustomer = new clsHajimeSupportCustomer()


