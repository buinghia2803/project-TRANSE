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
            alertText(error.responseJSON?.message, 'error')
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
 * Response alert
 */
alertText = function (text = '', type = 'success') {
    switch (type) {
        case 'success':
            console.log(text);
            break;
        case 'info':
            console.log(text);
            break;
        case 'error':
            console.log(text);
            break;
        case 'warning':
            console.log(text);
            break;
    }
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

inArrayFileExt = function (file, extArray) {
    let fileName = file.name;
    let fileExt = fileName.split('.');
    fileExt = fileExt[fileExt.length-1];
    fileExt = fileExt.toLowerCase();

    return inArray(fileExt, extArray);
}

findDuplicates = function (arr) {
    const duplicates = [];
    const seen = {};

    $.each(arr, function (index, value) {
        if (seen[value] === undefined) {
            seen[value] = 1;
        } else {
            if (seen[value] === 1) {
                duplicates.push(value);
            }
            seen[value]++;
        }
    });

    return duplicates;
}

diffArray = function (arr1, arr2) {
    const diff = [];

    // Get item arr1 not in arr2
    const diff1 = $.grep(arr1, function (el) {
        return $.inArray(el, arr2) === -1;
    });

    // Get item arr2 not in arr1
    const diff2 = $.grep(arr2, function (el) {
        return $.inArray(el, arr1) === -1;
    });

    // Merge diff1 and diff2
    $.merge(diff, diff1);
    $.merge(diff, diff2);

    return diff;
}

setSession = function (key, value, callback) {
    loadAjaxPost(SET_SESSION_AJAX_URL, {
        key: key,
        value: value,
    }, {
        beforeSend: function() {},
        success:function(result) {
            callback();
        },
        error: function (error) {}
    });
}

/**
 * Is Valid Product Code
 * @param code string|array product code
 */
isValidProdCode = function (code) {
    let codes = code;
    let regex = /[0-9][0-9][a-zA-Z][0-9][0-9]/g;
    let valid = true;

    // Convert to array when code is string
    if (!Array.isArray(codes)) {
        codes = [ code ];
    }

    $.each(codes, function (index, code) {
        if (code.length != 5) {
            valid = false;
            return false;
        } else if(!regex.test(code)) {
            valid = false;
            return false;
        }
    });

    return valid;
}

/**
 * Is Valid Full width List Prod
 *
 * @param string
 * @returns {boolean}
 */
isValidFullwidthListProd = function (string) {
    let regex = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９　～！＠＃＄％＾＆＊（）＿＋ー＝￥」「；’・。、＜＞？”：｝｛｜－, ，]+$/;
    if(!regex.test(string)) {
        return false;
    }
    return true;
}

/**
 * Download file
 * @param dataUrl string
 * @param fileName string|null
 */
download = function (dataUrl, fileName = null) {
    // Set File Name
    let split = dataUrl.split('/');
    let name = split[split.length - 1];
    if (fileName != null) {
        name = fileName;
    }

    // Create Element and Download
    const link = document.createElement("a");
    link.href = dataUrl;
    link.download = name;
    link.click();
}

/**
 * Open all file attach
 */
openAllFileAttach = function (fileData, field = null) {
    if (field == null) {
        field = '#openAllFileAttach';
    }

    $(field).on('click', function() {
        for (const key in fileData) {
            let data = fileData[key];

            if (data.url) {
                if (data.url.includes(ASSET_URL) == false) {
                    data.url = ASSET_URL + data.url;
                }

                let dataObj = {
                    'name': data.url,
                }

                if (inArrayFileExt(dataObj, ['xml'])) {
                    download(data.url)
                } else {
                    const newTab = window.open(data.url)
                    const error = self.checkPopupBlocker(newTab)
                    if (error) {
                        break;
                    }
                }
            }
        }
    });
}

checkPopupBlocker = function (popupWindow) {
    let error = false
    if (popupWindow) {
        popupWindow.onload = function () {
            if ((popupWindow.innerHeight > 0) == false) {
                error = true;
            }
        };
    } else {
        error = true
    }

    if (error == true) {
        $.confirm({
            title: '',
            content: 'ブロッカーポップアップが有効になっています。このサイトを例外リストに追加し、再度お試しください',
            buttons: {
                ok: {
                    text: 'OK',
                    btnClass: 'btn-blue',
                    action: function () {
                    }
                }
            }
        });
    }

    return error;
}

disabledScreen = function (hasRemoveSubmit = true) {
    const form = $('form').not('#form-logout');
    form.find('input, textarea, select').not('.no_disabled')
        .addClass('disabled')
        .prop('disabled', true)
        .prop('readonly', true);

    form.find('a').not('.no_disabled')
        .addClass('disabled')
        .attr('href', 'javascript:void(0)')
        .attr('target', '')
        .css('pointer-events', 'none');

    form.find('button[type=submit], input[type=submit], button[type=button], input[type=button]').not('.no_disabled')
        .prop('disabled', true)
        .addClass('disabled');

    if(hasRemoveSubmit == true) {
        form.find('button[type=submit], input[type=submit]').not('.no_disabled').remove();
    }
}

scrollToElement = function (element, spacing= 0) {
    window.scroll({
        top: element.offset().top + spacing,
        left: element.offset().left - 20,
        behavior: 'smooth'
    });
}

openNewWindow = function (href, width = 500, height = 500) {
    let windowFeatures = `width=${width},height=${height},toolbar=yes,scrollbars=yes`;
    window.open(href, '_blank', windowFeatures);
}
