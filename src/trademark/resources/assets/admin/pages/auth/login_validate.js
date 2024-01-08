function validate() {
    validation('#form', {
        'admin_number': {
            required: true,
            isValidInfoMemberId: true
        },
        'password': {
            required: true,
            isValidInfoPassword : true,
        },
    }, {
        'admin_number': {
            required: errorMessageFormatRequired,
            isValidInfoMemberId: errorMessageFormatMemberId,
        },
        'password': {
            required: errorMessageFormatRequired,
            isValidInfoPassword: errorMessageFormatPassword
        },
    })
}
validate()
$('#btn-clear').click(function() {
    $('#password').val('')
    $('#admin_number').val('')
    $('#form').validate().destroy()
    validate()
})
