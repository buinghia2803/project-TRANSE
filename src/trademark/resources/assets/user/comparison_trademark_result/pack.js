class comparisonTrademarkResult {
    constructor() {
        const self = this
        window.addEventListener('load', function () {
            self.doLoad();
        })
    }

    /**
    * Do load all action and element.
    */
    doLoad() {
        this.clickFilePDF()
        this.redirectToU201AWindow()
        this.submitForm()
    }

    clickFilePDF() {
        $('#click_file_pdf').click(function () {
            const a = $('.click_ele_a')
            for (const object of a) {
                object.click()
            }
        })
    }

    redirectToU201AWindow() {
        $('#redirect_to_u201a_window').on('click', function () {
            arrayProductSelect = []
            isChoiUser = []
            openModal('#u201a_window-modal');
        })
    }

    submitForm() {
        $('.submit_form').click(function () {
            loadAjaxPost(routeSubmit, {
                comparison_trademark_result_id: $("input[name=comparison_trademark_result_id]").val(),
                trademark_id: $("input[name=trademark_id]").val(),
            }, {
                beforeSend: function(){},
                success:function(result){
                    $.alert({
                        title: '',
                        content: '拒絶理由通知対応を申し込みました',
                        buttons: {
                            OK: {
                                text: '案件トップページへ戻る',
                                btnClass: 'btn-blue',
                                action: function(){
                                    window.location.href = routeApplicationDetail
                                }
                            }
                        }
                    });
                },
                error: function (error) {}
            }, 'loading');
        })
    }
}
new comparisonTrademarkResult()
