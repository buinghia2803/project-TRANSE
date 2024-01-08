class EditFreeHistory {
    constructor() {
        this.initValidate();
        this.onClickRemoveFile();
        this.onChangeAttachment();
        this.onChangeType();

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
            'internal_remark': {
                maxlength: 1000,
            },
            'comment': {
                maxlength: 1000,
            },
            'attachment[]': {
                required: function () {
                    let type = parseInt($('input[name=type]:checked').val());
                    let attachFileLength = $('.attachment-group').find('p').length;
                    return type == TYPE_4 && attachFileLength == 0;
                },
                extension: 'pdf',
            },
            'is_check_amount': {
                required: function () {
                    let type = parseInt($('input[name=type]:checked').val());
                    return type == TYPE_4;
                },
            }
        }

        this.messages = {
            'internal_remark': {
                maxlength: errorMessageMaxLength1000,
            },
            'comment': {
                maxlength: errorMessageMaxLength1000,
            },
            'attachment[]': {
                required: errorMessageRequiredFile,
                extension: errorMessageFileExtension,
                accept: errorMessageFileExtension,
            },
            'is_check_amount': {
                required: errorMessageRequiredIsCheckAmount,
            },
            'user_response_deadline': {
                min: errorMessageIsValidResponseDeadline,
                max: errorMessageIsValidResponseDeadline,
            },
        }

        new clsValidation('#form', { rules: this.rules, messages: this.messages })
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
            } else if(groupItem.length + files.length > 20) {
                hasValid = false;
                errorMessage = errorMessageMax20File;
            } else {
                $.each(files, function (index, item) {
                    if (item.type != 'application/pdf') {
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

    onChangeType() {
        $('body').on('change', 'input[name=type]', function (e) {
            $('input[name=is_check_amount]').closest('td').find('.notice').remove();
        });
    }
}

new EditFreeHistory()
