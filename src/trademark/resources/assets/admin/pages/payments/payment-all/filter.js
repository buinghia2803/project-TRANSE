
setValueSearchCondition()
searchConditionOption()
disabledSearchValue()

function setValueSearchCondition() {
    if(params && params.search) {
        var dataSearch = params.search
        $.each($('.search-item'), function (index, item) {
            $(item).find('.search_field option').filter(function (idx, option) {
                //1.search_field
                if($(option).val() == dataSearch[index].field) {
                    $(option).prop('selected', true)
                    setTimeout(function () {
                        //2.search_value
                        if ($(option).data('typing') == 'date') {
                            $(item).find('.search_value').attr('type', 'date')
                            $(this).closest('tr').find('.search_condition').html(option);
                        } else {
                            $(item).find('.search_value').attr('type', 'text')
                        }
                        if (dataSearch[index].value) {
                            $(item).find('.search_value').val(dataSearch[index].value)
                        }
                    }, 500);
                } else {
                    $(option).prop('selected', false)
                }
            })
        })
    }
}

//dowload csv
$('body').on('click', '#btn-download-csv', function (e) {
    $('.checkbox-download-csv').prop('checked', true).css('cursor', 'not-allowed');
    $('#form').submit()
    setTimeout(function () {
        $('.checkbox-download-csv').prop('checked', false).css('cursor', 'pointer');
    }, 1000);
});

//click search filters
$('body').on('click', '.submit-search', function (e) {
    setValueSearchCondition()
    searchConditionOption()
});

//on change search_field
$('body').on('change', '.search_field', function (e) {
    e.preventDefault();
    let optionSelected = $(this).find(":selected");
    let typing = optionSelected.data('typing');
    let searchValueEl = $(this).closest('tr').find('.search_value')
    let searchFieldValue = $(this).val()
    if (typing == 'date') {
        searchValueEl.attr('type', 'date').val('');
    } else {
        searchValueEl.attr('type', 'text').val('');
    }
    $(this).closest('tr').find('.typing').val(typing);

    let option = searchConditionOption(typing, searchFieldValue);

    $(this).closest('tr').find('.search_condition').html(option);

    //disabled search_value
    if($(this).val() == '') {
        searchValueEl.addClass('disabled')
    } else {
        searchValueEl.removeClass('disabled')
    }
});

//default disabled input search_value
function disabledSearchValue() {
    $('.search_field').each(function(indx, item) {
        let searchValueEl = $(item).closest('tr').find('.search_value')
        if($(item).val() == '') {
            searchValueEl.addClass('disabled')
        } else {
            searchValueEl.removeClass('disabled')
        }
    });
}

//on change search_value
$('body').on('change keyup', '.search_value', function (e) {
    let value = $(this).val();
    $(this).parent().find('.error').remove();

    if (value.length > 255) {
        $(this).after('<div class="error mt-0">' + errorMessageMaxLength + '</div>');
        //disabled button search
        $('.submit-search').addClass('disabled-btn').prop('disabled', true)
    } else {
        $('.submit-search').removeClass('disabled-btn').prop('disabled', false)
    }
});

$('body').on('click', '#form-search input[type=reset]', function (e) {
    e.preventDefault();
    let form = $(this).closest('form');
    form.trigger('reset');
    $('.search_field').change();
});

//search condition option by type
function searchConditionOption(typing, searchFieldValue) {
    let option = '';
    if (searchFieldValue == '') {
        $.each(conditionsAll, function (key, item) {
            option += `<option value="${key}" >${item}</option>`;
        })
    } else {
        if (typing == 'date') {
            $.each(conditionDate, function (key, item) {
                option += `<option value="${key}" >${item}</option>`;
            })
        } else {
            $.each(conditions, function (key, item) {
                option += `<option value="${key}" >${item}</option>`;
            })
        }
    }

    return option;
}
