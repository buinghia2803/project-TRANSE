$('#trademark_name').on('input', function() {
    if(/\s/g.test($('#trademark_name').val())) {
        $('#trademark_name').val($('#trademark_name').val().trim())
    }
})
$('#reference_number').on('input', function() {
    if(/\s/g.test($('#reference_number').val())) {
        $('#reference_number').val($('#reference_number').val().trim())
    }
})

$('input[name=type_trademark]').change(function () {
    const value = $(this).val()
    if(value == 1) {
        $('.dd_image_trademark').hide();
        $('.dd_name_trademark').show()
    } else {
        $('.dd_name_trademark').hide();
        $('.dd_image_trademark').show()
    }
})
$('input[name=type_trademark]:checked').change()
