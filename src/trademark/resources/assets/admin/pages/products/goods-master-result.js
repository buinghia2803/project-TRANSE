class GoodsMasterResultClass {
    constructor() {
        const self = this
        window.addEventListener('load', function() {
            self.doLoad()
        })
    }

    doLoad() {
        this.sortData()
    }

    sortData() {
        $('.sort-btn').on('click', function(e) {
            e.preventDefault();
            let dataTarget = $(this).data('target')
            let targetSort = $(this).data('sort')
            if(dataTarget && targetSort) {
                $(`#${dataTarget}`).val(targetSort)
            }
            $('#form').submit()
        })
    }
}

new GoodsMasterResultClass();
