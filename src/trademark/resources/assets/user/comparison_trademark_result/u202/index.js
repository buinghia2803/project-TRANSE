class preQuestionReply {
    constructor() {
        const self = this
        window.addEventListener('load', function () {
            self.doLoad();
        })
    }

    /**
     * Do load class
     */
    doLoad() {
        this.clickFilePDF()
        this.clickOpenFileUpload()
        this.setFormPageSubmitForm()
        this.deleteFile()
        this.showNameFileSelect()
    }

    //delete file
    deleteFile() {
        $('.delete-file-icon').on('click', function() {
            const self = this
            let path = $(this).data('url')
            let idData = $(this).data('id')
            if(path) {
                $.ajax({
                    method: 'POST',
                    url: routeDeleteFileAjax,
                    data_type: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        path: path,
                        reason_question_detail_id: idData
                    },
                    success: function(res) {
                        if(res.status) {
                           $(self).closest('.item-file').hide()
                        }
                    },
                    error: function(data) {
                        // location.reload();
                    }
                });
            }
        })
    }

    //click open pdf
    clickFilePDF() {
        $('#click_file_pdf').click(function () {
            const a = $('.click_ele_a')
            for (const object of a) {
                object.click()
            }
        })
    }

    //click open files
    clickOpenFileUpload() {
        let keyItem
        $('.button-input-file').on('click', function() {
            keyItem = $(this).data('key')
            $(`.input-files-${keyItem}`).trigger('click');
        });
    }

    /**
     * SHow file select
     */
    showNameFileSelect() {
        $('.input-files').on('change', function() {
            let self = this
            let key = $(self).data('key');

            var input = document.getElementById('input-files-' + key);
            var output = document.getElementById('files-select-'+key);

            $(`.button-input-file[data-key=${key}]`).next('.error').remove();
            if (input.files.length <= 20) {
                var children = "";
                let files = []
                let hasError = false;
                let errorMessage = '';
                const dataTransfer = new DataTransfer();
                for (let i = 0; i < input.files.length; ++i) {
                    if(input.files[i].size <= 3*1024*1024) {
                        children += '<li>' + input.files.item(i).name + '</li>';
                        files.push(input.files[i])
                        dataTransfer.items.add(input.files[i])
                    } else {
                        hasError = true;
                        errorMessage = errorMessageMaxFileSize3MB;
                    }
                }
                if (hasError == true) {
                    $(`.button-input-file[data-key=${key}]`).after(`<div class="error">${errorMessage}</div>`);
                    output.innerHTML = '';
                } else {
                    input.files = dataTransfer.files;
                    output.innerHTML = '<ul>'+children+'</ul>';
                }
            } else {
                $(`.button-input-file[data-key=${key}]`).after(`<div class="error">${errorMessageLimit20FileUpdate}</div>`);
                output.innerHTML = '';
            }

            $(`.attachment_${key}`).remove();
        });
    }

    //set from page when submit form
    setFormPageSubmitForm() {
        $('input[type=submit]').on('click', function() {
            if($(this).hasClass('saveDraftU202')) {
                $('.from_page').val(U202_DRAFT)
            } else if($(this).hasClass('saveDraftRedirectToKakunin')){
                $('.from_page').val(U202_DRAFT_TO_KAKUNIN)
            }

            let form = $(this).closest('form');
            form.valid();
            let hasError = form.find('.error-validate:visible,.notice:visible,.error:visible');
            if (hasError.length == 0 && form.valid()) {
                form.submit();
            } else {
                let firstError = form.find('.error-validate:visible,.notice:visible,.error:visible').first();
                scrollToElement(firstError, -100);
                return false;
            }
        });
    }
}

new preQuestionReply()
