class PlanProductCreate {
    constructor() {
        this.initValidate();
        this.clickDeleteRow();
        this.clickEditCode();
        this.clickAddProduct();
        this.clickAddDistinct();
        this.onChangeProductName();
        this.onChangeProductCode();
        this.onClickSubmit();
        this.showAllCode();
        this.validateDistinct();
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
        this.rules = {...common203Rule, ...this.rules};

        this.messages = {
            'content': {
                maxlengthTextarea: errorMessageMaxLength1000,
            },
        }
        this.messages = {...common203Message, ...this.messages};

        new clsValidation('#form', {rules: this.rules, messages: this.messages})
    }

    uniqueID() {
        return Math.floor(Math.random() * (10000000 - 1 + 1) + 1);
    }

    handleRow(row, action) {
        let inputDelete = $('input[name=delete_plan_detail_product_ids]');
        let inputDeleteValue = inputDelete.val();
        let deleteIds = inputDeleteValue.length > 0 ? inputDeleteValue.split(',') : [];

        let inputRestore = $('input[name=restore_plan_detail_product_ids]');
        let inputRestoreValue = inputRestore.val();
        let restoreIds = inputRestoreValue.length > 0 ? inputRestoreValue.split(',') : [];

        let planDetail = $('tr[data-row=' + row + ']').find('td[data-plan_detail_product_id]');
        $.each(planDetail, function (index, item) {
            let planDetailProductID = $(this).data('plan_detail_product_id');

            if (action == 'delete_row') {
                if (!inArray(planDetailProductID, deleteIds)) {
                    deleteIds.push(planDetailProductID);

                    restoreIds = $.grep(restoreIds, function(value) {
                        return value != planDetailProductID;
                    });
                }
            } else if (action == 'restore_row') {
                if (!inArray(planDetailProductID, restoreIds)) {
                    restoreIds.push(planDetailProductID)

                    deleteIds = $.grep(deleteIds, function(value) {
                        return value != planDetailProductID;
                    });
                }
            }
        });

        deleteIds = deleteIds.join(',');
        inputDelete.val(deleteIds);

        restoreIds = restoreIds.join(',');
        inputRestore.val(restoreIds);
    }

    clickDeleteRow() {
        self = $(this);

        $('body').on('click', '[data-delete_row]', function (e) {
            e.preventDefault();
            let trEl = $(this).closest('tr');
            let row = trEl.data('row');

            self.handleRow(row, 'delete_row');

            if (trEl.hasClass('is-manager')) {
                $('tr[data-row=' + row + ']').find('td').removeClass('bg_yellow').addClass('bg_gray');
                $(this).val(LABEL_RESTORE);
                $(this).removeAttr('data-delete_row');
                $(this).attr('data-restore_row', '');
            } else {
                $('tr[data-row=' + row + ']').remove();
            }
        });

        $('body').on('click', '[data-restore_row]', function (e) {
            let trEl = $(this).closest('tr');
            let row = trEl.data('row');

            self.handleRow(row, 'restore_row');

            if (trEl.hasClass('is-manager')) {
                $('tr[data-row=' + row + ']').find('td').removeClass('bg_gray').addClass('bg_yellow');
                $(this).val(LABEL_DELETE_ALL);
                $(this).removeAttr('data-restore_row');
                $(this).attr('data-delete_row', '');
            }
        });
    }

    clickEditCode() {
        $('body').on('click', '[data-edit_code]', function (e) {
            e.preventDefault();

            $(this).closest('td').find('[data-input_product_code]').removeClass('hidden');

            $(this).closest('td').find('[data-type=3]').remove();
            $(this).closest('td').find('[data-type=4]').remove();

            $(this).remove();
        });
    }

    clickAddProduct() {
        self = this;

        $('body').on('click', '[data-add_product]', function (e) {
            e.preventDefault();

            let info = $(this).data('info');
            let rowID = self.uniqueID();
            let rowHTML = productHTML;

            rowHTML = rowHTML.replaceAll('{rowID}', rowID);
            rowHTML = rowHTML.replaceAll('{distinctionName}', info.distinction.name);
            rowHTML = rowHTML.replaceAll('{planDetailDistinctionID}', info.distinction.plan_detail_distinction);
            rowHTML = rowHTML.replaceAll('{distinctionID}', info.distinction.id);
            rowHTML = rowHTML.replaceAll('{index}', rowID);
            rowHTML = rowHTML.replaceAll('{productName}', info.distinction.product_name);

            $(this).closest('tr').after(rowHTML);
        });
    }

    clickAddDistinct() {
        self = this;

        $('body').on('click', '[data-add_distinct]', function (e) {
            e.preventDefault();

            let info = $(this).data('info');
            let rowID = self.uniqueID();
            let rowHTML = distinctHTML;

            rowHTML = rowHTML.replaceAll('{rowID}', rowID);
            rowHTML = rowHTML.replaceAll('{index}', rowID);
            rowHTML = rowHTML.replaceAll('{productName}', info.distinction.product_name);

            $(this).closest('tbody').append(rowHTML);

            // Scroll to new Distinct
            let firstEL = $('tr[data-row=' + rowID + ']').first();
            window.scroll({
                top: firstEL.offset().top - 100,
                behavior: 'smooth'
            });
        });
    }

    onChangeProductName() {
        $('body').on('change keyup', '[data-input_product_name]', function (e) {
            e.preventDefault();
            let value = $(this).val();
            let regex = /^[ぁ-んァ-ン一-龥ａ-ｚＡ-Ｚ０-９々ー・。（）－]+$/;

            $(this).parent().find('.error').remove();
            if (value.length == 0) {
                $(this).after(`<div class="error mt-0">${errorMessageRequired}</div>`);
            } else if (value.length > 200) {
                $(this).after(`<div class="error mt-0">${errorMessageMaxLength200}</div>`);
            } else if(!regex.test(value)) {
                $(this).after(`<div class="error mt-0">${errorMessageFormat}</div>`);
            }
        })
    }

    onChangeProductCode() {
        $('body').on('change keyup', '[data-input_product_code]', function (e) {
            e.preventDefault();
            let el = $(this);

            let value = $(this).val();
            value = value.replaceAll('  ', ' ');
            $(this).val(value);
            $(this).parent().find('.error').remove();

            if (value.length == 0) {
                el.after(`<div class="error mt-0">${errorMessageRequired}</div>`);
                return false;
            }

            let valueArray = value.split(" ");

            if(valueArray.length > 50) {
                el.after(`<div class="error mt-0">${errorMessageMax50Code}</div>`);
                return false;
            }

            $.each(valueArray, function (index, item) {
                if(!isValidProdCode(item)) {
                    el.after(`<div class="error mt-0">${errorMessageFormatCode}</div>`);
                    return false;
                }
            });
        });
    }

    validateDistinct() {
        $('body').on('change', '[data-select_distinct]', function (e) {
            e.preventDefault();
            let value = $(this).val();
            $(this).parent().find('.error').remove();
            if (value === '') {
                $(this).after(`<div class="error mt-0 mb-0">${errorMessageRequired}</div>`);
            }
        })
    }

    onClickSubmit() {
        $('body').on('click', 'input[type=submit],button[type=submit]', function (e) {
            const form = $('#form');

            $('[data-input_product_name]:visible').change();
            $('[data-input_product_code]:visible').change();
            $('[data-select_distinct]:visible').change();
            $('#datepicker').change();

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
        });
    }

    showAllCode() {
        $('body').on('click', '.show_all_code', function (e) {
            e.preventDefault();

            $(this).closest('.code-block').find('.hidden').removeClass('hidden');
            $(this).remove();
        })
    }
}

new PlanProductCreate;
