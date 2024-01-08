datasSetColorByDistrictionGroup.forEach(function (element) {
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
            $('.distrinction_'+element['codeDistriction']).addClass('bg_yellow')
        }
        if(arrPink.length == element['product'].length) {
            $('.distrinction_'+element['codeDistriction']).addClass('bg_pink')
        }
    })
})
