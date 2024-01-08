$('body').on('change', '.search_field', function (e) {
    e.preventDefault();

    let optionSelected = $(this).find(":selected");
    let typing = optionSelected.data('typing');

    if (typing == 'date') {
        $(this).closest('tr').find('.search_value').attr('type', 'date').val('');
    } else {
        $(this).closest('tr').find('.search_value').attr('type', 'text').val('');
    }

    let option = searchConditionOption(typing);
    $(this).closest('tr').find('.search_condition').html(option);
});

$('body').on('change keyup', '.search_value', function (e) {
    let value = $(this).val();
    $(this).parent().find('.error').remove();

    if (value.length > 255) {
        $(this).after('<div class="error mt-0">' + errorMessageMaxLength + '</div>');
    }
});

setTimeout(function () {
    $.each($('.search_field'), function () {
        let option = searchFieldOption();
        $(this).html(option);
        $(this).change();
    });
}, 200);

$('body').on('click', '#submit-search', function (e) {
    e.preventDefault();

    let form = $(this).closest('form');
    let hasError = form.find('.error:visible').length;
    if (hasError > 0) {
        return false;
    }

    let searchHasClose = false;
    let hasClose = $('input[name=has_close]').prop('checked');
    if (hasClose == true) {
        searchHasClose = true;
    }

    let searchType = $('input[name=type_search]:checked').val();
    let searchData = [];

    $.each($('.search-item') , function () {
        let field = $(this).find('.search_field').val();
        let value = $(this).find('.search_value').val();
        let condition = $(this).find('.search_condition').val();

        searchData.push({
            'field': field,
            'value': value,
            'condition': condition,
        });
    })

    setSession(SESSION_SEARCH_TOP, {
        searchHasClose: searchHasClose,
        searchType: searchType,
        searchData: searchData
    }, function () {
        window.location = URL_LIST_ANKEN;
    });
});

$('body').on('click', '#form-search input[type=reset]', function (e) {
    e.preventDefault();
    let form = $(this).closest('form');
    form.trigger('reset');
    $('.search_field').change();
});

function searchFieldOption() {
    let option = '';

    $.each(searchFields, function (key, item) {
        if(item.typing == undefined) {
            item.typing = 'text';
        }
        option += `<option value="${key}" data-typing="${item.typing}">${item.title}</option>`
    })

    return option;
}

function searchConditionOption(typing) {
    let option = '';

    if (typing == 'date') {
        $.each(conditionDate, function (key, item) {
            option += `<option value="${key}" >${item}</option>`;
        })
    } else {
        $.each(conditions, function (key, item) {
            option += `<option value="${key}" >${item}</option>`;
        })
    }

    return option;
}
