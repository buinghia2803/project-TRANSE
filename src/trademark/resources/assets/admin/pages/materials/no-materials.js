class NoMaterial {
    constructor() {
        this.showAllCode();
    }

    disableInput() {
        const form = $('form');
        form.find('input:not([type="submit"]), textarea, select').prop('readonly', true);
        form.find('input, textarea, select , button').not('.no_disabled').prop('disabled', true);
        form.find('button[type=submit], input[type=submit]').prop('disabled', true).css('display', 'none').remove();
        form.find('a').css('pointer-events', 'none');
        $('[type=submit]').remove();
    }

    showAllCode() {
        $('body').on('click', '.show_all_code', function (e) {
            e.preventDefault();

            $(this).closest('.code-block').find('.hidden').removeClass('hidden');
            $(this).remove();
        })
    }
}

new NoMaterial;
