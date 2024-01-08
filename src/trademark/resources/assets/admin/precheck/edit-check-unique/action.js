$(document).ready(function () {
    data.forEach(element => {
        element['product'].forEach(item => {
            let idItem = item.id + '_' + item.m_distinction_id
            $("input#is_decision_draft_" + idItem).val(item.is_decision_draft ? item.is_decision_draft : 0)
            $("input#is_decision_edit_" + idItem).val(item.is_decision_edit ? item.is_decision_edit : 0)

            if(+$("input#is_decision_draft_" + idItem).val() === 1 && +$("input#is_decision_edit_" + idItem).val() === 0) {
                $('.result_edit_' + idItem).val(item.precheck_product[item.precheck_product.length - 1].precheck_result[0].result_identification_detail)
            } else if(+$("input#is_decision_draft_" + idItem).val() === 0 && +$("input#is_decision_edit_" + idItem).val() === 1) {
                $('.result_edit_' + idItem).val(item.result_identification_detail_edit)
            } else if(+$("input#is_decision_draft_" + idItem).val() === 0 && +$("input#is_decision_edit_" + idItem).val() === 1) {
                $('.result_edit_' + idItem).val($('.select_button_edit_copy_' + idItem).val())
            }

            this.itemCheckBox = $('input[data-foo="check_lock[]"]')
            const isCheckAll = this.itemCheckBox.length === $('input[data-foo="check_lock[]"]:checked').length;
            $('.checkbox_all').prop('checked', isCheckAll);

            $(document).on('click', '.add_' + item.id, function () {
                let productCode = [];
                item.code.forEach(function (itemCode) {
                    productCode.push(itemCode.name + ' ')
                })
                $('.icon-add-sub').removeClass('sub_' + item.id)
                $('.icon-add-sub').addClass('sub_' + item.id)
                $('.text-product-code_' + item.id).html(productCode)
                $('.icon-add-sub').html('-')
            })

            $(document).on('click', '.sub_' + item.id, function () {
                let productCode = [];
                item.code.forEach(function (itemCode, key) {
                    if (key < 3) {
                        productCode.push(itemCode.name + ' ')
                    }
                })
                $('.icon-add-sub').removeClass('sub_' + item.id)
                $('.icon-add-sub').addClass('add_' + item.id)
                $('.icon-add-sub').html('+')
                $('.text-product-code_' + item.id).html(productCode)
            })
            if (precheckPresent.is_confirm === isConfirmTrue) {
            $('.copy_undisabled').attr('disabled', true);
            $('.edit_copy').attr('disabled', true);
            $('.result_update').attr('disabled', true);
            $('.select_button_edit_copy').attr('disabled', true);
            $('.confirm_copy').attr('disabled', true);
            } else {
                if ($('.checkbox_' + idItem + ':checkbox:checked').length > 0) {
                    $('.checkbox_' + idItem).prop('checked', true);
                    $('.copy_undisabled_' + idItem).attr('disabled', true);
                    $('.edit_copy_' + idItem).attr('disabled', true);
                    $('.result_update_' + idItem).attr('disabled', true);
                    $('.select_button_edit_copy_' + idItem).attr('disabled', true);
                    $('.confirm_copy_' + idItem).attr('disabled', true);
                } else {
                    $('.checkbox_' + idItem).prop('checked', false);
                    $('.copy_undisabled_' + idItem).attr('disabled', false);
                    $('.edit_copy_' + idItem).attr('disabled', false);
                    $('.result_update_' + idItem).attr('disabled', false);
                    $('.select_button_edit_copy_' + idItem).attr('disabled', false);
                    $('.confirm_copy_' + idItem).attr('disabled', false);
                }
            }


            $(document).on('change', '.select_button_edit_copy_' + idItem, function () {
                    $('.result_edit_' + idItem).val($('.select_button_edit_copy_' + idItem).val())
                    $(this).closest('.select_button_edit').find('.result_identification_detail_edit').val( $(this).val())
                    $("input#is_decision_edit_" + idItem).val(0)
            })

            $(document).on('click', '.copy_undisabled_' + idItem, function () {
                $('select.select_button_edit_copy_' + idItem).removeAttr('disabled')
                $('select.select_button_edit_copy_' + idItem).val(item.precheck_product[item.precheck_product.length - 1].precheck_result[0].result_identification_detail)
                $('.result_identification_detail_edit_' + idItem).val(item.precheck_product[item.precheck_product.length - 1].precheck_result[0].result_identification_detail)
            })

            $(document).on('click', '.edit_copy_' + idItem, function () {
                if (+$('.result_identification_detail_present_'+idItem).val() === 1) {
                    $('.result_update_' + idItem).html('○')
                } else if (+$('.result_identification_detail_present_'+idItem).val() === 2) {
                    $('.result_update_' + idItem).html('△')
                } else if (+$('.result_identification_detail_present_'+idItem).val() === 3) {
                    $('.result_update_' + idItem).html('▲')
                } else if (+$('.result_identification_detail_present_'+idItem).val() === 4) {
                    $('.result_update_' + idItem).html('×')
                }
                $('.result_edit_' + idItem).val($('.result_identification_detail_present_'+idItem).val())
                $('input#result_identification_detail' + idItem).val($('.result_identification_detail_present_'+idItem).val())
                $("input#is_decision_draft_" + idItem).val(1)
                $("input#is_decision_edit_" + idItem).val(0)
                $('.keep_data_result_' + idItem).remove()
            })

            $(document).on('click', '.confirm_copy_' + idItem, function () {
                if (parseInt($('.select_button_edit_copy_' + idItem).val()) === 1) {
                    $('.result_update_' + idItem).html('○')
                } else if (parseInt($('.select_button_edit_copy_' + idItem).val()) === 2) {
                    $('.result_update_' + idItem).html('△')
                } else if (parseInt($('.select_button_edit_copy_' + idItem).val()) === 3) {
                    $('.result_update_' + idItem).html('▲')
                } else if (parseInt($('.select_button_edit_copy_' + idItem).val()) === 4) {
                    $('.result_update_' + idItem).html('×')
                }
                $('.result_edit_' + idItem).val(parseInt($('.select_button_edit_copy_' + idItem).val()))
                $('input#result_identification_detail' + idItem).val($('.result_edit_' + idItem).val())
                $("input#is_decision_edit_" + idItem).val(1)
                $("input#is_decision_draft_" + idItem).val(0)
                $('.keep_data_result_' + idItem).remove()

                $('.result_update_' + idItem).closest('.result_update').find('.error').remove()
            })

            $('.checkbox_' + idItem).on('change click', function () {
                this.itemCheckBox = $('input[data-foo="check_lock[]"]')
                const isCheckAll = this.itemCheckBox.length === $('input[data-foo="check_lock[]"]:checked').length;

                $('.checkbox_all').prop('checked', isCheckAll)
                if ($('.checkbox_' + idItem + ':checkbox:checked').length > 0) {
                    $('.checkbox_' + idItem).prop('checked', true);
                    $('.copy_undisabled_' + idItem).attr('disabled', true);
                    $('.edit_copy_' + idItem).attr('disabled', true);
                    $('.result_update_' + idItem).attr('disabled', true);
                    $('.select_button_edit_copy_' + idItem).attr('disabled', true);
                    $('.confirm_copy_' + idItem).attr('disabled', true);
                } else {
                    $('.checkbox_' + idItem).prop('checked', false);
                    $('.copy_undisabled_' + idItem).attr('disabled', false);
                    $('.edit_copy_' + idItem).attr('disabled', false);
                    $('.result_update_' + idItem).attr('disabled', false);
                    $('.select_button_edit_copy_' + idItem).attr('disabled', false);
                    $('.confirm_copy_' + idItem).attr('disabled', false);
                }

            })
        })
    });

    $(document).on('click', '.checkbox_all', function (event) {
        data.forEach(element => {
            element['product'].forEach(item => {
                let idItem = item.id + '_' + item.m_distinction_id

                $('.checkbox_' + idItem).not(this).prop('checked', this.checked);
                if ($('.checkbox_all:checkbox:checked').length > 0) {
                    $('.checkbox_' + idItem).prop('checked', true);
                    $('.copy_undisabled_' + idItem).attr('disabled', true);
                    $('.edit_copy_' + idItem).attr('disabled', true);
                    $('.result_update_' + idItem).attr('disabled', true);
                    $('.select_button_edit_copy_' + idItem).attr('disabled', true);
                    $('.confirm_copy_' + idItem).attr('disabled', true);
                    $('.checkbox_' + idItem).closest('.checkbox_parent').find('.error').remove()
                } else {
                    $('.checkbox_' + idItem).prop('checked', false);
                    $('.copy_undisabled_' + idItem).attr('disabled', false);
                    $('.edit_copy_' + idItem).attr('disabled', false);
                    $('.result_update_' + idItem).attr('disabled', false);
                    $('.select_button_edit_copy_' + idItem).attr('disabled', false);
                    $('.confirm_copy_' + idItem).attr('disabled', false);

                }

            })
        })
    })

    $(document).on('click', '.copy_undisabled_all', function () {
        data.forEach(element => {
            element['product'].forEach(item => {
                let idItem = item.id + '_' + item.m_distinction_id
                if ($('.checkbox_' + idItem + ':checkbox:checked').length <= 0) {
                    if (item.precheck_product[item.precheck_product.length - 1].is_register_product === 1) {
                        $('select.select_button_edit_copy_' + idItem).removeAttr('disabled')
                        $('select.select_button_edit_copy_' + idItem).val(item.precheck_product[item.precheck_product.length - 1].precheck_result[0].result_identification_detail)
                        $('.result_identification_detail_edit_' + idItem).val(item.precheck_product[item.precheck_product.length - 1].precheck_result[0].result_identification_detail)
                    }
                }
            })
        })
    })

    $(document).on('click', '.edit_copy_all', function () {
        let arr = [];
        let i = 0;
        data.forEach(element => {
            element['product'].forEach(item => {
                let idItem = item.id + '_' + item.m_distinction_id
                if ($('.checkbox_' + idItem + ':checkbox:checked').length <= 0) {
                    if (item.precheck_product[item.precheck_product.length - 1].is_register_product === 1) {
                        if(item.detailPresent && item.detailPresent[0]) {
                            if (item.detailPresent[0].result_identification_detail === 1) {
                                $('.result_update_' + idItem).html('○')
                            } else if (item.detailPresent[0].result_identification_detail === 2) {
                                $('.result_update_' + idItem).html('△')
                            } else if (item.detailPresent[0].result_identification_detail === 3) {
                                $('.result_update_' + idItem).html('▲')
                            } else if (item.detailPresent[0].result_identification_detail === 4) {
                                $('.result_update_' + idItem).html('×')
                            }
                        }

                        $('input#result_identification_detail' + idItem).val($('.result_identification_detail_present_'+idItem).val())
                        $('.result_edit_' + idItem).val($('.result_identification_detail_present_'+idItem).val())
                        $("input#is_decision_edit_" + idItem).val(0)
                        $("input#is_decision_draft_" + idItem).val(1)
                        $('.keep_data_result_' + idItem).remove()
                    }
                }
            })
        })
    });

    $('.confirm_copy_all').on('click', function () {
        data.forEach(element => {
            element['product'].forEach(item => {
                let idItem = item.id + '_' + item.m_distinction_id
                if ($('.checkbox_' + idItem + ':checkbox:checked').length <= 0) {
                    if (item.precheck_product[item.precheck_product.length - 1].is_register_product === 1) {
                        if (parseInt($('.select_button_edit_copy_' + idItem).val()) === 1) {
                            $('.result_update_' + idItem).html('○')
                        } else if (parseInt($('.select_button_edit_copy_' + idItem).val()) === 2) {
                            $('.result_update_' + idItem).html('△')
                        } else if (parseInt($('.select_button_edit_copy_' + idItem).val()) === 3) {
                            $('.result_update_' + idItem).html('▲')
                        } else if (parseInt($('.select_button_edit_copy_' + idItem).val()) === 4) {
                            $('.result_update_' + idItem).html('×')
                        } else {
                            $('.result_update_' + idItem).html('')
                        }
                        $('.result_edit_' + idItem).val($('.select_button_edit_copy_' + idItem).val())
                        $('input#result_identification_detail' + idItem).val($('.result_edit_' + idItem).val())
                        $("input#is_decision_edit_" + idItem).val(1)
                        $("input#is_decision_draft_" + idItem).val(0)
                        $('.keep_data_result_' + idItem).remove()

                        $('.result_update_' + idItem).closest('.result_update').find('.error').remove()
                    }
                }
            })
        })
    })

    $('input[type=checkbox]').on('click', function () {
        if($(this)[0].checked == true) {
            $(this).closest('.checkbox_parent').find('.error').remove()
        }
    })

    $('.confirm').on('click', function () {
        let checkbox = $('input[type=checkbox]').not('.checkbox_all')

        for(let i =0; i < checkbox.length; i++) {
            if($(checkbox[i])[0].checked == false) {
                $(checkbox[i]).closest('.checkbox_parent').find('.error').remove()
                $(checkbox[i]).closest('.checkbox_parent').find('.text-checked').after('<div class="error mb-2">' + errorE0025 + '</div>')
            }
        }

        let resultUpdate = $('.result_update_data')
        for(let i =0; i < resultUpdate.length; i++) {
            $(resultUpdate[i]).closest('.result_update').find('.error').remove()
            if($(resultUpdate[i])[0].innerText == '') {
                $(resultUpdate[i]).closest('.result_update').find('.result_update_data').after('<div class="error mb-2">' + errorE001 + '</div>')
            }
        }

        if($('.checkbox_all')[0].checked == false) {
            document.querySelector('.error').parentNode.scrollIntoView({
                behavior: 'smooth',
            });
            return false
        }
    })
})
