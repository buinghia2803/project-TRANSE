class clsTop {
    constructor() {
        const self = this
        this.showAllNotApply = 0
        window.addEventListener('load', function () {
            self.doLoad();
        })
    }

    /**
     * Do load all action and element.
     */
    doLoad() {
        this.showAllMyfolder()
        this.showAllToDoList()
        this.showAllNotice()
        this.showAllProdNameTrademark()
        this.closeProdNameTrademark()
        this.showAllProdNameAppTrademark()
        this.closeProdNameAppTrademark()
        this.showAllNotApplyList()
        this.showAllApplyList()
        this.btnRedirectToPage()
        this.btnDeleteAnken()
        this.deleteMyFolder()
        this.sortNotApplyByType()
        this.redirectToPage()
    }

    /**
     * Redirect to page
     */
    redirectToPage() {
        $('body').on('click', '.btn-todo-redirect', function () {
            const redirectUrl = $(this).data('redirect')
            const id = $(this).data('notice-detail-id')
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: BASE_URL + 'update-notice-detail/' + id,
                method: 'POST',
                data_type: 'json',
                data: {},
                beforeSend: function () {
                    loadingBox('open')
                },
            }).done(function (data) {
                loadingBox('close')
                window.location.href = redirectUrl
            })
        })
    }

    /**
     * Handle event click show all my folder.
     */
    showAllMyfolder() {
        $('body').on('click', '#showAllMyFoler', function () {
            const seft = this
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: routeAllMyFolder,
                method: 'GET',
                data_type: 'json',
                data: {},
                beforeSend: function () {
                    loadingBox('open')
                },
            })
            .done(function(res) {
                $('#myFolderTbl tbody').append(res.html)
                loadingBox('close')
                $(seft).css('display', 'none')
            }).fail(function() {
                loadingBox('close')
            });
        })
    }

    /**
     * Handle event click show all my folder.
     */
    showAllToDoList() {
        $('body').on('click', '#showAllToDoList', function () {
            const seft = this
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: routeAllToDoList,
                method: 'GET',
                data_type: 'json',
                data: {},
                beforeSend: function () {
                    loadingBox('open')
                },
            })
            .done(function(res) {
                $('#tblToDoList tbody').append(res.html)
                loadingBox('close')
                $(seft).css('display', 'none')
            }).fail(function() {
                loadingBox('close')
            });
        })
    }

    /**
     * Handle event click show all my folder.
     */
    showAllNotApplyList() {
        const _self = this
        $('body').on('click', '#showAllATMNotApply', function () {
            const self = this
            _self.showAllNotApply = 1
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: routeAllNotApplyList + '?show_all=' + _self.showAllNotApply,
                method: 'GET',
                data_type: 'json',
                data: {},
                beforeSend: function () {
                    loadingBox('open')
                },
            })
            .done(function(res) {
                $('#tblAppTrademarkNotApply tbody').append(res.html)
                loadingBox('close')
                $(self).remove()
            }).fail(function() {
                loadingBox('close')
            });
        })
    }

    /**
     * Sort data
     */
    sortNotApplyByType() {
        const self = this
        $('.btn-sort').on('click', function () {
            const sortType = $(this).data('sort')
            const showAll = $('#showAllATMNotApply').length
            let routeParams = routeAllNotApplyList + '?sort_type='+sortType+'&show_all=' + self.showAllNotApply
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: routeParams,
                method: 'GET',
                data_type: 'json',
                data: {},
                beforeSend: function () {
                    loadingBox('open')
                },
            })
            .done(function(res) {
                $('#tblAppTrademarkNotApply tbody').html(res.html)
                loadingBox('close')
            }).fail(function() {
                loadingBox('close')
            });
        })
    }

    /**
     * Show all product name
     */
    showAllProdNameTrademark() {
        $('body').on('click', '.showAllProductTrademark', function (){
            const self = this
            const trademarkId = $(this).data('trademark-id')

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: BASE_URL + 'all-prod-name-trademark/' + trademarkId,
                method: 'GET',
                data_type: 'json',
                data: {},
                beforeSend: function () {
                    loadingBox('open')
                },
            })
            .done(function(res) {
                const content = res.html + `
                <span>
                    <a href="javascript:void(0)" data-trademark-id="${trademarkId}" class="closeAllProductTrademark">[-]</a>
                </span>
                `
                $(self).closest('.td_distinction-product-cls').html(content)

                loadingBox('close')
            }).fail(function() {
                loadingBox('close')
            });
        })
    }

    /**
     * Show all product name
     */
    closeProdNameTrademark() {
        $('body').on('click', '.closeAllProductTrademark', function (){
            const self = this
            const trademarkId = $(this).data('trademark-id')

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: BASE_URL + 'close-prod-name-trademark/' + trademarkId,
                method: 'GET',
                data_type: 'json',
                data: {},
                beforeSend: function () {
                    loadingBox('open')
                },
            })
            .done(function(res) {
                const content = res.html + `
                    <span>
                        <a href="javascript:void(0)" data-trademark-id="${trademarkId}" class="showAllProductTrademark">[+]</a>
                    </span>
                `
                $(self).remove()
                $(self).closest('.td_distinction-product-cls').html(content)

                loadingBox('close')
            }).fail(function() {
                loadingBox('close')
            });
        })
    }

    /**
     * Show all product name
     */
    showAllProdNameAppTrademark() {
        $('body').on('click', '.showAllProductAppTrademark', function (){
            const self = this
            const appTrademarkId = $(this).data('app-trademark-id')

            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: BASE_URL + 'all-prod-name-app-trademark/' + appTrademarkId,
                method: 'GET',
                data_type: 'json',
                data: {},
                beforeSend: function () {
                    loadingBox('open')
                },
            })
            .done(function(res) {
                const content = res.html + `
                <span>
                    <a href="javascript:void(0)" data-app-trademark-id="${appTrademarkId}" class="closeProductAppTrademark">[-]</a>
                </span>
                `
                $(self).closest('.td_distinction-product-cls').html(content)
                loadingBox('close')
            }).fail(function() {
                loadingBox('close')
            });
        })
    }

    /**
     * Close product name
     */
    closeProdNameAppTrademark() {
            $('body').on('click', '.closeProductAppTrademark', function (){
                const self = this
                const appTrademarkId = $(this).data('app-trademark-id')

                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    url: BASE_URL + 'close-prod-name-app-trademark/' + appTrademarkId,
                    method: 'GET',
                    data_type: 'json',
                    data: {},
                    beforeSend: function () {
                        loadingBox('open')
                    },
                })
                .done(function(res) {
                    const content = res.html + `
                    <span>
                        <a href="javascript:void(0)" data-app-trademark-id="${appTrademarkId}" class="showAllProductAppTrademark">[-]</a>
                    </span>
                    `
                    $(self).closest('.td_distinction-product-cls').html(content)
                    $(self).remove()
                    loadingBox('close')
                }).fail(function() {
                    loadingBox('close')
                });
            })
        }

    /**
     * Handle event click show all my folder.
     */
    showAllApplyList() {
        $('body').on('click', '#showAllATMApply', function () {
            const seft = this
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: routeAllApplyList,
                method: 'GET',
                data_type: 'json',
                data: {},
                beforeSend: function () {
                    loadingBox('open')
                },
            })
            .done(function(res) {
                $('#tblAppTrademarkApply tbody').append(res.html)
                loadingBox('close')
                $(seft).css('display', 'none')
            }).fail(function() {
                loadingBox('close')
            });
        })
    }

    /**
     * Show all notice
     */
    showAllNotice() {
        $('#showAllNotice').on('click', function () {
            const seft = this
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: routeAllNotice,
                method: 'GET',
                data_type: 'json',
                data: {},
                beforeSend: function () {
                    loadingBox('open')
                },
            })
            .done(function(res) {
                $('#modalNoticeAll .content dl').html(res.html)
                $('#modalNoticeAll').show()
                loadingBox('close')
            }).fail(function() {
                loadingBox('close')
            });
        })
    }

    /**
     * Delete anken
     */
    btnDeleteAnken() {
        $('body').on('click', '.delete-anken', function () {
            const trademarkId = $(this).data('trademark-id')
            const _seft = this

            $.confirm({
                title: '',
                content: messageDeleteAnken,
                buttons: {
                    cancel: {
                        text: CANCEL,
                        action: function action() {}
                    },
                    confirm: {
                        text: YES,
                        btnClass: 'btn-red',
                        action: function action() {
                            $.ajax({
                                headers: {
                                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                },
                                url: BASE_URL + 'delete-anken/' + trademarkId,
                                method: 'DELETE',
                                data_type: 'json',
                                data: {},
                                beforeSend: function () {
                                    loadingBox('open')
                                },
                            }).done(function(res) {
                                $(_seft).closest('tr').remove()
                                loadingBox('close')
                                window.location.reload();
                            }).fail(function() {
                                loadingBox('close')
                            });
                        }
                    }
                }
            });
        })
    }

    /**
     * Delete my folder
     */
    deleteMyFolder() {
        $('body').on('click', '.delete-my-folder', function () {
            const _seft = this
            const myFolderId = $(this).data('folder-id')
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: BASE_URL + 'delete-my-folder/' + myFolderId,
                method: 'DELETE',
                data_type: 'json',
                data: {},
                beforeSend: function () {
                    loadingBox('open')
                },
            })
            .done(function(res) {
                $(_seft).closest('tr').remove()
                loadingBox('close')
                window.location.reload();
            }).fail(function() {
                loadingBox('close')
            });
        })
    }

    /**
     * Go to page with type.
     */
    btnRedirectToPage() {
        $('body').on('click', '.btn-type-redirect', function () {
            const seft = this
            const typePage = $(this).data('type-page')
            const trademarkId = $(this).data('trademark-id')
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: routeRedirectWithType,
                method: 'GET',
                data_type: 'json',
                data: {
                    type_page : typePage,
                    trademark_id: trademarkId
                },
                beforeSend: function () {
                    loadingBox('open');
                },
            })
            .done(function(res) {
                loadingBox('close');
                if(res && res.redirect_to) {
                    window.location.href = res.redirect_to
                }
            }).fail(function() {
                loadingBox('close')
            });
        })
    }
}

new clsTop()
