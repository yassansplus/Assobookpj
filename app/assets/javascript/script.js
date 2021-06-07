const $ = require('jquery');
window.iziToast = require('izitoast');
// this "modifies" the jquery module: adding behavior to it
// the bootstrap module doesn't export/return anything
require('bootstrap');
require('popper.js');

if(window.location.pathname === '/profil' && localStorage.getItem('update')){
    localStorage.removeItem('update');
}

const animateScroll = (value,bool) => {
    const scroll = (bool ? $(value).offset().top - 80 : value);
    $('html, body').animate({
        scrollTop: scroll
    }, 1000);
}

$('.content a').on('click', function(e) {
    e.preventDefault();
    animateScroll(this.hash,true);
});

// When the user clicks on the button, scroll to the top of the document
const topFunction = () => {
    animateScroll(0,false);
}

const mybutton = document.getElementById("scroll-to-up");
if(mybutton !== null){
    mybutton.addEventListener('click',topFunction);
    // When the user scrolls down 20px from the top of the document, show the button
    window.onscroll = () => scrollFunction();

    const scrollFunction = () => {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            mybutton.style.display = "block";
        } else {
            mybutton.style.display = "none";
        }
    }

    window.setTimeout(function () {
        $(".alert").fadeTo(1000, 0).slideUp(500, function () {
            $(this).remove();
        });
    }, 2000);

    $(window).scroll(function() {
        const distanceScrolled = $(this).scrollTop();
        $('.banner-photo div:first-child').css('-webkit-filter', 'blur('+distanceScrolled/40+'px)');
    });
}

var getId = '';
const addInput = function(){
    $(this).css({
        'background': 'rgba(238,238,238,0.7)',
        'border-radius': '5px',
        'padding': '3px'
    });
    getId = $(this).data('elem');
}

$(`p[data-id=id]`).click(addInput);

const getIziToast = function(title,message,icon,color){
    iziToast.show({
        icon: `${icon}`,
        color: `${color}`,
        title: `${title}`,
        message: `${message}`,
    });
}

$(document).click(function(event) {
    if(getId.length > 0){
        const target = $(`p[data-elem=${getId}]`);
        if(target.attr('style') !== undefined){
            //Closest permet de récuperer le premier ancestre de l'element passer
            // return proche de l'element courant ou l'élement courant.
            if(!$(event.target).closest(target).length){
                target.removeAttr('style');
                const newName = target.text();
                $.ajax({
                    url: '/edit-nom',
                    type: 'POST',
                    data: {name: newName,type: getId},
                    success: function(data) {
                        const typeUser = data['type'] === 0 ? 'adh-firstname' : 'assos';
                        if(data['title'] === 'OK'){
                            getIziToast('OK','Modification réussie !','fas fa-check','green');
                            if(getId !== 'adh-lastname'){
                                $('.nav-item:last-child .nav-link').html(`<i class="bi bi-house-door" style="margin-right: 0.5em"></i> ${data['data']}`);
                                $(`p[data-elem=${typeUser}]`).text(data['data']);
                                $('p[data-elem=banner]').text(data['data']);
                            }
                        }else if(data['title'] === 'Warning'){
                            getIziToast('Attention','Aucune modification établie car l\'element est le même !','fas fa-exclamation-triangle','orange');
                        }else if(data['title'] === 'Error'){
                            getIziToast('Erreur','L\'élément ne peut pas être vide','fas fa-ban','red');
                            target.text(data['data']);
                        }
                        getId = '';
                    },
                    error: function(data){
                        getIziToast('Erreur','Une erreur est survenue','fas fa-ban','red');
                        getId = '';
                    }
                });
            }
        }
    }
});