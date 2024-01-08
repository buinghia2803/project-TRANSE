let rules = {}
let messages = {}

$('input[type=file], textarea:not(hidden)').each(function (key, item) {
    //if item is textarea answer
    if($(item).hasClass('textarea-answer')) {
        rules[$(item).attr('name')] = {
            required: true,
            maxlength: 500
        }

        messages[$(item).attr('name')] = {
            required: errorMessageRequired,
            maxlength: errorMessageMaxLength500,
        }

    }
})

validation('#form', rules, messages)

