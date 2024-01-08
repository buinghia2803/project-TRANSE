$(document).ready(function (e) {
    // Dismiss modal
    $('body').on('click', '[data-dismiss]', function (e) {
        e.preventDefault();
        let modalID = $(this).closest('.modal').attr('id');
        closeModal('#'+modalID);
    })
})

function openModal(modalID) {
    // $('body').addClass('modal-open');
    $(modalID).addClass('in');
    $(modalID).css('display', 'block');
    $(modalID).after(`<div class="modal-backdrop fade in"></div>`);
}

function closeModal(modalID) {
    // $('body').removeClass('modal-open');
    $(modalID).removeClass('in');
    $(modalID).css('display', 'none');
    $(modalID).next().remove();
}
