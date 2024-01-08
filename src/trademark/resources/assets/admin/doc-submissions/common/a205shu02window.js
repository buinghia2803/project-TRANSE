class a205Shu02WindowClass {
    constructor() {
        const self = this
        window.addEventListener('load', function() {
           self.doLoad()
        });
    }

    doLoad() {
        this.onSubmitForm()
        this.addNewRowDocSubmission()
        this.isCheckedWrittenOption()
        this.onChangeIsWrittenOpinion()
        this.removeItem()
        this.deleteFile()
        this.showButtonAddRow()
        this.renderImageInputFiles()
        this.validateInput()
    }

    initValidate() {
        //1.validate description_written_opinion
        let errorDesWrittenHtml = null;
        if($('#description_written_opinion').val().length > 1000) {
            errorDesWrittenHtml = `<span class="error">${errorMessageMaxLength1000String}</span>`;
        }

        $('#wp-description-written-opinion').find('.show-error').html(errorDesWrittenHtml)

        //2.validate data-property-name
        $('.data-property-name').each(function (key, item) {
            if ($(item).val() == '') {
                $(item).closest('.show-error-box').find('.show-error').html(`<span class="error">${errorMessageRequired}</span>`)
            } else if ($(item).val().length > 255) {
                $(item).closest('.show-error-box').find('.show-error').html(`<span class="error">${errorMessageMaxLength255}</span>`)
            } else {
                $(item).closest('.show-error-box').find('.show-error').html('')
            }
        });

        //3.validate file_no
        let regex = /^[1-9][0-9]*$/;

        $('.file_no').each(function (key, item) {
            let value = $(item).val();
            if (value == '') {
                $(item).closest('.delete').find('.show-error-file-no').html(`<span class="error">${errorMessageRequired}</span>`)
            } else if (value.length > 3) {
                $(item).closest('.delete').find('.show-error-file-no').html(`<span class="error">${errorMessageFormatFileNo}</span>`)
            } else if (!regex.test(value) && value !== '') {
                $(item).closest('.delete').find('.show-error-file-no').html(`<span class="error">${errorMessageFormatFileNo}</span>`)
            } else {
                $(item).closest('.delete').find('.show-error-file-no').html('')
            }
        });

        //4.Validate input files
        $('.attach_file').each(function (key, item) {
            //total current file old
            let totalFileOld = $(item).closest('.bukken').find('.item-attach-file-old').length;
            //validate input files when on change input files
            if (item.files.length + totalFileOld == 0) {
                $(item).closest('.row-item-submission').find('.show-error-attach-file').html(`<span class="error">${Freerireki_E003}</span>`)
            } else if (item.files.length + totalFileOld > 20) {
                $(item).closest('.bukken').find('.show-error-attach-file').html(`<span class="error">${Import_A000_E001}</span>`)
            } else {
                $(item).closest('.bukken').find('.show-error-attach-file').html('')
            }
        });
    }

    validateInput() {
        let self = this;
        $('body').on('change keyup focusout', 'input, textarea', function(e) {
            self.initValidate()
        });
    }

    //submit form
    onSubmitForm() {
        let self = this
        $('body').on('click', 'input[type=submit]', function () {
            //validate form
            self.initValidate()

            if($('.error').length) {
                //roll to class error first
                document.querySelector('.error').scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });

                return false;
            }
        });
    }

    //disabled description_written_opinion
    isCheckedWrittenOption() {
        if($('#is_written_opinion').is(':checked')) {
            $('.written_opinion').hide();
            $('#description_written_opinion').prop('readonly', true).addClass('disabled');
        } else {
            $('.written_opinion').show();
            $('#description_written_opinion').prop('readonly', false).removeClass('disabled');
        }
    }

    //on change is_written_opinion
    onChangeIsWrittenOpinion() {
        const self = this
        $('#is_written_opinion').on('change', function() {
            self.isCheckedWrittenOption()
        })
    }

    showButtonAddRow() {
        if($('.row-item-submission').length >= 20) {
            $('#add_row_doc_submission').hide()
        } else {
            $('#add_row_doc_submission').show()
        }
    }

    //add new row
    addNewRowDocSubmission() {
        const self = this;
        $('body').on('click', '#add_row_doc_submission', function(e) {
            let validator = $("#form").validate();
            validator.resetForm();
            e.preventDefault()
            let currentKey  = $('.row-item-submission').last().data('key') ?? -1;
            let keyRow = currentKey + 1;
            let html = `<div class="row-item-submission row-item-submission-new" data-key="${keyRow}">
                            <dt>${textNameSubmission}</dt>
                            <dd class="show-error-box">
                                <input type="text" class="em24 data-property-name" name="data-properties[${keyRow}][name]" value=""/>
                                <input type="button" value="${textDeleteRow}" class="btn_d delete-row"/>
                                <div class="show-error red"></div>
                            </dd>
                            <dt>${contentFile}</dt>
                            <dd class="bukken clearfix">
                                <input type="file" name="data-properties[${keyRow}][attach_file][]" class="attach_file" id="attach_file_${keyRow}" accept="image/png, image/gif, image/jpeg, image/bmp" multiple/><br/>
                                <div class="show-error-attach-file"></div>
                                <br/>
                                <div class="list-item-attach-file bqn-${keyRow}"></div>
                            </dd>
                        </div>`;
            $('#list-submission-property').append(html)
            self.showButtonAddRow()
        });
    }

    //removeItem: delete data(old data) or remove html(data append)
    removeItem() {
        const self = this
        $('body').on('click', '.delete-row', function() {
            let docSubAttachProId = $(this).data('doc-submission-attach-property-id')
            let rowItem = $(this).closest('.row-item-submission')

            $.confirm({
                title: textTitleModal,
                content: textContentModal,
                buttons: {
                    cancel: {
                        text: textCancel,
                        btnClass: 'btn-default',
                        action: function () {
                        }
                    },
                    action: {
                        text: textTitleModal,
                        btnClass: 'btn-blue',
                        action: function () {
                            if(docSubAttachProId) {
                                //call ajax delete on db
                                let data = {
                                    doc_submission_attach_property_id: docSubAttachProId
                                }
                                self.callAjaxDeleteDocSubmissionAttachProperty(data, rowItem)
                            } else {
                                //remove html append
                                rowItem.remove()
                                self.showButtonAddRow()
                            }
                        }
                    }
                }
            });
        });
    }

    //call ajax delele doc_submission_attach_properties
    callAjaxDeleteDocSubmissionAttachProperty(data, rowItem) {
        $.ajax({
            method: 'POST',
            url: routeDeleteDocSubmissionAttachProperty,
            data_type: 'json',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: data,
            success: function(res) {
                if(res.status) {
                    rowItem.remove()
                }
            },
            error: function(data) {
                // location.reload();
            }
        });
    }

    //delete file
    deleteFile() {
        $('body').on('click', '.delete-file', function() {
            let self = this
            let docSubAttachmentId = $(this).data('id')

            let itemAttachFile = $(this).closest('.item-attach-file')
            if(docSubAttachmentId) {
                $.ajax({
                    method: 'POST',
                    url: routeDeleteDocSubmissionAttach,
                    data_type: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        'doc_submission_attachment_id': docSubAttachmentId
                    },
                    success: function(res) {
                        if(res.status) {
                            itemAttachFile.remove()
                        }
                    },
                    error: function(data) {
                        // location.reload();
                    }
                });
            } else {
                //remove html (data append)
                let idFileClick = $(self).data('file-id')
                let input = document.getElementById(idFileClick);
                let inputFiles = $(self).closest('.row-item-submission').find('.attach_file');
                if (input) {
                    const dataTransfer = new DataTransfer();
                    let indexClick =  $(self).data('index');
                    for (let i = 0; i < inputFiles[0].files.length; ++i) {
                        if(i !== +indexClick) {
                            dataTransfer.items.add(inputFiles[0].files[i])
                        }
                    }
                    inputFiles[0].files = dataTransfer.files

                    itemAttachFile.remove()

                    document.querySelectorAll('.bqn-' + $(self).data('row') + ' .item-attach-file-new .delete-file').forEach((ele, fff) => {
                        ele.setAttribute('data-index', fff)
                    })

                }
            }
        });
    }

    //render image file - on change input file
    renderImageInputFiles() {
        $('body').on('change', '.attach_file', function() {
            let idRowFile = $(this).attr('id')
            const self = this
            let dataFiles = self.files

            //reset div file new
            $(self).closest('.row-item-submission').find('.item-attach-file-new').remove()
            //key row
            let keyRow = $(self).closest('.row-item-submission').data('key');
            //key attach file
            let keyFile = $(self).closest('.row-item-submission').find('.item-attach-file').last().data('key-attach') + 1
            if(!keyFile) {
                keyFile = 0
            }


            //get DOM input file
            let inputFileDome = document.getElementById('attach_file_' + keyRow);
            let children = "";
            let files = []
            //validate format file
            const extensions = ['gif', 'jpeg', 'jpg', 'png', 'bmp'];
            const validImageTypes = ['image/gif', 'image/jpeg', 'image/jpg', 'image/png', 'image/bmp'];
            const dataTrans = new DataTransfer();
            let elementErrorFile = $(self).closest('.bukken').find('.show-error-attach-file')

            for (let j = 0; j < dataFiles.length; ++j) {
                let fileName = dataFiles[j]['name'];
                let fileExtension = fileName.split(".").at(-1);

                //check has file > 3MB => remove file on list files
                if(dataFiles[j].size > 3*1024*1024) {
                    $(elementErrorFile).html(`<span class="error">${errorMessageMaxFileSize3MB}</span>`)
                } else if(!validImageTypes.includes(dataFiles[j]['type']) || !extensions.includes(fileExtension)) {
                    $(elementErrorFile).html(`<span class="error">${errorMesssageFormatFile}</span>`)
                } else {
                    files.push(dataFiles[j])
                    dataTrans.items.add(dataFiles[j])
                    $(elementErrorFile).html('')
                }
            }
            inputFileDome.files = dataTrans.files
            dataFiles = dataTrans.files
            //============ end check

            //total current file old
            let totalFileOld = $('.bqn-'+keyRow).find('.item-attach-file-old').length;
            //validate input files when on change input files
           if(dataFiles.length + totalFileOld > 20) {
                inputFileDome.value = ''
                $(elementErrorFile).html(`<span class="error">${Import_A000_E001}</span>`)

                return false
            }


            let i;
            for(i = 0;i < dataFiles.length;i++) {
                let index = keyFile + i
                var reader = new FileReader();
                reader.onload = function(event) {
                    let html = `<div class="item-attach-file item-attach-file-new">
                                    <img src="${event.target.result}">
                                    <div class="delete">
                                        <span>${textOrder}</span>
                                        <input type="text" class="em04 mb05 file_no" name="data-properties[${keyRow}][data-attach][${index}][file_no]" value=""><br>
                                        <div class="show-error-file-no"></div>
                                        <input type="button" value="${labelDelete}" data-index="${index}" data-row="${keyRow}" data-file-id="${idRowFile}" class="btn_d delete-file">
                                    </div>
                                </div>`;
                    $(self).closest('.row-item-submission').find('.list-item-attach-file').append(html)

                }
                reader.readAsDataURL(dataFiles[i]);
            }

        });
    }
}

new a205Shu02WindowClass()
