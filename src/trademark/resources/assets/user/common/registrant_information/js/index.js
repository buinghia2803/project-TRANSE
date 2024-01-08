$('body').on('change', 'input.data-type_acc', function() {
    let name = $(this).attr('name');
    value = $('input[name="' + name + '"]:checked').val();
    $(this).closest('.eTypeAcc').find('.notice').remove();

    if(!value) {
        $(this).closest('.eTypeAcc').find('.fTypeAcc').after('<div class="notice">'+ errorMessageRequired +'</div>')
    }
});

$('body').on('change keyup', 'input.data-name', function() {
    value = $('input.data-name').val();
    $(this).parent().find('.notice').remove();

    regex = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９-]{1,50}$/;

    if(value.length == 0) {
        $(this).after('<div class="notice">'+ errorMessageRequired +'</div>');
    } else if (!regex.test(value)) {
        $(this).after('<div class="notice">'+ errorMessageNameRegex +'</div>');
    }
});

$('body').on('change', 'select.data-m_nation_id', function() {
    value = $('select.data-m_nation_id').val();
    $(this).closest('.eNation').find('.notice').remove();

    if(!value) {
        $(this).closest('.eNation').append('<div class="notice">'+ errorMessageRequired +'</div>')
    }
});

$('body').on('change', 'select.data-m_prefecture_id', function() {
    value = $('select.data-m_prefecture_id').val();
    $(this).closest('.ePerfecture').find('.notice').remove();

    if(!value) {
        $(this).closest('.ePerfecture').append('<div class="notice">'+ errorMessageRequired +'</div>')
    }
});

$('body').on('change keyup', '.data-address_second', function() {
    value = $('.data-address_second').val();
    $(this).parent().find('.notice').remove();

    regex = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９-]{0,100}$/;

    if(value.length == 0) {
        $(this).after('<div class="notice">'+ errorMessageRequired +'</div>');
    } else if (!regex.test(value)) {
        $(this).after('<div class="notice">'+ errorMessageAddressRegex +'</div>');
    }
});

$('body').on('change keyup', '.data-address_three', function() {
    value = $('.data-address_three').val();
    $(this).parent().find('.notice').remove();

    regex = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９-]{0,100}$/;

    if (!regex.test(value)) {
        $(this).after('<div class="notice">'+ errorMessageAddressRegex +'</div>');
    }
});

$('body').on('click', 'input[type=submit]', function(e) {
    e.preventDefault();
    $('input[class=data-name]').change();
    $('input[class=data-type_acc]').change();
    $('select.data-m_nation_id').change();
    $('select.data-m_prefecture_id').change();
    $('.data-address_second').change();

    form = $(this).closest('form');
    form.valid()
    if(form.find('.notice').length == 0) {
        setTimeout(() => {
            form.submit();
        }, 200);
    }
});

$("#btn-click-copy").click(function() {
    $.ajax({
        url: "{{ route('user.get-data-click-copy') }}",
        type: "post",
        data: {
            _token: "{{ csrf_token() }}",
        },
        success: function(data) {
            const {
                info_type_acc,
                info_name,
                info_nation_id,
                info_prefectures_id,
                info_address_second,
                info_address_three
            } = data.data;

            $("input[name=type_acc]").filter(`[value=${info_type_acc}]`).prop("checked", true);
            $("input[name=name]").val(info_name);
            $(`select#m_nation_id option[value='${info_nation_id}']`).prop("selected", true);
            $(`select#m_prefecture_id option[value='${info_prefectures_id}']`).prop("selected", true);
            $("input[name=address_second]").val(info_address_second);
            $("input[name=address_three]").val(info_address_three);
        },
    });
})
