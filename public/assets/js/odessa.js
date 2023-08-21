$('.burger2').click(function() {
  $('.burger2').toggleClass('on');
  $('.header').toggleClass('active');
 });

 $(document).ready(function () {

  $('#comment').click(function() {
    $('.modal-feedback').toggleClass('active');
    $('.modal-feedback-overlay').toggleClass('active');

  });

  $('#close').click(function() {
    $('.modal-feedback').toggleClass('active');
    $('.modal-feedback-overlay').toggleClass('active');
  });

  $('#feedback').on('submit', function(event){
    event.preventDefault();

    var url = $(this).attr('data-action');

    $.ajax({
        url: url,
        method: 'POST',
        data: new FormData(this),
        dataType: 'JSON',
        contentType: false,
        cache: false,
        processData: false,
        success:function(response)
        {
            $('#feedback').trigger("reset");
            $('.modal-feedback').toggleClass('active');
            $('.modal-feedback-overlay').toggleClass('active');
            $('h4').toggleClass('hidden');
            $('.comment').addClass('send');

        },
        error: function(response) {
        }
    });
});
 });

$(document).ready(function () {

  $('#reserve').on('submit', function(event){
    event.preventDefault();

    var url = $(this).attr('data-action');

    $.ajax({
        url: url,
        method: 'POST',
        data: new FormData(this),
        dataType: 'JSON',
        contentType: false,
        cache: false,
        processData: false,
        success:function(response)
        {
            $('#reserve').trigger("reset");
            $('#reserve').fadeOut("fast");
            $('.reserve-message').toggleClass("hidden");
            $('.reserve-message').fadeIn("slow");
        },
        error: function(response) {
        }
    });
});
});