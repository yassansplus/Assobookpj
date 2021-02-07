
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
    $('.banner-photo div:first-child').css('-webkit-filter', 'blur('+distanceScrolled/40+'px)');
});

const addInput = function(){
    $(this).css({
        'background': 'rgba(238,238,238,0.7)',
        'border-radius': '5px',
        'padding': '3px'
    });
}

$('.banner-photo div:last-child p').click(addInput);
function changePrestation(id) {
    var choix = $('#categorieFirst' + id).val();
    $.ajax({
        url: '/getPrestation',
        type: 'GET',
        data: 'choix=' + choix,
        success: function(data) {
            if (data) {
                var tab = $.parseJSON(data);
                var html;
                html = '<option default selected disabled>Prestation (' + tab.length + ')</option>' + '<option disabled>──────────</option>';
                for (var i = 0; i < tab.length; i++) {
                    html = html + '<option value=' + tab[i][0] + '>' + tab[i][1] + '</option>';

                }
                $('#prestationFirst' + id).html(html);
            }
        }
    });
}

const getIziToast = function(title,message,icon,color){
    iziToast.show({
        icon: `${icon}`,
        color: `${color}`,
        title: `${title}`,
        message: `${message}`,
    });
}

$(document).click(function(event) {
    const target = $('.banner-photo div:last-child p');
    if(target.attr('style') !== undefined){
        //Closest permet de récuperer le premier ancestre de l'element passer
        // return proche de l'element courant ou l'élement courant.
        if(!$(event.target).closest(target).length){
            target.removeAttr('style');
            const newName = target.text();
            $.ajax({
                url: '/edit-nom',
                type: 'POST',
                data: {name: newName},
                success: function(data) {
                    if(data['title'] === 'OK'){
                        getIziToast('OK','Modification réussie !','fas fa-check','green');
                    }else if(data['title'] === 'Warning'){
                        getIziToast('Attention','Aucune modification établie car le prénom est le même !','fas fa-exclamation-triangle','orange');
                    }else if(data['title'] === 'Error'){
                        getIziToast('Erreur','Le nom ne peut pas être vide','fas fa-ban','red');
                        target.text(data['data']);
                    }
                },
                error: function(data){
                    getIziToast('Attention','Aucune modification établie car le prénom est le même !','fas fa-ban','red');
                }
            });
        }
    }
});
