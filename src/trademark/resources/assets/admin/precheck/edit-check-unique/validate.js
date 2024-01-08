$(document).ready(function () {
    $("#form").validate({
        rules: {
            'content1': {
                maxlength: 1000
            },
            'content2': {
                maxlength: 1000,
            },
        },
        messages: {
            'content1': {
                maxlength: errorMessageContentMaxLength,
            },
            'content2': {
                maxlength: errorMessageContentMaxLength,
            },
        },
        errorElement: "div",
    });
});
