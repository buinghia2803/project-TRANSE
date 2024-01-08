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

    /**
     * initial when load
     */
    init() {
        if (isBlockScreen) {
            this.disableInput()
        } else {
            this.clickFilePdf()
            this.clickEleDownload()
            this.clickBtnUpload()
            this.changeFileUpload()
            this.checkRoute()
            this.submit()
            this.validateCustomer()
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

    clickEleDownload() {
        $('.click_btn_ele_download').click(function () {
            const a = $(this).closest('tr').find('.click_ele_download')
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
            self.closest('tr').find('.error').remove()
            let files = self[0].files

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
                            if (item.length > 0) {
                                let lengthAttach = self.closest('tr').find('.attach-group').find('.attach-item').length
                                self.closest('tr').find('.attach-group').prepend(`
                                <div class="attach-item" style="display: flex">
                                    <input type="hidden" class="data-hidden" name="data[${self.data('plan_id')}][plan_detail_doc][${self.data('plan_detail_doc_id')}][attach][${lengthAttach}]" value="${item}">
                                    <span class="line line-2">${item.replace("/uploads/temp/", "")}</span>
                                    <span class="delete-file" data-file="${item}">&times;</span>
                                </div>
                            `);
                            }
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
        const form = $('form')
        form.find('a, input, button, textarea, select').prop('disabled', true)
        form.find('a, input, button, textarea, select').addClass('disabled')
        form.find('a').attr('href', 'javascript:void(0)')
        form.find('a').attr('target', '')
        $('[type=submit]').remove()
        $('#cart').prop('disabled', false);
        $('.checkQuestion').prop('disabled', true).addClass('disabled')
    }

    checkRoute() {
        function disableInput() {
            const form = $('form');
            form.find('input:not([type="submit"]), textarea, select').prop('readonly', true).prop('disabled', true).addClass('disabled');
            form.find('input[name="_token"]').prop('readonly', false).prop('disabled', false);
            form.find('.data-hidden').each(function (idx, item) {
                $(item).prop('disabled', false);
            })
        }

        if (routeConfirm){
            disableInput();
        } else {
            if (!isBlockScreen) {
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
                            required_document_id: requiredDoc ? requiredDoc.id : null,
                            from_send_doc: 'u204',
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
                            URL <input type="text" class="em18 mb05 data-hidden input_url" name="data[${$(this).data('plan_id')}][plan_detail_doc][${$(this).data('plan_detail_doc_id')}][url][${lengthAppend}]" nospace/>
                        </div>
                    `);
                })
            }

        }
    }

    /**
     * It's a function that triggers the change event on a textarea and an input field when a submit
     * button is clicked.
     */
    submit() {
        $('body').on('click', 'input[type=submit],button[type=submit]', function(e) {
            $('textarea.content').change();
            $('.input_first').change();
            let el = $(this);

            let hasSubmit = true;

            let attachGroup = $('.attach-group');
            $.each(attachGroup, function () {
                let attachItem = $(this).find('.attach-item');
                $(this).parent().find('.error').remove();

                if (el.attr('name') == 'submit') {
                    if (attachItem.length == 0) {
                        hasSubmit = false;
                        $(this).after(`<div class="error">${correspondence_U204_E005}</div>`)
                    }
                }
            })

            if (hasSubmit == false) {
                let firstError = $('form').find('.notice:visible,.error:visible').first();
                window.scroll({
                    top: firstError.offset().top - 100,
                    behavior: 'smooth'
                });

                return false;
            }
        });
    }
}
new clsMaterial()
