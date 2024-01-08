class CreateExamine {
    constructor() {
        this.onChangeComment();
        this.initValidate();
        this.onChangeCheckItem();
        this.onClickSubmit();
        this.showAllCode();
        this.showHideCheckAll();

        $(document).ready(function() {
            $('[data-check_item]').change();
        });
    }

    /**
     * Init validate
     */
    initValidate() {
        // paymentRule, paymentMessage is constant in file payer-info.blade.php
        this.rules = {
            content: {
                maxlength: 1000
            }
        }
        this.messages = {
            content: {
                maxlength: errorMessageMaxLength1000
            }
        }

        new clsValidation('#form', { rules: this.rules, messages: this.messages })
    }

    showHideCheckAll() {
        $.each($('[data-check_all_group]'), function () {
            let checkAllGroup = $(this).data('check_all_group');
            let key = $(this).data('key');

            let checkItem = $(`input[data-check_item=${checkAllGroup}][data-key=${key}]:not(:disabled)`);
            let checkItemOld = $(`span[data-check_item=${checkAllGroup}][data-key=${key}]`);
            let checkItemUnTick = $(`span[data-check_item=${checkAllGroup}][data-key=${key}].un_tick`);

            if (checkItem.length == 0) {
                $(this).addClass('disabled').prop('disabled', true);
            }

            if (checkItemUnTick.length > 0) {
                $(this).parent().empty();
            }

            if (checkItemOld.length > 0) {
                $(this).parent().addClass('text-center').css('vertical-align', 'middle').empty().append('✓');
            }
        });
    }

    onChangeCheckItem() {
        $('body').on('change', '[data-check_item]', function () {
            let allItemGroupChecked = $(this).closest('tr').find('input[data-rank]:checked, span[data-rank]');

            let allRank = [];
            $.each(allItemGroupChecked, function () {
                allRank.push($(this).data('rank'));
            });
            allRank = allRank.sort();

            let rank = allRank[allRank.length - 1];
            let rankHTML = 'ランク：' + rank;
            if(rank == undefined) {
                rank = '';
                rankHTML = '';
            }

            $(this).closest('tr').find('.rank').html(rankHTML);
            $(this).closest('tr').find('.rank_value').val(rank);

            const reasonTable = $('#reason-table');
            reasonTable.parent().find('.error').remove();
        });
    }

    onChangeComment() {
        $('body').on('change keyup', 'textarea.comment_patent_agent', function () {
            let value = $(this).val();

            $(this).closest('td').find('.error').remove();

            if (value.length > 1000) {
                $(this).after(`<div class="error mt-0">${errorMessageMaxLength1000}</div>`)
            }
        })
    }

    onClickSubmit() {
        $('body').on('click', 'input[type=submit]', function (e) {
            const form = $('#form');
            const reasonTable = $('#reason-table');
            let nameBtn = $(this).attr('name');

            reasonTable.parent().find('.error').remove();

            if (inArray(nameBtn, [SUBMIT, SUBMIT_SUPERVISOR])) {
                let productRow = $('tr[data-id]').not('[data-is_complete=1]').not('[data-is_register=0]');
                $.each(productRow, function () {
                    let itemChecked = $(this).find('[data-check_item]:checked');

                    if (itemChecked.length == 0) {
                        reasonTable.parent().find('.error').remove();
                        reasonTable.after(`<p class="error mb-0">${errorMessageRequiredCheckReason}</p>`);
                    }
                })
            }

            let has_error = form.find('.notice:visible,.error:visible');
            if (has_error.length == 0 && form.valid()) {
                form.submit();
            } else {
                e.preventDefault();

                let firstError = has_error.first();
                window.scroll({
                    top: firstError.offset().top - 100,
                    behavior: 'smooth'
                });
            }
        })
    }

    showAllCode() {
        $('body').on('click', '.show_all_code', function (e) {
            e.preventDefault();

            $(this).closest('td').find('.hidden').removeClass('hidden').addClass('d-block');
            $(this).remove();
        })
    }
}

new CreateExamine();
