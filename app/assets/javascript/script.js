
const $ = require('jquery');
window.iziToast = require('izitoast');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');
require('popper.js')

window.setTimeout(function () {
    $(".alert").fadeTo(1000, 0).slideUp(500, function () {
        $(this).remove();
    });
}, 2000);

$(window).scroll(function(e) {
    const distanceScrolled = $(this).scrollTop();
    console.log(distanceScrolled);
    $('.banner-photo div:first-child').css('-webkit-filter', 'blur('+distanceScrolled/40+'px)');
});
