class Supervisor {
    constructor() {
        this.initValidate();
        this.onchangeDocIsComplete();
        this.onchangePlanIsComplete();
        this.onClickSubmit();
    }

    /**
     * Init validate
     */
    initValidate() {
        // common203Rule, common203Message is constant in file common
        this.rules = {
            'content': {
                maxlengthTextarea: 1000,
            },
        }

        this.messages = {
            'content': {
                maxlengthTextarea: errorMessageMaxLength1000,
            },
        }

        new clsValidation('#form', {rules: this.rules, messages: this.messages})
    }

    onchangeDocIsComplete() {
        $('body').on('change', '[data-doc_is_complete]', function (e) {
            let planItem = $(this).closest('.plan-item');
            let isChecked = $(this).prop('checked');

            planItem.find('.error').remove();

            if (isChecked == false) {
                planItem.find('[data-plan_is_complete]').prop('checked', false);

                planItem.find('.doc-table').removeClass('reject');
            }
        });
    }

    onchangePlanIsComplete() {
        $('body').on('change', '[data-plan_is_complete]', function (e) {
            let el = $(this);
            let planItem = $(this).closest('.plan-item');
            let totalDocIsComplete = planItem.find('[data-doc_is_complete]');
            let totalDocIsCompleteChecked = planItem.find('[data-doc_is_complete]:checked');

            planItem.find('.error').remove();

            if ($(this).prop('checked')) {
                planItem.find('.doc-table').addClass('reject');
            } else {
                planItem.find('.doc-table').removeClass('reject');
            }

            if (totalDocIsComplete.length != totalDocIsCompleteChecked.length) {
                $.confirm({
                    title: '',
                    content: errorMessageRequiredDocComplete,
                    buttons: {
                        ok: {
                            text: 'OK',
                            btnClass: 'btn-blue',
                            action: function () {
                                el.prop('checked', false);

                                planItem.find('.doc-table').removeClass('reject');
                                scrollToElement(planItem.find('.doc-table'), -20);
                            }
                        }
                    }
                });
            }
        });
    }

    onClickSubmit() {
        $('body').on('click', 'input[type=submit]', function (e) {
            const form = $('#form');
            const name = $(this).attr('name');

            $('.plan-item').find('.error').remove();

            if (name == SAVE) {
                let totalDocIsComplete = $('[data-doc_is_complete]');
                let totalDocIsCompleteChecked = $('[data-doc_is_complete]:checked');

                let totalPlanIsComplete = $('[data-plan_is_complete]');
                let totalPlanIsCompleteChecked = $('[data-plan_is_complete]:checked');

                if (totalDocIsComplete.length > 0 && totalDocIsCompleteChecked.length >= totalDocIsComplete.length
                    || totalPlanIsComplete.length > 0 && totalPlanIsCompleteChecked.length >= totalPlanIsComplete.length
                ) {
                    $.alert(errorMessageRequiredSave);
                    return false;
                }
            } else if (name == SUBMIT) {
                $.each($('.plan-item'), function () {
                    let totalDocIsComplete = $(this).find('[data-doc_is_complete]');
                    let totalDocIsCompleteChecked = $(this).find('[data-doc_is_complete]:checked');

                    let totalPlanIsComplete = $(this).find('[data-plan_is_complete]');
                    let totalPlanIsCompleteChecked = $(this).find('[data-plan_is_complete]:checked');

                    if (totalDocIsComplete.length != totalDocIsCompleteChecked.length) {
                        $(this).find('.doc-table').after(`<span class="error">${Common_E025}</span>`);
                    }

                    if (totalPlanIsComplete.length != totalPlanIsCompleteChecked.length) {
                        $(this).find('[data-plan_is_complete]').closest('label').after(`<span class="error d-block">${Common_E025}</span>`);
                    }
                });
            }

            let has_error = form.find('.notice:visible,.error:visible');
            if (has_error.length == 0 && form.valid()) {
                form.submit();
            } else {
                let firstError = has_error.first();
                scrollToElement(firstError, -100);
                return false;
            }
        });
    }
}

new Supervisor;
