class GoodMasterSearchClass {
    constructor() {
        const self = this
        this.initValidate()

        window.addEventListener('load', function() {
            self.doLoad()
        })
    }

    doLoad() {
        this.initValidate()
        this.onChangeFieldSearch()
        this.onChangeValueSearch()
        this.submitForm()
    }

    submitForm() {
        const self = this;
        $('body').on('click', 'input[type=submit],button[type=submit]', function(e) {
            e.preventDefault();
            self.initValidate()
            const form = $('#form');
            let hasError = form.find('.notice:visible,.error:visible');
            if (hasError.length == 0) {
                form.submit();
            } else {
                let firstError = hasError.first();
                window.scroll({
                    top: firstError.offset().top - 100,
                    behavior: 'smooth'
                });
            }
        });
    }

    initValidate() {
        $('.row-data').each(function(index, el) {
            let fieldSearch = $(el).find('.field_search').val()
            let valueSearch = $(el).find('.value_search').val()
            let conditionSearch = $(el).find('.condition_search').val()
            $(el).closest('tr').find('.error').remove();
            if(valueSearch.length) {
                if(valueSearch.length > 255) {
                    $(el).find('.value_search').after(`<div class="error">${Common_E021}</div>`);
                } else if(fieldSearch == searchCodeName) {
                    if(!isValidProdCode(valueSearch)) {
                        $(el).find('.value_search').after(`<div class="error">${support_A011_E003}</div>`);
                    }
                }
            }
        });
    }

    onChangeValueSearch() {
        const self = this
        $('.value_search').on('change', function() {
            self.initValidate()
        });
    }

    onChangeFieldSearch() {
        const self = this
        $('.field_search').on('change', function() {
            let elementRow = $(this).closest('.row-data')
            let typeSearch = parseInt($(this).val());
            let option = self.getOptionConditionSearch(typeSearch)
            elementRow.find('.condition_search').html(option)

            elementRow.find('.value_search').val('')
            elementRow.find('.condition_search').val(equalLabel)
            self.initValidate()
        })
    }

    getOptionConditionSearch(typeSearch) {
        let option = '';
        if(typeSearch && (typeSearch == searchDistinctionName || typeSearch == searchConcept)) {
            $.each(dataConditionCompare, function (key, item) {
                option += `<option value="${key}" >${item}</option>`;
            })
        } else {
            $.each(dataConditionFilter, function (key, item) {
                option += `<option value="${key}">${item}</option>`;
            })
        }

        return option;
    }
}

new GoodMasterSearchClass();
