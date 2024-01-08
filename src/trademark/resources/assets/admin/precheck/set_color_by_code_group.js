$(document).ready(function () {
    datasSetColorByCodeGroup.forEach(function (element) {
        arrPink = [];
        arrYellow = [];
        element['product'].forEach(function (value) {
            if(value.type == 3) {
                arrYellow.push(value.id)
            }
            if(value.type == 4) {
                arrPink.push(value.id)
            }
            if(arrYellow.length == element['product'].length) {
                $('.code_'+element['codeName']).addClass('bg_yellow')
            }
            if(arrPink.length == element['product'].length) {
                $('.code_'+element['codeName']).addClass('bg_pink')
            }
        })
    })


});
