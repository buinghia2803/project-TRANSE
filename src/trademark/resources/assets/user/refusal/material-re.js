class clsMaterial {
    constructor() {
        const self = this
        window.addEventListener('load', function () {
            self.doLoad()
        })
    }

    /**
     * Do load all action and element.
     */
    doLoad() {
        this.init();
        this.initValidation();
    }

    /**
     * Init validate
     */
    initValidation() {
        this.rules = {}
        this.messages = {}

        new clsValidation('#form', { rules: this.rules, messages: this.messages })
    }

    //==================================================================
    // initial when load
    //==================================================================
    init() {
        if (isBlockScreen) {
            this.disableInput()
        } else {
            this.validateCustomer()
            this.clickFilePdf()
            this.clickBtnUpload()
            this.changeFileUpload()
            this.checkRoute()
            this.clickDownload()
            this.onClickSubmit()
        }
    }

    validateCustomer() {
        $('body').on('change', '.input_first', function() {
            let value = $(this).val();
            $(this).parent().find('.error').remove();

            if(value.length == 0) {
                $(this).after('<div class="error">'+ Common_E001 +'</div>');
            }
        });

        $('body').on('change', '.file_upload', function() {
            let self = $(this)
            $(this).parent().find('.error').remove();
            let files = self[0].files

            if (files.length == 0) {
                $(this).after('<div class="error">'+ correspondence_U204_E005 +'</div>');
            }
        });
        $('body').on('change', 'textarea.content', function() {
            let value = $(this).val();

            $(this).parent().find('.error').remove();
            if (value.length > 1000) {
                $(this).after('<div class="error">'+ Common_E026 +'</div>');
            }
        });
    }

    clickFilePdf() {
        $('#click_file_pdf').click(function () {
            const a = $('.click_ele_a')
            for (const object of a) {
                object.click()
            }
        })
    }

    clickBtnUpload(){
        $('body').on('click', '.btn_upload', function(e) {
            $(this).parent().find('.file_upload').click()
        })
    }

    changeFileUpload() {
        $('body').on('change', '.file_upload', function() {
            let self = $(this)
            let files = self[0].files

            if(files.length > 0) {
                self.closest('tr').find('.error').remove()
            }

            let lengthFileUpload = self.closest('tr').find('.attach-group').find('.attach-item').length

            let hasSend = true;
            if (files.length > (20 - lengthFileUpload)) {
                self.after(`<div class="red error">${Import_A000_E001}</div>`)

                hasSend = false
            }

            $.each(files, function (index, item) {
                if (item.size > 3*1024*1024) {
                    self.closest('tr').find('.error').remove()
                    self.after(`<div class="red error">${Common_E028}</div>`)

                    hasSend = false
                    return false
                } else if (!inArrayFileExt(item, ['jpg', 'jpeg', 'pdf'])) {
                    self.closest('tr').find('.error').remove()
                    self.after(`<div class="red error">${correspondence_U204_E004}</div>`)

                    hasSend = false
                    return false
                }
            })

            if (!hasSend) {
                return false
            }

            var formData = new FormData();

            for(var i=0;i<files.length;i++){
                formData.append("images[]", files[i], files[i]['name']);
            }

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                url: routeAjaxMaterial,
                contentType: false,
                processData: false,
                data: formData,
                beforeSend: function () {
                    loadingBox('open')
                },
                success: function (result) {
                    loadingBox('close')
                    if(result.status == 200){
                        $.each(result.filepath, function (index, item) {
                            let lengthAttach = self.closest('tr').find('.attach-group').find('.attach-item').length
                            self.closest('tr').find('.attach-group').prepend(`
                                <div class="attach-item" style="display: flex">
                                    <input type="hidden" class="data-hidden" name="data[${self.data('plan_id')}][plan_detail_doc][${self.data('plan_detail_doc_id')}][attach][${lengthAttach}]" value="${item}">
                                    <span class="line line-2">${item.replace("/uploads/temp/", "")}</span>
                                    <span class="delete-file" data-file="${item}">&times;</span>
                                </div>
                            `);
                        })
                    }
                },
                error: function (error) {
                    loadingBox('close')
                }
            })
        });
    }

    /**
     * It disables all form elements except for the cart button.
     */
    disableInput () {
        disabledScreen();
    }

    checkRoute() {
        function disableInput() {
            const form = $('form');
            if (requiredDocument.is_send) {
                form.find('input:not([name|=submit_confirm],[name|=draft_confirm]), textarea, select').prop('readonly', true).prop('disabled', true);
                $('[type=submit]').prop('readonly', true).prop('disabled', true)
            }
            form.find('.data-hidden').each(function (idx, item) {
                $(item).prop('disabled', false);
            })
        }

        if (routeConfirm){
            disableInput();
        } else {
            $('body').on('click', '.delete-file', function() {
                let self = $(this)
                const plan_detail_doc_id = self.data('plan_detail_doc_id')
                const file = self.data('file')
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: routeAjaxMaterialDelete,
                    data: {
                        required_document_id: requiredDocument.id,
                        from_send_doc: 'u204_' + round,
                        plan_detail_doc_id,
                        trademark_plan_id: trademarkPlanId,
                        file
                    },
                    beforeSend: function () {
                        loadingBox('open')
                    },
                    success: function (result) {
                        loadingBox('close')
                        if (result.status == 200) {
                            self.parent().parent().find('.is_has_file').remove()
                            self.closest('.attach-item').remove()
                        }
                    },
                    error: function (error) {
                        loadingBox('close')
                    }
                })
            });

            $('body').on('click', '.click_append', function(e) {
                e.preventDefault();
                let lengthAppend = $('body').find('.url-group').find('.url-item').length;
                if (lengthAppend == 9) {
                    $(this).remove();
                }

                $(".url-group").append(`
                    <div class="url-item">
                        URL <input type="text" class="em18 mb05 input_url data-hidden" name="data[${$(this).data('plan_id')}][plan_detail_doc][${$(this).data('plan_detail_doc_id')}][url][${lengthAppend}]" nospace/>
                    </div>
                `);
            })
        }
    }

    clickDownload() {
        $('.click_download').click(function () {
            $(this).closest('tr').find('.download_a_hidden').each(function (idx, item) {
                item.click();
            })
        })
    }

    onClickSubmit() {
        $('body').on('click', 'input[type=submit],button[type=submit]', function(e) {
            let el = $(this);

            if (el.attr('name') == 'submit') {
                $('textarea.content').change();
                $('.input_first').change();

                let fileClass = $('.file_upload')
                fileClass.each( function (index, item) {
                    $(item).parent().find('.error').remove();
                    let fileUploaded = item.files

                    if (fileUploaded.length == 0) {
                        if ($(item).siblings('.attach-group_old').find('p.is_has_file').length < 1) {
                            $(item).after('<div class="error">'+ correspondence_U204_E005 +'</div>');
                        }
                    }
                })
            }

            const form = $('#form');
            form.valid();

            let hasError = form.find('.notice:visible,.error:visible');
            if (hasError.length == 0 && form.valid()) {

                form.submit();
            } else {
                let firstError = form.find('.notice:visible,.error:visible').first();
                window.scroll({
                    top: firstError.offset().top - 100,
                    behavior: 'smooth'
                });
            }
        });
    }
}
new clsMaterial()
