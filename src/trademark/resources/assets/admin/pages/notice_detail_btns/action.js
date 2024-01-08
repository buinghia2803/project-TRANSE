class ActionButton {
    constructor() {
        this.clickTypeBtn();
        this.onChangeXML();
        this.onChangePDF();
    }

    clickTypeBtn() {
        const self = this;

        $('body').on('click', '[data-type_btn]', function (e) {
            e.preventDefault();
            let el = $(this);

            let typeBtn = $(this).data('type_btn');

            switch (typeBtn) {
                case 'create_html':
                    self.createHtml(el);
                    break;
                case 'upload_xml':
                    $(this).closest('.button-group').find('input[name^=xml_file]').click();
                    break;
                case 'upload_pdf':
                    $(this).closest('.button-group').find('input[name^=pdf_file]').click();
                    break;
                case 'contact_customer':
                    self.contactCustomer(el);
                    break;
            }
        });
    }

    contactCustomer(element) {
        let url = element.data('route');
        loadAjaxPost(url, {}, {
            beforeSend: function () { },
            success: function (result) {
                window.location.reload();
            },
            error: function (error) { }
        }, 'loading');
    }

    createHtml(element) {
        let url = element.data('route');

        loadAjaxPost(url, {}, {
            beforeSend: function () { },
            success: function (result) {
                if (result.data_html != null && result.data_html.html_path.length > 0) {
                    const a = document.createElement('a');
                    a.href = result.data_html.html_path;
                    a.download = result.data_html.html_name;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }

                // let openUrl = result.data.url;
                // window.open(openUrl, '_self');
                window.location.reload();
            },
            error: function (error) {}
        }, 'loading');
    }

    onChangeXML() {
        $('body').on('change', 'input[name^=xml_file]', function (e) {
            let files = e.target.files;
            let form = $(this).closest('form');

            form.validate({
                rules: {
                    'xml_file[]': {
                        extension: 'xml',
                        formatFileSize: 3,
                        maxfiles: 20
                    }
                },
                messages: {
                    'xml_file[]': {
                        extension: errorMessageIsValidXML,
                        formatFileSize: errorMessageIsValidXML,
                        maxfiles: errorMessageMax20,
                    }
                },
            });

            if (!form.valid()) {
                let errorElement = form.find('.notice');
                let errorText = errorElement.text();
                $(this).val('');
                $(this).closest('td').find('.show-error').html(`<div class="error">${errorText}</div>`);
            } else {
                $(this).closest('td').find('.show-error').empty();
                form.submit();
            }
        });
    }

    onChangePDF() {
        $('body').on('change', 'input[name^=pdf_file]', function (e) {
            let files = e.target.files;
            let form = $(this).closest('form');

            form.validate({
                rules: {
                    'pdf_file[]': {
                        extension: 'pdf',
                        formatFileSize: 3,
                        maxfiles: 20,
                    }
                },
                messages: {
                    'pdf_file[]': {
                        extension: errorMessageIsValidPDF,
                        formatFileSize: errorMessageIsValidPDF,
                        maxfiles: errorMessageMax20,
                    }
                },
            });

            if (!form.valid()) {
                let errorElement = form.find('.notice');
                let errorText = errorElement.text();
                $(this).val('');
                $(this).closest('td').find('.show-error').html(`<div class="error">${errorText}</div>`);
            } else {
                $(this).closest('td').find('.show-error').empty();
                form.submit();
            }
        });
    }
}

new ActionButton();
