const $ = require('jquery');
window.iziToast = require('izitoast');
const autoComplete = require("@tarekraafat/autocomplete.js");
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

window.setTimeout(function () {
    $(".alert").fadeTo(1000, 0).slideUp(500, function () {
        $(this).remove();
    });
}, 2000);

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

fetch('/lists',{
    method: "GET",
    headers: {
        'Content-Type': 'application/json'
    }
})
    .then((res) => res.json())
    .then((data) => autoCompleteWithLists(data));


const autoCompleteWithLists = (lists) => {
    const autoCompleteJS = new autoComplete({
        selector: "#autoComplete",
        placeHolder: "Recherche...",
        data: {
            src: lists,
            cache: true,
        },
        resultsList: {
            element: (list, data) => {
                if (!data.results.length) {
                    // Create "No Results" message element
                    const message = document.createElement("div");
                    // Add class to the created element
                    message.setAttribute("class", "no_result");
                    message.setAttribute("class", "p-3");
                    // Add message text content
                    message.innerHTML = `<span>Aucun résultat pour "${data.query}"</span>`;
                    // Append message element to the results list
                    list.prepend(message);
                }
            },
            noResults: true,
        },
        resultItem: {
            highlight: true
        },
        events: {
            input: {
                selection: (event) => {
                    const selection = event.detail.selection.value;
                    autoCompleteJS.input.value = selection;
                }
            }
        }
    });
}


const autoCompleteTarget = document.querySelector('#autoComplete');

if(autoCompleteTarget !== null){
    document.querySelector('#autoComplete').addEventListener('keyup', function (e) {
        if (e.key === 'Enter') {
            this.parentNode.parentNode.parentNode.submit();
        }
    });
}

