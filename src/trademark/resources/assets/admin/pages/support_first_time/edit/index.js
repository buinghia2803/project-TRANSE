$(document).ready(function () {
    //COPY ALL =====
    let flugErrorProd = false
    let flugErrorCode = false
    let flugSftContentProd = false
    let errorsProd = []
    let errorsCode = []
    let regexHalfWidth = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９　～！＠＃＄％＾＆＊（）＿＋ー＝￥」「；’・。、＜＞？”：｝｛｜]+$/;

    checkStatusInputCheckAll();
    disabledButtonAll();
    disabledButtonSingle()

    //Validation form table =====================================
    validationForm()

    function validSftContentProduct() {
        flugSftContentProd = false;
        if ($('.stf_content_product_ids:checked').length <= 0 || ($('.stf_content_product_ids').length > $('.stf_content_product_ids:checked').length)) {
            flugSftContentProd = true
        }
        if(flugSftContentProd) {
            $('.errorRequiredSftContentProd').html(`<div class="error" style="font-size: 14px">${errorPleaseChooseProduct}</div>`)
            $([document.documentElement, document.body]).animate({
                scrollTop: $("#contents").offset().top
            }, 1000);
        }
    }

   // submit form
    $(document).on('click', 'input[type="submit"]', function () {
        if($(this).hasClass('saveDraft')) {
            //save data and show quoutes
            $('#code-button').val('')
        } else if($(this).hasClass('saveNotice')) {
            //save data and go to anken top
            $('#code-button').val(notice)
            validSftContentProduct()
            if(flugSftContentProd) {
                return false;
            }
        }

        validationForm()
        if (errorsProd.length || errorsCode.length) {
            return false
        } else if($(this).hasClass('saveNotice')) {
            //check is_decision when submit to user
            let dataNotIsDecision = [];
            let dataNotCheckedIsBlock = [];
            $('.row-table-item').each(function(indx, item) {
                if(!$(item).find('.delete_item_hide').is(':checked')) {
                    let prodData = $(item).find('.m_product_name_decision').val()
                    let distinctionData = $(item).find('input[class*=m_distinction_decision_]').val()
                    let isDecision = $(item).find('.is_decision').val()
                    if(prodData == '' || distinctionData == '' || isDecision == 0) {
                        dataNotIsDecision.push(indx)
                    }
                    if(!$(item).find('.is_block').is(':checked')) {
                        dataNotCheckedIsBlock.push(indx)
                    }
                }
            });

            if(dataNotIsDecision.length > 0) {
                $.confirm({
                    title: '',
                    content: errorNotIsDecisionAllData,
                    buttons: {
                        cancel: {
                            text: confirmText,
                            btnClass: 'btn-default',
                            action: function () {
                                scrollToElement($('.product-table').first());
                            }
                        },
                    }
                });
                return false
            }

            if(dataNotCheckedIsBlock.length > 0) {
                $.confirm({
                    title: '',
                    content: errorNotCheckAllIsBlock,
                    buttons: {
                        cancel: {
                            text: '確認',
                            btnClass: 'btn-default',
                            action: function () {
                                scrollToElement($('.product-table').first());
                            }
                        },
                    }
                });
                return false
            }
            $('#form').submit()
        }
    });

    function validationForm() {
        //validate product
        $('body').on('focusout focusin', 'input[class*=m_product_name_edit_]', function() {
            let regex =  /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９　～！＠＃＄％＾＆＊（）＿＋ー＝￥」「；’・。、＜＞？”：｝｛｜]+$/;
            let value = $(this).val()
            let index = $(this).attr('key-prod')
            flugErrorProd = false
            if ($(this).val().length) {
                if (value.length > 200) {
                    $(this).next().filter('.error-validate').remove()
                    $(this).after(`<div class="error-validate">${errorMessageFormatProduct}</div>`)
                    flugErrorProd = true
                    errorsProd[index] = value

                    //disabled button in row
                    $(`.row-table-item-${index}`).find('input[type=button]').each(function (idx, el) {
                        $(el).addClass('pointer-events-none')
                    });

                    disabledRowByIndex(index)
                } else if (!regex.test(value)) {
                    $(this).next().filter('.error-validate').remove()
                    $(this).after(`<div class="error-validate">${errorMessageFormatProduct}</div>`)
                    flugErrorProd = true
                    errorsProd[index] = value

                    //disabled button in row
                    $(`.row-table-item-${index}`).find('input[type=button]').each(function (idx, el) {
                        $(el).addClass('pointer-events-none')
                    });
                    disabledRowByIndex(index)
                } else {
                    const error = $(this).closest('.boxes').find('.error-validate')
                    if (error.length) {
                        error.remove()
                    }
                    errorsProd.splice(index, 1);

                    //disabled button in row
                    $(`.row-table-item-${index}`).find('input[type=button]').each(function (idx, el) {
                        $(el).removeClass('pointer-events-none')
                    });
                    disabledRowByIndex(index)
                }
            }
        })

        //validate code
        $('body').on('focusout focusin', 'input[class*=prod_code]', function() {
            let value = $(this).val()
            let index = $(this).data('index')
            let indexCo = parseInt($(this).data('index-code'))
            if ($(this).val().length) {
                if (value.length != 5) {
                    $(this).next().filter('.error-validate').remove()
                    $(this).after(`<div class="error-validate">${errorMessageFormatCode}</div>`)
                    errorsCode.push({ index: indexCo, value: value })
                    flugErrorCode = true

                    //disabled button in row
                    $(`.row-table-item-${index}`).find('input[type=button]').each(function (idx, el) {
                        $(el).addClass('pointer-events-none')
                    });

                } else if (!isValidProdCode(value) && value !== '') {
                    $(this).next().filter('.error-validate').remove()
                    $(this).after(`<div class="error-validate">${errorMessageFormatCode}</div>`)
                    errorsCode.push({ index: indexCo, value: value })
                    flugErrorCode = true

                    //disabled button in row
                    $(`.row-table-item-${index}`).find('input[type=button]').each(function (idx, el) {
                        $(el).addClass('pointer-events-none')
                    });
                } else {
                    flugErrorCode = false
                    errorsCode = errorsCode.filter(function(item) {
                        return item.index !== indexCo;
                    });
                    $(this).next().filter('.error-validate').remove()

                    //disabled button in row
                    $(`.row-table-item-${index}`).find('input[type=button]').each(function (idx, el) {
                        $(el).removeClass('pointer-events-none')
                    });
                }
            } else {
                flugErrorCode = false
                errorsCode = errorsCode.filter(function(item) {
                    return item.index !== indexCo;
                });
                $(this).next().filter('.error-validate').remove()

                //disabled button in row
                $(`.row-table-item-${index}`).find('input[type=button]').each(function (idx, el) {
                    $(el).removeClass('pointer-events-none')
                });
            }
        })
    }

    //1.copy data from draft to edit
    $(document).on('click', '.copyFromDraftToEdit', function () {
        $('.row-table-item').each((index, item) => {
            if (!$('.is_block_' + index).is(':checked')) {
                 //1.m_product draft to edit
                //row exists suitable_product data
                if($('.label_m_product_name_' + index).length) {
                    let txt_prod = $('.label_m_product_name_' + index).text()
                    $('.m_product_name_edit_' + index).val(txt_prod);
                    $('.m_product_id_edit_' + index).val($('.m_product_id_draft_' + index).val());
                    $('.m_product_name_edit_' + index).removeAttr('data-suggest');
                    $('.m_product_name_edit_' + index).attr('readonly', true);
                }

                //2.m_distinction draft to edit
                if($('.label_m_distinction_draft_' + index).length) {
                    let txt_distinction_draft = $('.label_m_distinction_draft_' + index).text()
                    $('.label_m_distinction_edit_' + index).text('').text(txt_distinction_draft);
                    $('input[type=hidden].m_distinction_edit_' + index).val($('input[type=hidden].m_distinction_draft_' + index).val())
                    $(".m_distinction_edit_" + index + " option").filter(function () {
                        return $(this).text() == txt_distinction_draft;
                    }).prop("selected", true);
                }

                //3.m_code draft to edit
                $('.label_m_code_edit_' + index).html($('.label_m_code_draft_' + index).html());

                //copy code input type = 1,2
                if($('.m_code_draft_' + index).length) {
                    $('.m_code_edit_' + index).val($('.m_code_draft_' + index).val());
                }

                //m_code if type = 3
                if($('.m_code_draft_' + index).length > 0) {
                    let codes = $('.m_code_draft_' + index).val().split(',');
                    if (codes.length > 0) {
                        $('.row-table-item-' + index + ' .prod_code_old_data').remove()
                        codes.map((value, key) => {
                            $('.row-table-item-' + index).find('[data-code-list]').append(`
                        <input type="text" class="em18 prod_code prod_code_old_data prod_code_old_data_${index}" value="${value}" data-code name="data[${index}][data_edit][code][${key}][name]"/>
                    `)
                        })
                    }
                }
            }
        });
    });

    //2.copy data from draft to decision
    $(document).on('click', '.copyFromDraftToDecision', function () {
        $('.row-table-item').each((index, item) => {
            if (!$('.is_block_' + index).is(':checked')) {
                if($('.m_product_name_draft_' + index).length) {
                    //Set value input hidden is_decision = 1
                    $('.is_decision_'+index).val(draftIsDecision)
                    $('.label_m_product_name_decision_' + index).text($('.label_m_product_name_' + index).text());
                    $('.m_product_name_decision_' + index).val($('.m_product_name_draft_' + index).val());
                    $('.m_product_id_decision_' + index).val($('.m_product_id_draft_' + index).val());
                }

                if($('.label_m_distinction_draft_' + index).length) {
                    $('.m_distinction_decision_' + index).val('').val($('.m_distinction_draft_' + index).val());
                    $('.label_m_distinction_decision_' + index).text('').text($('.label_m_distinction_draft_' + index).text());
                }
                //m_code
                if($('.m_code_draft_'+index).length) {
                    $('.m_code_decision_' + index).val($('.m_code_draft_'+index).val());
                    $('.label_m_code_decision_' + index).html($('.label_m_code_draft_' + index).html());
                }
            }
        });
    });

    //3.copy data from edit to decision
    $(document).on('click', '.copyFromEditToDecision', function () {
        //Set value input hidden is_decision_edit = 2
        $('.is_decision').val(editIsDecision)
        $('.row-table-item').each((index, item) => {
            $(item).find('.input_product_name').next().filter('.error-validate').remove()
            if (!$('.is_block_' + index).is(':checked')) {
                //1.m_product
                //if type
                let valueProd = $('.m_product_name_edit_' + index).val()
                let prodId = $('.m_product_id_edit_' + index).val()

                if (valueProd && valueProd != '') {
                    if (valueProd.length > 200) {
                        $(item).find('.input_product_name').after(`<div class="error-validate">${errorMessageFormatProduct}</div>`)
                        return
                    } else if (!regexHalfWidth.test(valueProd)) {
                        $(item).find('.input_product_name').after(`<div class="error-validate">${errorMessageFormatProduct}</div>`)
                        return
                    } else {
                        $('.label_m_product_name_decision_' + index).text(valueProd);
                        $('.m_product_name_decision_' + index).val(valueProd);
                        $('.m_product_id_decision_' + index).val(prodId);
                    }
                }

                //2.m_distinction
                $('.label_m_distinction_decision_' + index).text('').text($('.label_m_distinction_edit_' + index).text());
                $('.m_distinction_decision_' + index).val($('.label_m_distinction_edit_' + index).text());

                //m_distinction if type = 3
                let val_dis = $('.m_distinction_edit_' + index + ' option:selected').text()
                $('.label_m_distinction_decision_' + index + '.type_bg_yellow').text(val_dis)
                $('.m_distinction_decision_' + index + '.type_bg_yellow').val(val_dis)

                //3.m_code
                let codes = []
                $('.label_m_code_edit_' + index + ' span').map((index, el) => {
                    let txt = $(el).text()
                    if (codes.indexOf(txt) == -1) {
                        codes.push(txt)
                    }
                });

                // $('.m_code_decision_' + index).val(codes.join(','));
                $('.m_code_decision_' + index).val($(`.m_code_edit_${index}`).val());
                $('.label_m_code_decision_' + index).html($('.label_m_code_edit_' + index).html());

                //m_code if type = 3
                let code_type_3 = []
                $('.row-table-item-' + index + ' .data-code-list' + ' .prod_code').map((k, el) => {
                    let txt = $(el).val()
                    if(txt && txt != '') {
                        //validate to copy
                        if (txt.length != 5) {
                            return
                        } else if (!isValidProdCode(txt)) {
                            return
                        }
                        if (code_type_3.indexOf(txt) == -1) {
                            code_type_3.push(txt)
                        }
                    }
                });
                if (code_type_3.length > 0) {
                    $('.m_code_decision_' + index).val(code_type_3.join(','));
                    $('.label_m_code_decision_' + index).text(code_type_3.join(' '));
                }
            }
        });
    });

    //COPY SINGLE -----------
    //1.Copy single data from draft to edit
    $(document).on('click', '.copySingleDraftToEdit', function () {
        let index = $(this).data('index')
        if (!$('.is_block_' + index).is(':checked')) {
            let newValue = $('.label_m_product_name_' + index).text()
            if (newValue == '') {
                return false
            }
            $('.m_product_name_edit_' + index).val(newValue);
            $('.m_product_id_edit_' + index).val($('.m_product_id_draft_' + index).val());
            $('.m_product_name_edit_' + index).removeAttr('data-suggest');
            $('.m_product_name_edit_' + index).attr('readonly', true);

            $(".m_product_name_edit_" + index + " option").filter(function () {
                return $(this).text() == newValue;
            }).prop("selected", true);

            //distinction_edit
            let txt_distinction_draft = $('.label_m_distinction_draft_'+index).text()
            $('.label_m_distinction_edit_'+index).text(txt_distinction_draft);
            $('.m_distinction_edit_'+index).val($('.m_distinction_draft_'+index).val());
            //distinction_edit- type = 3
            $(".m_distinction_edit_" + index + " option").filter(function () {
                return $(this).text() == txt_distinction_draft;
            }).prop("selected", true);

            //m_code_edit
            $('.label_m_code_edit_'+index).text('').text($('.label_m_code_draft_'+index).text());

            $(".m_distinction_edit_" + index + " option").filter(function () {
                return $(this).text() == txt_distinction_draft;
            }).prop("selected", true);
            //m_code
            $('.label_m_code_edit_' + index).html($('.label_m_code_draft_' + index).html());

            //input hidden m_code
            //copy code input type = 1,2
            $('.m_code_edit_' + index).val($('.m_code_draft_' + index).val());

            //m_code if type = 3
            let codes = $('.m_code_draft_' + index).val().split(',');
            if (codes.length > 0) {
                $('.row-table-item-' + index + ' .prod_code_old_data').remove()
                codes.map((value, key) => {
                    $('.row-table-item-' + index).find('[data-code-list]').append(`
                        <input type="text" class="em18 prod_code prod_code_old_data prod_code_old_data_${index}" value="${value}" data-code name="data[${index}][data_edit][code][${key}][name]"/>
                    `)
                })
            }
        }
    });

    //2.Copy single data from draft to decision
    $('.copySingleDraftToDecision').on('click', function () {
        let index = $(this).data('index')
        //Set value input hidden is_decision = 1
        $('.is_decision_'+index).val(draftIsDecision)

        if (!$('.is_block_' + index).is(':checked')) {
            let newValue = $('.label_m_product_name_' + index).text()
            if (newValue == '') {
                return false
            }
            //validate product
            $('.label_m_product_name_decision_' + index).text(newValue);
            $('.m_product_name_decision_' + index).val(newValue);
            $('.m_product_id_decision_' + index).val($('.m_product_id_draft_'+index).val());

            //m_distinction_decision
            $('.label_m_distinction_decision_'+index).text($('.label_m_distinction_draft_'+index).text());
            $('.m_distinction_decision_'+index).val($('.m_distinction_draft_'+index).val());

            //m_code_decision
            $('.label_m_code_decision_'+index).text($('.label_m_code_draft_'+index).text());
            $('.m_code_decision_'+index).val($('.m_code_draft_'+index).val());
        }
    });

    //3.Copy single data from edit to decision
    $(document).on('click', '.copySingleEditToDecision', function () {
        let index = $(this).data('index')
        //Set value input hidden is_decision_edit = 2
        $('.is_decision_'+index).val(editIsDecision)

        if (!$('.is_block_' + index).is(':checked')) {
            let newValue = $('.m_product_name_edit_' + index).val()
            let prodIdEdit = $('.m_product_id_edit_' + index).val()
            $('.label_m_product_name_decision_' + index).text(newValue);
            $('.m_product_name_decision_' + index).val(newValue);
            $('.m_product_id_decision_' + index).val(prodIdEdit);

            //m_distinction_decision
            $('.label_m_distinction_decision_'+index).text($('.label_m_distinction_edit_'+index).text());
            $('.m_distinction_decision_'+index).val($('input.m_distinction_edit_'+index).val());
            //m_distinction_decision type = 3
            let valDis = $('.m_distinction_edit_' + index + ' option:selected').text()
            $('.label_m_distinction_decision_' + index + '.type_bg_yellow').text(valDis)
            $('.m_distinction_decision_' + index + '.type_bg_yellow').val(valDis)

            //3.m_code
            $('.label_m_code_decision_'+index).text($('.label_m_code_edit_'+index).text());
            $('.m_code_decision_'+index).val($('.m_code_edit_'+index).val());

            //m_code if type = 3
            let code_type_3 = []
            $('.row-table-item-' + index + ' .data-code-list' + ' .prod_code').map((k, el) => {
                let txt = $(el).val()
                if(txt && txt != '') {
                    //validate to copy
                    if (txt.length != 5) {
                        return
                    } else if (!isValidProdCode(txt)) {
                        return
                    }
                    if (code_type_3.indexOf(txt) == -1) {
                        code_type_3.push(txt)
                    }
                }
            });
            if (code_type_3.length > 0) {
                $('.m_code_decision_' + index).val(code_type_3.join(','));
                $('.label_m_code_decision_' + index).text(code_type_3.join(' '));
            }
        }
    });



    // check all is_block checkAllIsBlock
    $(document).on('click', '.checkAllIsBlock', function () {
        if ($(this).is(':checked')) {
            let dataChecked = [];
            $('.is_block').each(function (index, item) {
                $(item).prop('checked', true)
                $(item).val(isBlockConst)
                if($(item).is('checked')) {
                    dataChecked.push($(item).attr('data-id'))
                }
            });
        } else {
            $('.is_block').prop('checked', false)
            let dataNotChecked = [];
            $('.is_block:not(:checked)').each(function (index, item) {
                dataNotChecked.push($(item).attr('data-id'))
            });
        }
        disabledButtonAll()
    });

    //check single is_block
    $(document).on('click', '.is_block', function () {
        if (($('.is_block').length > 0) && $('.is_block').length == $(".is_block:checked").length) {
            $('.checkAllIsBlock').prop('checked', true)
        } else {
            $('.checkAllIsBlock').prop('checked', false)
        }

        if ($(this).is(':checked')) {
            $(this).val(isBlockConst)
            let dataChecked = [];
            $('.is_block:checked').each(function (index, item) {
                dataChecked.push($(item).attr('data-id'))
            });
        } else {
            $(this).val(notIsBlockConst)
            let dataNotChecked = [];
            $('.is_block:not(:checked)').each(function (index, item) {
                dataNotChecked.push($(item).attr('data-id'))
            });
        }
        disabledButtonAll()
        disabledButtonSingle()
    });

    //ajax update is block
    function ajaxUpdateIsBlock(data) {
        if (!flugSftKeepData) {
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: routeUpdateIsBlockAjax,
                method: 'POST',
                data_type: 'json',
                data: data
            }).done(function (data) {
                if (data?.status) {
                    checkStatusInputCheckAll();
                    disabledButtonAll()
                    disabledButtonSingle()
                }
            })
        }
    }

    //check status check of input check all
    function checkStatusInputCheckAll() {
        if (($('.is_block').length > 0) && $('.is_block').length == $(".is_block:checked").length) {
            $('.checkAllIsBlock').prop('checked', true)
        } else {
            $('.checkAllIsBlock').prop('checked', false)
        }
    }

    //disabled all button
    function disabledButtonAll() {
        if ($('.checkAllIsBlock').is(':checked')) {
            //disabled all button: copyFromDraftToEdit, copyFromDraftToDecision, copyFromEditToDecision
            $('.disabledAllButton').prop('disabled', true).css('cursor', 'not-allowed')
            $('.add_code_old_data').prop('disabled', true).css('cursor', 'not-allowed')
            $('.deleteItem').prop('disabled', true).css('cursor', 'not-allowed')
            $('.hideItem').prop('disabled', true).css('cursor', 'not-allowed')
            $('.prod_code_old_data').prop('readonly', true)
            $('select').addClass('pointer-events-none');
            $('input[type=text]').addClass('pointer-events-none');
        } else {
            $('.disabledAllButton').prop('disabled', false).css('cursor', 'pointer');
            $('.add_code_old_data').prop('disabled', false).css('cursor', 'pointer');
            $('.deleteItem').prop('disabled', false).css('cursor', 'pointer');
            $('.hideItem').prop('readonly', false).css('cursor', 'pointer');
            $('.prod_code_old_data').prop('readonly', false)
            $('select').removeClass('pointer-events-none');
            $('input[type=text]').removeClass('pointer-events-none');
        }
    }

    //disabled single button
    function disabledButtonSingle() {
        $('.row-table-item').each((index, item) => {
            if ($(item).find('.is_block').is(':checked')) {
                $(item).find('.disabled_btn_single_' + index).prop('disabled', true).css('cursor', 'not-allowed');
                $(item).find('.add_code_old_data').prop('disabled', true).css('cursor', 'not-allowed');
                $(item).find('.deleteItem').prop('disabled', true).css('cursor', 'not-allowed');
                $(item).find('input[type=button]').prop('disabled', true);
                $(item).find('button[type=button]').addClass('pointer-events-none');
                $(item).find('input[type=text]').addClass('pointer-events-none');
                $(item).find('select').addClass('pointer-events-none');
            } else {
                $(item).find('.disabled_btn_single_' + index).prop('disabled', false).css('cursor', 'pointer');
                $(item).find('.add_code_old_data').prop('disabled', false).css('cursor', 'pointer');
                $(item).find('.deleteItem').prop('disabled', false).css('cursor', 'pointer');
                $(item).find('input[type=button]').prop('disabled', false);
                $(item).find('button[type=button]').removeClass('pointer-events-none');
                $(item).find('input[type=text]').removeClass('pointer-events-none');
                $(item).find('select').removeClass('pointer-events-none');
            }
        });
    }

    function disabledRowByIndex(index) {
        let item = $('.row-table-item-'+index)
        if ($(item).find('.is_block').is(':checked')) {
            $(item).find('.disabled_btn_single_' + index).prop('disabled', true).css('cursor', 'not-allowed');
            $(item).find('.add_code_old_data').prop('disabled', true).css('cursor', 'not-allowed');
            $(item).find('.deleteItem').prop('disabled', true).css('cursor', 'not-allowed');
            $(item).find('input[type=button]').prop('disabled', true);
            $(item).find('button[type=button]').addClass('pointer-events-none');
            $(item).find('input[type=text]').addClass('pointer-events-none');
            $(item).find('select').addClass('pointer-events-none');
        } else {
            $(item).find('.disabled_btn_single_' + index).prop('disabled', false).css('cursor', 'pointer');
            $(item).find('.add_code_old_data').prop('disabled', false).css('cursor', 'pointer');
            $(item).find('.deleteItem').prop('disabled', false).css('cursor', 'pointer');
            $(item).find('input[type=button]').prop('disabled', false);
            $(item).find('button[type=button]').removeClass('pointer-events-none');
            $(item).find('input[type=text]').removeClass('pointer-events-none');
            $(item).find('select').removeClass('pointer-events-none');
        }
    }

    //click button update type m_products table && change background to pink
    $(document).on('click', '.changeBackgroundToPink', function () {
        let index = $(this).data('index');
        let valueCurrent = $('.m_product_name_edit_'+index).val()
        $('.row-table-item-' + index + ' td').addClass('bg_pink');
        $('.row-table-item-' + index + ' .m_product_name_edit_' + index).remove();
        $('.row-table-item-' + index).find('.error-validate').remove()
        $('.row-table-item-' + index + ' .name_prod_' + index).append(`
        <input type="text" class="em30 m_product_name_edit m_product_name_edit_${index}" name="data[${index}][data_edit][m_product_name]" autocomplete="off" value="${valueCurrent}" key-prod="${index}"/><br />
        `);
        $('.m_product_type_' + index).val(mProductType4)
        $('.product_type_' + index).val(mProductType4)
        $('.product_type_' + index).val(mProductType4)
        $(this).hide()
    });

    //Delete item
    $('.deleteItem').on('click', function () {
        let txt = $(this).val()
        if (txt == '削除') {
            $(this).val('復帰')
            $(this).addClass('btn_c')
            $(this).removeClass('btn_d')
        } else {
            $(this).val('削除')
            $(this).addClass('btn_d')
            $(this).removeClass('btn_c')
        }

        // 削除
        let index = $(this).closest('.row-table-item').data('index')
        $('.row-table-item-' + index + ' td').toggleClass('bg_gray')
        let el = $('.row-table-item-' + index + ' .delete_item_hide')
        if (el.is(':checked')) {
            el.prop('checked', false)
        } else {
            el.prop('checked', true)
        }
    });
    $.each($('[data-is_delete]'), function () {
        let isDelete = $(this).data('is_delete');
        if (isDelete == true) {
            $(this).click();
        }
    });

    //Hide item
    $(document).on('click', '.hideItem', function () {
        $(this).closest('tr').remove()
    })

    $(document).on('click', '#btn_confirm', function () {
        $('#duplicate_modal').css('display', 'none')
    })

    $(document).on('click', '.close', function () {
        $('#validate_modal').css('display', 'none')
        $('#duplicate_modal').css('display', 'none')
    })

    $(document).on('click', '#btn_cancel', function () {
        $('#validate_modal').css('display', 'none')
    })

    // add product
    $('#add_prod').on('click', function () {
        let flugData = limitAddRow()
        if(!flugData) {
            return false;
        }
        addProductDefault();
        checkStatusInputCheckAll()
        disabledButtonAll();
        disabledButtonSingle()
    })

    // add row yellow
    $('#add_prod_free').on('click', function () {
        let flugData = limitAddRow()
        if(!flugData) {
            return false;
        }
        addProductFree();
        checkStatusInputCheckAll()
        disabledButtonAll();
        disabledButtonSingle()
    })

    function limitAddRow() {
        let total = $('.row-table-item').filter(function(indx, item) {
           return !$(item).find('.delete_item_hide').is(':checked');
        }).length;

        if(total >= limitAddRowConst) {
            $.confirm({
                title: '',
                content: errorMessageYouCanEnter100Items,
                buttons: {
                    cancel: {
                        text: confirmText,
                        btnClass: 'btn-default',
                        action: function () {
                        }
                    },
                }
            });
            return false;
        }
        return true;
    }

    // add old code row yellow prod_code_old_data_1
    $(document).on('click', '.add_code_old_data', function () {
        let index = $(this).last().data('index');
        if ($('.is_block_'+index).val() == isBlockConst) {
            return
        }
        let indexCodeOld = $('.prod_code_old_data_' + index).length;
        if (indexCodeOld >=29) {
            $(this).remove()
        }
        $('.row-table-item-' + index).find('[data-code-list]').append(`
            <input type="text" class="em18 prod_code prod_code_old_data prod_code_old_data_${index} m_code_edit_${index}" value="" data-code name="data[${index}][data_edit][code][${indexCodeOld}][name]" autocomplete="off"/>
        `)
    })

    //add new code row yellow
    let indexCode = 0;
    $(document).on('click', '.add_code', function () {
        let index = $(this).closest('.row-table-item').data('index')
        indexCode++
        $('.row-table-item-' + index).find('[data-code-list]').append(`
            <input type="text" class="em18 prod_code" value="" data-code name="data[${index}][data_edit][code][${indexCode}][name]" data-index="${index}" data-index-code="${index}${indexCode}" autocomplete="off"/>
        `)
    })

    //on change m_product_name_edit
    $(document).on('change', 'select.m_product_name_edit', function () {
        let idProduct = $(this).find('option:checked').data('id')
        let index = $(this).closest('.row-table-item').data('index')
        ajaxGetInfoCodeAndistinction(idProduct, index)
    })

    //ajax get info code and distinction
    function ajaxGetInfoCodeAndistinction(idProduct, index) {
        $.ajax({
            method: 'GET',
            url: routeMproductGetCodeAndDistinction,
            data_type: 'json',
            data: {
                product_id: idProduct
            },
            success: function (res) {
                if (res) {
                    $('.label_m_distinction_edit_' + index).text(res.m_distinction?.name)
                    $('.m_distinction_edit_' + index).val(res.m_distinction?.id)
                    if (res?.m_codes && res?.m_codes.length > 0) {
                        //TODO
                    }
                }
            },
            error: function (data) {
                // location.reload();
            }
        });
    }

    /**
     * Add row white type_product:1,2
     */
    function addProductDefault() {
        let index = $('.row-table-item').length
        let mProducts = JSON.parse(MPRODUCTS);
        let mProductionOption = '';
        $.each(mProducts, function (id, value) {
            mProductionOption += `<option value="${value}" data-id="${id}" name="m_product">${value}</option>`;
        })
        var html = `
       <tr class="row-table-item row-table-item-${index}" data-index="${index}">
            <td class="center">
                ${index + 1}<br />
                <input type="button" value="削除" class="small btn_d hideItem" />
                <input type="hidden" name="data[${index}][is_decision]" class="is_decision is_decision_${index}" value="" />
                <input type="hidden" name="data[${index}][product_type]" class="product_type product_type_${index}" value="" />
            </td>
            <td class="boxes"><br />
                <!--<input type="button" value="修正" class="btn_a mb05 copySingleDraftToEdit disabledAllButton disabled_btn_single_${index}" data-index="${index}"/>
                <input type="button" value="決定" class="btn_b mb05 copySingleDraftToDecision disabledAllButton disabled_btn_single_${index}" data-index="${index}" /> </td>
            <td class="center"></td>-->
            <td></td>
            <td></td>
            <!--data_edit-->
            <td class="boxes">
                 <div class="name_prod_${index}" style="position: relative">
                  <input type="text" class="em30 prod_name input_product_name m_product_name_edit m_product_name_edit_${index}"
                               name="data[${index}][data_edit][m_product_name]" data-suggest id="product_name_${index}" autocomplete="off" key-prod="${index}" nospace />
                   <input type="hidden" name="data[${index}][data_edit][product_id]" value="" class="m_product_id_edit m_product_id_edit_${index}">
                 </div>
                <input type="button" value="決定" class="btn_b copySingleEditToDecision disabledAllButton disabled_btn_single_${index} valid" data-index="${index}" />
                <input type="button" value="編集" class="btn_a mb05 disabledAllButton disabled_btn_single_${index} changeBackgroundToPink" data-index="${index}"/></td>
              <td class="center tr_m_distinction_edit_${index}">
                    <div class="label_m_distinction_edit_${index}"></div>
                    <input type="hidden" name="data[${index}][data_edit][m_distinction_id]" class="m_distinction_edit_${index}" />
              </td>
            <td>
                <div class="label_m_code_edit_${index}"></div>
                <input type="hidden" name="data[${index}][data_edit][m_code]" value="" class="m_code_edit_${index}">
            </td>
            <!--data decision -->
            <td class="">
               <input type="hidden" name="data[${index}][data_decision][m_product_type]" class="m_product_type m_product_type_${index}">
               <div class="label_m_product_name_decision_${index}"></div>
               <input type="hidden" name="data[${index}][data_decision][m_product_name]" class="m_product_name_decision m_product_name_decision_${index}">
               <input type="hidden" name="data[${index}][data_decision][product_id]" class="m_product_id_decision m_product_id_decision_${index}">
            </td>
            <td class="center">
                <div class="label_m_distinction_decision_${index}"></div>
                <input type="hidden" name="data[${index}][data_decision][m_distinction_id]" class="m_distinction_decision_${index}">
           </td>
            <td class="">
               <div class="label_m_code_decision_${index}"></div>
               <input type="hidden" name="data[${index}][data_decision][m_code]" class="m_code_decision_${index}">
           </td>
            <td class="center"><input type="checkbox" name="data[${index}][is_block]" class="is_block is_block_${index}" data-id="0" value="${isBlockConst}"/> ${confirmTextEdit}</td>
     </tr>
    `;
        $('tBody.dataOld').append(html)
    }

    /**
     * add row yellow type_product: 3
     */
    function addProductFree() {
        let index = $('.row-table-item').length
        let distinction = JSON.parse(DISTINCTION);
        let distinctionOption = '';
        $.each(distinction, function (id, value) {
            distinctionOption += `<option value="${id}" name="distinction">${value}</option>`;
        })
        var html = `
       <tr class="row-table-item row-table-item-${index}" data-index="${index}">
            <td class="center bg_yellow">
                ${index + 1}.<br>
                <input type="button" value="${deleteText}" class="small btn_d hideItem">
                 <input type="hidden" name="data[${index}][product_type]" class="product_type product_type_${index}" value="${mProductType3}">
                 <input type="hidden" name="data[${index}][is_decision]" class="is_decision is_decision_${index}" value="" />
            </td>
            <td class="boxes bg_yellow"><br>
            <td class="center bg_yellow"></td>
            <td class="bg_yellow"></td>

            <td class="boxes bg_yellow">
                <div class="name_prod_${index}" style="position: relative">
                   <input type="text" class="em30 m_product_name_edit m_product_name_edit_${index}" autocomplete="off" name="data[${index}][data_edit][m_product_name]" value="" key-prod="${index}"><br>
                </div>
               <input type="button" value="${decisionText}" class="btn_b copySingleEditToDecision disabledAllButton disabled_btn_single_${index}" data-index="${index}">
            <td class="center bg_yellow m_distinction_edit_${index}">
            <select name="data[${index}][data_edit][m_distinction_id]" class="m_distinction_edit_${index} m_distinction_edit_${index} valid">
              @foreach($distinction as $value)
                    ${distinctionOption}
                @endforeach
            </select>
            </td>
            <td class="bg_yellow">
                <div data-code-list class="data-code-list">
                    <input type="text" class="em18 prod_code" value="" autocomplete="off" name="data[${index}][data_edit][code][0][name]" data-index="${index}" data-index-code="${index}0"/>
                </div>
                <input type="button" class="add_code" value="${addCodeText}">
            </td>
            <td class="bg_yellow">
               <input type="hidden" name="data[${index}][data_decision][m_product_type]" class="m_product_type m_product_type_${index}" value="${mProductType3}">
               <div class="label_m_product_name_decision_${index}"></div>
               <input type="hidden" name="data[${index}][data_decision][m_product_name]" class="m_product_name_decision m_product_name_decision_${index}">
           </td>
            <td class="center bg_yellow">
                <div class="label_m_distinction_decision_${index} type_bg_yellow"></div>
                <input type="hidden" name="data[${index}][data_decision][m_distinction_id]" value="" class="m_distinction_decision_${index} type_bg_yellow">
           </td>
            <td class="bg_yellow">
               <div class="label_m_code_decision_${index}"></div>
               <input type="hidden" name="data[${index}][data_decision][m_code]" value="" class="m_code_decision_${index}">
           </td>

            <td class="center bg_yellow"><input type="checkbox" class="is_block is_block_${index}" name="data[${index}][is_block]" data-id="0" value="${isBlockConst}">${disabledChecked}</td>
            </tr>
    `;
        $('tBody.dataOld').append(html)
    }

    $(document.body).click( function() {
        const dataSearch = $('.search-suggest:visible')
        if (dataSearch.length) {
            dataSearch.find('.search-suggest__list').find('.item').first()[0].click();
        }
        $('.search-suggest').hide()
    });
});
