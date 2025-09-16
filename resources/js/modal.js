$('#comment').click(function () {
    console.log('comment click');
    $('#modal-feedback').addClass('active');
    $('.modal-overlay').addClass('active');

});

$(".menu-item").on('click', function () {
    console.log('item click');
    $('#modal-item').addClass('active');
    $('.modal-overlay').addClass('active');
});
$('.bottom-sheet-handle').click(function () {
    console.log('close click');
    $('#modal').removeClass('active');
    $('#modal-feedback').removeClass('active');
    $('.modal-overlay').removeClass('active');
    $('#feedback')[0].reset(); // Сброс формы, если она открыта
});

function closeModal() {
    $('#modal-item').removeClass('active');
    $('#modal-feedback').removeClass('active');

    $('.modal-overlay').removeClass('active');
    $('#feedback')[0].reset(); // Сброс формы, если она открыта
}

$('.modal-menu-close, .bottom-sheet-handle, .modal-overlay').click(function () {
    closeModal();
});
