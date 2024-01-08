data1s.forEach(element => {
    element['product'].forEach(item => {
        $(document).on('click','.add_' + item.id ,function () {
            $('.icon-add-sub').removeClass('sub_' + item.id)
            $('.icon-add-sub').addClass('sub_' + item,id)
            $('.icon-add-sub').html('-')
            $('.line').removeClass('line-1')
            $('.line').removeClass('w-100')
        })
        $(document).on('click', '.sub' + item.id ,function () {
            $('.icon-add-sub').removeClass('sub_' + item.id)
            $('.icon-add-sub').addClass('add_' + item.id)
            $('.icon-add-sub').html('+')
            $('.line').addClass('line-1')
            $('.line').addClass('w-100')
        })
    })
});

$('.open-modal').on('click', function () {
    loadAjaxPost(openModalUrl, {id: id, data: data}, {
        beforeSend: function(){},
        success:function(result){
            $(".ebcf_modal-content").html(result);
        },
        error: function (error) {
            console.log('err', error)
        }
    }, 'loading');
})
