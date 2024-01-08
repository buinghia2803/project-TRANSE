data.forEach(element => {
    element.forEach(item => {
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
