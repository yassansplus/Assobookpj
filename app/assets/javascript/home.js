document.getElementById("envoi").hidden = true;

$(document).ready(function () {
    $(".submit").click(function () {
        let form = $(this).parent();
        let valueComment = form.serialize();
        let url = form.attr('action');

        $.post(window.location.origin + url, valueComment, function (data) {
            iziToast.success({
                title: 'OK',
                message: data,
            })
        })

        return false;
    })

})

function scrollReveal() {
    var revealPoint = 200;
    var revealElement = document.querySelectorAll(".demo");
    for (var i = 0; i < revealElement.length; i++) {
        var windowHeight = window.innerHeight;
        var revealTop = revealElement[i].getBoundingClientRect().top;
        if (revealTop < windowHeight - revealPoint) {
            revealElement[i].classList.add("active");
        } else {
            revealElement[i].classList.remove("active");
        }
    }
}
window.addEventListener("scroll", scrollReveal);
scrollReveal();

$(document).ready(function(){
    $("#jumbo").delay(3000).hide(3000);
});