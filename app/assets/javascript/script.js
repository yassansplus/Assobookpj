
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
