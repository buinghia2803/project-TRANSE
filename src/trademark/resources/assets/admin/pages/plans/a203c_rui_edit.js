class PlanProductGroupEdit {
    constructor() {
        const self = this
        this.valid = false
        this.deletedData = []
        window.addEventListener('load', function () {
            self.doLoad()
        })
    }

    /**
     * When load page.
     */
    doLoad() {
        this.clickAddProduct()
        this.clickAddDistinct()
        this.onChangeProductName()
        this.clickDeleteRow()
        this.setDataSession()
        this.preSubmit()
    }

    setDataSession() {
        if (dataSession == null) {
            return false;
        }

        let products = dataSession.products ?? [];
        $.each(products, function (index, element) {
            if(element && element.is_add_distinct) {
                let rowID = self.uniqueID();
                let rowHTML = distinctHTML;

                let bgClass = '';
                let roleAdd = element.role_add;
                if(roleAdd ==  ROLE_MANAGER) {
                    bgClass = 'bg_yellow'
                } else if(roleAdd == ROLE_SUPERVISOR) {
                    bgClass = 'bg_purple2'
                }

                const codeName = element.m_code_name;
                const codeId = element.m_code_id;
                const productName = element.product_name;

                rowHTML = rowHTML.replaceAll('{roleAdd}', roleAdd);
                rowHTML = rowHTML.replaceAll('{bgClass}', bgClass);
                rowHTML = rowHTML.replaceAll('{codeId}', codeId);
                rowHTML = rowHTML.replaceAll('{codeName}', codeName);
                rowHTML = rowHTML.replaceAll('{rowID}', rowID);
                rowHTML = rowHTML.replaceAll('{index}', rowID);
                rowHTML = rowHTML.replaceAll('{productName}', productName);

                $('tr[data-row], tr.item').last().after(rowHTML);

                $(`[name="products[${rowID}][m_distinction_id]"]`).val(element.m_distinction_id)

                $.each(element.plan_details, function (index, item) {
                    let leaveStatus = item.leave_status ?? null;
                    let leaveStatusOther = item.leave_status_other ?? null;
                    if(leaveStatus != null) {
                        $(`[name="products[${rowID}][plan_details][${index}][leave_status]"]`).val(leaveStatus);
                    } else if (leaveStatusOther != null) {
                        $.each(leaveStatusOther, function (index2, item2) {
                            $(`[name="products[${rowID}][plan_details][${index}][leave_status_other][${index2}]"]`).val(item2);
                        })
                    }
                });
            } else if (element && element.is_add_product) {
                let rowID = self.uniqueID();
                let rowHTML = productHTML;
                let distinctionName = element.m_distinction_name ?? null;
                let mDistinctionId = element.m_distinction_id ?? null;
                let planDetailDistinctionID = element.plan_detail_distinct_id;

                let bgClass = '';
                let roleAdd = element.role_add;
                if(roleAdd ==  ROLE_MANAGER) {
                    bgClass = 'bg_yellow'
                } else if(roleAdd == ROLE_SUPERVISOR) {
                    bgClass = 'bg_purple2'
                }

                const codeName = element.m_code_name;
                const codeId = element.m_code_id;
                const productName = element.product_name;

                rowHTML = rowHTML.replaceAll('{rowID}', rowID);
                rowHTML = rowHTML.replaceAll('{roleAdd}', roleAdd);
                rowHTML = rowHTML.replaceAll('{bgClass}', bgClass);
                rowHTML = rowHTML.replaceAll('{codeId}', codeId);
                rowHTML = rowHTML.replaceAll('{codeName}', codeName);
                rowHTML = rowHTML.replaceAll('{productName}', productName);
                rowHTML = rowHTML.replaceAll('{distinctionName}', distinctionName);
                rowHTML = rowHTML.replaceAll('{distinctionID}', mDistinctionId);
                rowHTML = rowHTML.replaceAll('{planDetailDistinctionID}', planDetailDistinctionID);
                rowHTML = rowHTML.replaceAll('{index}', rowID);

                $('.item.is-user').last().after(rowHTML);

                $.each(element.plan_details, function (index, item) {
                    let leaveStatus = item.leave_status ?? null;
                    let leaveStatusOther = item.leave_status_other ?? null;
                    if(leaveStatus != null) {
                        $(`[name="products[${rowID}][plan_details][${index}][leave_status]"]`).val(leaveStatus);
                    } else if (leaveStatusOther != null) {
                        $.each(leaveStatusOther, function (index2, item2) {
                            $(`[name="products[${rowID}][plan_details][${index}][leave_status_other][${index2}]"]`).val(item2);
                        })
                    }
                });
            }
        });
    }

    /**
     * Generate unique index.
     *
     * @returns {Number}
     */
    uniqueID() {
        return Math.floor(Math.random() * (10000000 - 1 + 1) + 1);
    }

    /**
     * Handling event product name.
     */
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

    /**
     * Handling event click button add new product.
     */
    clickAddProduct() {
        const self = this;
        $('body').on('click', '[data-add_product]', function (e) {
            e.preventDefault();

            let info = $(this).data('info');
            let rowID = self.uniqueID();
            let rowHTML = productHTML;
            let distinctionName = info.distinction.name;
            let mDistinctionId = info.distinction.id;
            let planDetailDistinctionID = info.distinction.plan_detail_distinction;

            let bgClass = '';
            let roleAdd = user.role;
            if(user.role ==  ROLE_MANAGER) {
                bgClass = 'bg_yellow'
            } else if(user.role == ROLE_SUPERVISOR) {
                bgClass = 'bg_purple2'
            }

            const codeName = info.distinction.code_name
            const codeId = info.distinction.m_code_id
            const productName = info.distinction.product_name

            rowHTML = rowHTML.replaceAll('{rowID}', rowID);
            rowHTML = rowHTML.replaceAll('{roleAdd}', roleAdd);
            rowHTML = rowHTML.replaceAll('{bgClass}', bgClass);
            rowHTML = rowHTML.replaceAll('{codeId}', codeId);
            rowHTML = rowHTML.replaceAll('{codeName}', codeName);
            rowHTML = rowHTML.replaceAll('{productName}', productName);
            rowHTML = rowHTML.replaceAll('{distinctionName}', distinctionName);
            rowHTML = rowHTML.replaceAll('{distinctionID}', mDistinctionId);
            rowHTML = rowHTML.replaceAll('{planDetailDistinctionID}', planDetailDistinctionID);
            rowHTML = rowHTML.replaceAll('{index}', rowID);

            $(this).closest('tr').after(rowHTML);
        });
    }

    /**
     * Handling event click button add new distinction.
     */
    clickAddDistinct() {
        self = this;

        $('body').on('click', '[data-add_distinct]', function (e) {
            e.preventDefault();

            let info = $(this).data('info');
            let rowID = self.uniqueID();
            let rowHTML = distinctHTML;

            let bgClass = '';
            let roleAdd = user.role;
            if(user.role ==  ROLE_MANAGER) {
                bgClass = 'bg_yellow'
            } else if(user.role == ROLE_SUPERVISOR) {
                bgClass = 'bg_purple2'
            }

            const codeName = info.distinction.code_name
            const codeId = info.distinction.m_code_id
            const productName = info.distinction.product_name

            rowHTML = rowHTML.replaceAll('{roleAdd}', roleAdd);
            rowHTML = rowHTML.replaceAll('{bgClass}', bgClass);
            rowHTML = rowHTML.replaceAll('{codeId}', codeId);
            rowHTML = rowHTML.replaceAll('{codeName}', codeName);
            rowHTML = rowHTML.replaceAll('{rowID}', rowID);
            rowHTML = rowHTML.replaceAll('{index}', rowID);
            rowHTML = rowHTML.replaceAll('{productName}', productName);

            $(this).closest('tbody').append(rowHTML);

            // Scroll to new Distinct
            let firstEL = $('tr[data-row=' + rowID + ']').first();
            window.scroll({
                top: firstEL.offset().top - 100,
                behavior: 'smooth'
            });
        });
    }

    /**
     * Handling event delete row.
     */
    clickDeleteRow() {
        $('body').on('click', '[data-delete_row]', function (e) {
            e.preventDefault();
            let row = $(this).closest('tr').data('row');

            let inputDelete = $('input[name=delete_plan_detail_product_ids]');
            let inputDeleteValue = inputDelete.val();
            let deleteIds = inputDeleteValue.length > 0 ? inputDeleteValue.split(',') : [];

            let planDetail = $('tr[data-row=' + row + ']').find('td[data-plan_detail_product_id]');
            $.each(planDetail, function (index, item) {
                deleteIds.push($(this).data('plan_detail_product_id'));
            });

            deleteIds = deleteIds.join(',');
            inputDelete.val(deleteIds);

            $('tr[data-row=' + row + ']').remove();
        });
    }

    /**
     * Handling data before submit.
     */
    preSubmit() {
        $('body').on('click', '#gotoA203c_rui_edit02', function (e) {
            $('[data-input_product_name]').change();

            let form = $(this).closest('form');
            form.valid();

            let hasError = form.find('.error-validate:visible,.notice:visible,.error:visible');
            if (hasError.length == 0 && form.valid()) {
                form.submit();
            } else {
                let firstError = form.find('.error-validate:visible,.notice:visible,.error:visible').first();
                window.scroll({
                    top: firstError.offset().top - 100,
                    behavior: 'smooth'
                });
            }
        })
    }
}

new PlanProductGroupEdit();
