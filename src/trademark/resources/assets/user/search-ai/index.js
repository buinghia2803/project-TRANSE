$(document).ready(function () {
    // Add More Keyword Form
    $('body').on('click', '#addMoreKeyword', function (e) {
        e.preventDefault();
        // Append HTML
        $('.list-keyword').append('<li><input type="text" class="em30" name="keyword[]"/></li>');
        // Remove Button
        let itemLength = $('.list-keyword').find('li').length;
        if (itemLength >= MAX_KEYWORD_FORM) {
            $(this).closest('.add-more').remove();
        }
    });

    // Validate form
    validation('#form', {
        name_trademark: {
            isOnlySpaceNameFullwidth: true,
            maxlength: 30,
        },
        image_trademark: {
            formatFile: true,
            formatFileSize: 3
        },
    }, {
        name_trademark: {
            isOnlySpaceNameFullwidth: errorMessageIsFullWidth,
            maxlength: errorMessageIsFullWidth,
        },
        image_trademark: {
            formatFile: errorMessageImageTrademarkFormat,
            formatFileSize: errorMessageImageTrademarkFormat
        }
    });

    // Validate Keyword
    $('body').on('change keyup', 'input[name^=keyword]', function (e) {
         e.preventDefault();
         let value = $(this).val();
        value = value.replace(' ', '');
        value = value.replace('　', '');
        $(this).val(value);
        $(this).closest('li').find('.error').remove();
        if (value.length != 0) {
            let regex = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９　～！＠＃＄％＾＆＊（）＿＋ー＝￥」「；’・。、＜＞？”：｝｛｜]+$/;
            if (!regex.test(value) || value.length > 25) {
                $(this).after(`<div class="error mt-0">${errorMessageIsFullWidthKeyword}</div>`);
            }
        }
    });

    // Reset Button
    $('body').on('click', 'input[type=reset]', function () {
        $(this).closest('form').find('.notice,.error').remove();
        $.each($('input[name^=keyword]'), function () {
            $(this).prop('defaultValue', '')
        });
        history.replaceState({}, document.title, window.location.pathname);
    });

    // Open Modal
    $('body').on('click', '[data-open_modal]', function (e) {
        e.preventDefault();
        let modalID = $(this).data('open_modal');
        let modalBox = $(modalID);

        let iframe = modalBox.find('iframe');
        let iframeSrc = iframe.attr('src');
        if (iframeSrc.length == 0) {
            iframe.attr('src', iframe.data('src'));
        }

        openModal(modalID);
    });
})

function appendKeyword(keywordData = []) {
    let modalBox = $('#u031pass-modal');
    let iframe = modalBox.find('iframe');
    iframe.attr('src', '');

    let keywordArray = [];

    // Push current keyword
    $.each($('input[name^=keyword]'), function () {
        let keyword = $(this).val();
        if (keyword.length > 0) {
            keywordArray.push(keyword);
        }
    });

    // Push new keyword
    $.each(keywordData, function (index, item) {
        if (item.length > 0) {
            keywordArray.push(item);
        }
    })

    // Remove current input
    $.each($('input[name^=keyword]'), function () {
        $(this).closest('li').remove();
    });

    // Generate new input
    let keywordHTML = '';
    $.each(keywordArray, function (index, item) {
        if (index < MAX_KEYWORD_FORM) {
            keywordHTML += `<li><input type="text" class="em30" name="keyword[]" value="${item}"/></li>`;
        }
    });

    // Append new list keyword
    $('.list-keyword').append(keywordHTML);
    $('input[name^=keyword]').keyup();

    // Remove Button Add more
    let itemLength = $('.list-keyword').find('li').length;
    if (itemLength >= MAX_KEYWORD_FORM) {
        $('#addMoreKeyword').closest('.add-more').remove();
    }
}
