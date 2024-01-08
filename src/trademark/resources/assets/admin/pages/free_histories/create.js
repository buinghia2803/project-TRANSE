class CreateFreeHistory {
    constructor() {
        this.onChangeType();
        this.onChangeProperty();
        this.onClickRemoveFile();
        this.onChangeAttachment();
        this.onChangeAmountType();
        this.onChangeAmount();
        this.onChangeUserResponseDeadline();
        this.initValidate();

        $(document).ready(function() {
            $('input[name=type]:checked').change();
        });
    }

    /**
     * Init validate
     */
    initValidate() {
        // paymentRule, paymentMessage is constant in file payer-info.blade.php
        this.rules = {
            'property': {
                required: true,
            },
            'status_name': {
                required: true,
                maxlength: 255,
            },
            'internal_remark': {
                maxlength: 1000,
            },
            'comment': {
                maxlength: 1000,
            },
            'amount_type': {
                required: function () {
                    let type = parseInt($('input[name=type]:checked').val());
                    return type == TYPE_4;
                },
            },
            'amount': {
                required: function () {
                    let amountType = parseInt($('input[name=amount_type]:checked').val());
                    return amountType == 2;
                },
                min: 1,
                max: 100000,
                number: true,
            },
            'attachment[]': {
                required: function () {
                    let type = parseInt($('input[name=type]:checked').val());
                    let attachFileLength = $('.attachment-group').find('.attachment-item').length;
                    return type == TYPE_4 && attachFileLength == 0;
                },
                extension: 'pdf',
            },
            'user_response_deadline': {
                required: function () {
                    let type = parseInt($('input[name=type]:checked').val());
                    return type == TYPE_4;
                },
            },
        }

        this.messages = {
            'property': {
                required: errorMessageRequired,
            },
            'status_name': {
                required: errorMessageRequired,
                maxlength: errorMessageMaxLength255,
            },
            'internal_remark': {
                maxlength: errorMessageMaxLength1000,
            },
            'comment': {
                maxlength: errorMessageMaxLength1000,
            },
            'amount_type': {
                required: errorMessageMaxAmount,
            },
            'amount': {
                required: errorMessageMaxAmount,
                min: errorMessageMaxAmount,
                max: errorMessageMaxAmount,
                number: errorMessageMaxAmount,
            },
            'attachment[]': {
                required: errorMessageRequiredFile,
                extension: errorMessageFileExtension,
                accept: errorMessageFileExtension,
            },
            'user_response_deadline': {
                required: errorMessageRequiredUserResponseDeadline,
                min: errorMessageMinCurrentDate,
            },
            'patent_response_deadline': {
                min: errorMessageMinCurrentDate,
            }
        }

        new clsValidation('#form', { rules: this.rules, messages: this.messages })
    }

    onChangeType() {
        $('body').on('change', 'input[name=type]', function (e) {
            let val = parseInt($(this).val());
            let amountType = $('input[name=amount_type]:checked').val();

            if (val == TYPE_1 || val == TYPE_4) {
                $('input[name=patent_response_deadline]').prop('disabled', false);
            } else {
                $('input[name=patent_response_deadline]').prop('disabled', true);
            }

            if (val == TYPE_4) {
                $('input[name=user_response_deadline]').prop('disabled', false);

                $('input[name=amount_type]').prop('disabled', false).removeClass('disabled');

                if (amountType == AMOUNT_TYPE_3) {
                    $('input[name=amount_type][value="' + AMOUNT_TYPE_1 + '"]').prop('checked', true);
                }

                $('input[name=amount_type][value="' + AMOUNT_TYPE_3 + '"]').prop('disabled', true).addClass('disabled');
            } else {
                $('input[name=user_response_deadline]').val('').prop('disabled', true);

                $('input[name=amount_type][value="' + AMOUNT_TYPE_1 + '"]').prop('disabled', true).addClass('disabled');
                $('input[name=amount_type][value="' + AMOUNT_TYPE_2 + '"]').prop('disabled', true).addClass('disabled');
                $('input[name=amount_type][value="' + AMOUNT_TYPE_3 + '"]').prop('checked', true);
            }

            $('select[name=property]').change();
            $('input[name=amount_type]:checked').change();
            $('input[name=user_response_deadline]').change();
        });
    }

    onChangeProperty() {
        $('body').on('change', 'select[name=property]', function (e) {
            let type = parseInt($('input[name=type]:checked').val());
            let property = $(this).val();

            $(this).parent().find('.error').remove();

            if (type == TYPE_4 && property == 5) {
                $(this).after(`<div class="error">${errorMessageIsValidProperty}</div>`);
            }
        })
    }

    onClickRemoveFile() {
        $('body').on('click', '.remove-file', function (e) {
            e.preventDefault();

            let attachmentItem = $(this).closest('.attachment-item');

            let attachRemoveUrl = attachmentItem.find('input').val();
            attachmentItem.after(`<input type="hidden" name="attachment_remove[]" value="${attachRemoveUrl}">`);
            attachmentItem.remove();
        });
    }

    onChangeAttachment() {
        $('body').on('change', 'input[name^=attachment]', function (e) {
            let el = $(this);
            let files = e.target.files;
            let hasValid = true;
            let errorMessage = '';
            let attachmentHTML = '';
            let groupBox = $(this).closest('td').find('.attachment-group');
            let groupItem = groupBox.find('.attachment-item');

            $(this).closest('td').find('.error').remove();
            $(this).closest('td').find('.notice').remove();

            if (files.length == 0) {
                return false;
            } else if(groupItem.length + files.length > MAX_FILE_UPLOAD) {
                hasValid = false;
                errorMessage = errorMessageMax20File;
            } else {
                $.each(files, function (index, item) {
                    if (item.type != 'application/pdf') {
                        hasValid = false;
                        return;
                    }
                    if (item.size > MAX_FILESIZE) {
                        hasValid = false;
                        errorMessage = errorMessageMaxFilesize;
                        return;
                    }
                })
            }

            if (hasValid == false) {
                $(this).after(`<div class="error">${errorMessage}</div>`);
            } else {
                var formData = new FormData();

                $.each(files, function (index, file) {
                    formData.append("files[]", file);
                });

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    type: 'POST',
                    url: ROUTE_UPLOAD_FILE,
                    contentType: false,
                    processData: false,
                    data: formData,
                    beforeSend: function () {
                        loadingBox('open')
                    },
                    success: function (result) {
                        loadingBox('close')
                        if(result.status == 200){
                            attachmentHTML = '';

                            $.each(result.filepath, function (index, item) {
                                attachmentHTML += `
                                    <p class="attachment-item mb-1 d-flex">
                                        <span class="line line-1">${item.name}</span>
                                        <span class="remove-file red cursor-pointer ms-1">Ⓧ</span>
                                        <input type="hidden" name="attachment_input[]" value="${item.path}">
                                    </p>
                                `;
                            });

                            el.closest('td').find('.attachment-group').append(attachmentHTML);
                            el.val('');
                        }
                    },
                    error: function (error) {
                        loadingBox('close')
                    }
                });
            }
        });
    }

    onChangeAmountType() {
        $('body').on('change', 'input[name=amount_type]', function () {
            let value = $(this).val();

            if (value == AMOUNT_TYPE_2) {
                $('input[name=amount]').prop('disabled', false);
            } else {
                $('input[name=amount]').val('').prop('disabled', true);
            }
        });
        $('input[name=amount_type]:checked').change();
    }

    onChangeAmount() {
        let invalidChars = [",", ".", "-", "+", "E", "e"];

        $('body').on('change keyup keydown', 'input[name=amount]', function (e) {
            if(invalidChars.includes(e.key)){
                return false;
            }
        });
    }

    onChangeUserResponseDeadline() {
        $('body').on('change', 'input[name=user_response_deadline]', function (e) {
            e.preventDefault();
            let userResponseDeadline = $(this).val();
            userResponseDeadline = new Date(userResponseDeadline);

            let patentResponseDeadline = $('input[name=patent_response_deadline]').val();
            patentResponseDeadline = new Date(patentResponseDeadline);

            $(this).parent().find('.error, .notice').remove();

            if (userResponseDeadline > patentResponseDeadline) {
                $(this).after(`<div class="error">${errorMessageMaxUserResponseDeadline}</div>`);
            }
        });
        $('input[name=user_response_deadline]').change();

        $('body').on('change', 'input[name=patent_response_deadline]', function (e) {
            e.preventDefault();

            $('input[name=user_response_deadline]').change();
        });
    }
}

new CreateFreeHistory()
