import $ from "jquery";
window.$ = $;
window.jQuery = $;

$(document).ready(function () {

    $('.menu-icon').click(function () {
        console.log('click');
        $('.menu-icon').toggleClass('nav-active');
        $('.menu-header').toggleClass('active');
    });

});