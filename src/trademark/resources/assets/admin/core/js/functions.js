// LOAD AJAX
/*
    loadAjaxPost(url, params, {
        beforeSend: function(){},
        success:function(result){},
        error: function (error) {}
    }, 'loading');
*/
loadAjaxPost = function (url, params, option, type = 'loading') {
    var _option = {
        beforeSend: function () {},
        success: function (result) {},
        error: function (error) {}
    }
    $.extend(_option, option);
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: url,
        data: params,
        beforeSend: function () {
            switch (type) {
                case 'loading': loadingBox('open'); break;
            }
            _option.beforeSend();
        },
        success: function (result) {
            switch (type) {
                case 'loading': loadingBox('close'); break;
            }
            _option.success(result);
        },
        error: function (error) {
            switch (type) {
                case 'loading': loadingBox('close'); break;
            }
            alertText(error.responseJSON.message, 'error')
            _option.error(error);
        }
    })
}

/*
    loadAjaxGet(url, {
        beforeSend: function(){},
        success:function(result){},
        error: function (error) {}
    }, 'loading');
*/
loadAjaxGet = function (url, option, type = 'loading') {
    var _option = {
        beforeSend: function () {
        },
        success: function (result) {
        },
        error: function (error) {
        }
    }
    $.extend(_option, option);
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'GET',
        url: url,
        beforeSend: function () {
            switch (type) {
                case 'loading':
                    loadingBox('open');
                    break;
            }
            _option.beforeSend();
        },
        success: function (result) {
            switch (type) {
                case 'loading':
                    loadingBox('close');
                    break;
            }
            _option.success(result);
        },
        error: function (error) {
            switch (type) {
                case 'loading':
                    loadingBox('close');
                    break;
            }
            alertText(error.message, 'error')
            _option.error(error);
        }
    })
}

// LoadingBox
loadingBox = function (type) {
    if (type === 'open') {
        $('body').append('<section id="loading_box"><div id="loading_image"></div></section>');
        $("#loading_box").css({visibility: "visible", opacity: 0.0}).animate({opacity: 1.0}, 200);
    } else {
        $("#loading_box").animate({opacity: 0.0}, 200, function () {
            $("#loading_box").remove();
        });
    }
}

/**
 * Toastr alert
 */
alertText = function (text = '', type = 'success') {
    switch (type) {
        case 'success':
            toastr.success(text);
            break;
        case 'info':
            toastr.info(text);
            break;
        case 'error':
            toastr.error(text);
            break;
        case 'warning':
            toastr.warning(text);
            break;
    }
}

/**
 * Confirm and Submit form hidden
 */
submitForm = function (form = '', confirmText = '') {
    if (confirmText.length > 0) {
        $.confirm({
            title: '',
            content: confirmText,
            buttons: {
                cancel: {
                    text: Lang.close,
                    action: function () {}
                },
                confirm: {
                    text: Lang.confirm,
                    btnClass: 'btn-danger',
                    action: function () {
                        loadingBox('open');
                        $(form).submit();
                    }
                },
            }
        });
    } else {
        loadingBox('open');
        $(form).submit();
    }
}

/**
 * TinyMCE load default
 */
addTinyMCE = function (selectorID, height = 400, hasImage = true) {
    id = selectorID.replace('#','');
    urlUploadImage = window.location.origin + '/' + $('meta[name=admin_dir]').attr('content') + '/ajax/editor-uploads';
    tinymce.execCommand('mceRemoveEditor', false, id);
    tinymce.init({
        path_absolute : "/",
        selector: selectorID,
        branding: false,
        hidden_input: false,
        relative_urls: false,
        convert_urls: false,
        height : height,
        entity_encoding : "raw",
        plugins: [
            "advlist", "autolink", "lists", "link", "image", "charmap", "preview", "anchor",
            "searchreplace", "visualblocks", "code", "fullscreen",
            "insertdatetime", "media",  "table", "wordcount"
        ],
        language: $('meta[name=language]').attr('content'),
        toolbar1: `undo redo | bold italic underline | alignleft aligncenter alignright alignjustify | ${(hasImage) ? 'image' : ''} | table | bullist numlist | link unlink`,
        toolbar2: "styles fontfamily fontsize | forecolor backcolor | fullscreen preview code",
        setup: function (editor) {
            editor.on('keyup', function(e) {
                tinymce.triggerSave();
                $(selectorID).parent().find('.error').remove();
                $(selectorID).parent().find('.tox').removeClass('border-error');
            });
        },
        file_picker_callback: function (cb, value, meta) {
            var input = document.createElement('input');
            input.setAttribute('type', 'file');
            input.setAttribute('accept', 'image/*');

            input.onchange = function () {
                var file = this.files[0];

                uploadFile(file, urlUploadImage, function (result) {
                    cb(result.image);
                });
            };

            input.click();
        }
    });
}

/**
 * Upload File
 */
uploadFile = function (file, url, callback) {
    const formData = new FormData();
    formData.append("file", file);
    // Send Ajax Upload File
    $.ajax({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: url,
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            loadingBox('open');
        },
        success: function (result) {
            callback(result);
            loadingBox('close');
        },
        error: function (error) {
            loadingBox('close');
            console.log('Error upload. File: ' + file.name);
        }
    })
}

/**
 * In Array
 */
inArray = function (needle, haystack) {
    var length = haystack.length;
    for(var i = 0; i < length; i++) {
        if(haystack[i] == needle) return true;
    }
    return false;
}

/**
 * Image Preview
 */
imagePreview = function (id, errorLabel = {}, acceptExt = ['image/png','image/jpeg']) {
    inputID = $(id);
    imageBox = inputID.closest('.image-group');
    imageDefault = '/admin_assets/images/default_image.png';

    $('body').on('click', '.image-preview-remove', function (e) {
        e.preventDefault();
        $(this).closest('.image-group').find('.image-preview-src').attr('src', imageDefault);
        $(this).closest('.image-group').find('input[type=file]').val('').attr('value', '');
        $(this).closest('.image-group').find('.image-preview-input').val('');
        $(this).addClass('d-none');
    });

    $('body').on('change', id, function(e) {
        e.preventDefault();
        el = $(this);
        file = this.files[0];
        $(imageBox).find('.error').remove();
        if (file) {
            if (file.size >= UPLOAD_MAX_FILESIZE) {
                el.closest('.image-group').find('.image-group-remove').click();
                $(imageBox).find('.image-button').after('<div class="error">'+errorLabel.filesize+'</div>');
            } else {
                if (inArray(file.type, acceptExt)) {
                    imagePreview = URL.createObjectURL(file);
                    $(imageBox).find('.image-preview-src').attr('src', imagePreview);
                    el.closest('.image-group').find('.image-preview-remove').removeClass('d-none');
                } else {
                    el.closest('.image-group').find('.image-preview-remove').click();
                    $(imageBox).find('.image-button').after('<div class="error">'+errorLabel.extension+'</div>');
                }
            }
        }
    });
}

/**
 * File Preview
 */
filePreview = function (id, errorLabel = {}) {
    inputID = $(id);
    fileBox = inputID.closest('.file-group');

    $('body').on('click', '.file-preview-remove', function (e) {
        e.preventDefault();
        $(this).closest('.file-group').find('.file-preview-src').empty();
        $(this).closest('.file-group').find('input[type=file]').val('').attr('value', '');
        $(this).closest('.file-group').find('.file-preview-input').val('');
        $(this).closest('.file-preview').addClass('d-none');
    });

    $('body').on('change', id, function(e) {
        e.preventDefault();
        el = $(this);
        file = this.files[0];
        console.log(file);
        $(fileBox).find('.error').remove();
        if (file) {
            if (file.size >= UPLOAD_MAX_FILESIZE) {
                el.closest('.file-group').find('.file-group-remove').click();
                $(fileBox).find('.file-preview').after('<div class="error">'+errorLabel.filesize+'</div>');
            } else {
                $(fileBox).find('.file-preview-src').text(file.name);
                el.closest('.file-group').find('.file-preview').removeClass('d-none');
            }
        }
    });
}

/**
 * Star Form
 */
star = function (id) {
    let inputID = $(id);
    let starBox = inputID.closest('.fStar');
    let starGroup = starBox.find('.star-group');

    $('body').on('click', '.star-group i', function (e) {
        e.preventDefault();

        let starItem = '';
        let star = $(this).data('value');

        for (let i = 0; i < star; i++) {
            starItem += `<i class="fa fa-star fz-30px cursor-pointer text-yellow" data-value="${i+1}"></i>`;
        }

        for (let i = 0; i < 5 - star; i++) {
            starItem += `<i class="far fa-star fz-30px cursor-pointer text-yellow" data-value="${star+i+1}"></i>`;
        }

        starBox.find('.error').remove();
        inputID.val(star);
        starGroup.html(starItem);
    });
}

/**
 * Quick Edit
 * @param el
 * @param table
 * @param field
 * @param ids
 * @param permission
 */
quickUpdate = function (el, table, field, ids = [], permission = '') {
    let data = {
        table: table,
        field: field,
        ids: ids,
        value: el.val(),
        permission: permission,
    };
    loadAjaxPost('/admin/ajax/quick-update', data, {
        beforeSend: function(){},
        success:function(result){
            alertText(result.message);
        },
        error: function (error) {}
    });
}

/**
 * Convert to Slug
 * @param text
 */
convertToSlug = function(text = '') {
    return text.toLowerCase()
        .replace(/ /g, '-')
        .replace(/[^\w-]+/g, '');
}

slug = function (slug) {
    $('body').on('change keyup', slug, function () {
        let value = $(this).val();
        value = convertToSlug(value);
        $(this).val(value);
    })
}

/**
 * Convert to Slug
 * @param postCode
 * @param callback
 */
generateAddress = function (postCode = '', callback) {
    let url = '/admin/ajax/generate-address';
    loadAjaxPost(url, { post_code: postCode }, {
        success: function (result) {
            callback(result);
        }
    }, 'none');
}
