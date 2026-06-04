$('#comment').click(function () {
    console.log('comment click');
    $('#modal-feedback').addClass('active');
    $('.modal-overlay').addClass('active');

});

$(".menu-item").on('click', function () {
    const $item = $(this);
        // 2. Извлекаем данные с помощью метода .data()
        let title = $item.data('title');
        let sup = $item.data('sup');
        let desc = $item.data('desc');
        let price = $item.data('price');
        let imageUrl = $item.data('image-url');


        const $modal = $('#modal-item');

        $modal.find('#modal-title').text(title);
        $modal.find('#modal-description').text(desc);
        $modal.find('#modal-sup').text(sup);
        $modal.find('#modal-price').text(price);
        $modal.find('#modal-image').attr('src', imageUrl);

    if (title) {
        console.log('open modal');
        $('#modal-item').toggleClass('active');
        $('.modal-overlay').toggleClass('active');
    }
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

$('.modal-menu-close, #close-modal, .bottom-sheet-handle, .modal-overlay').click(function () {
    closeModal();
});
