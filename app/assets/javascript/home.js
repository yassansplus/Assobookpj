//document.getElementById("envoi").hidden = true;

/*
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
            //$("#load-comment").load(window.location.href);
            $('#comment')[0].reset();
        })


        /*
        $.ajax({
            type: "POST",
            url: window.location.origin + url,
            data: valueComment,
            dataType: "text",
            success: function (){
                iziToast.success({
                    message: 'Votre commentaire à bien été ajouté !',
                })
                $( "#load-comment" ).load(window.location.href + " #load-comment" );
            }
        });



        return false;
    })
})

 */

/*

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


$(document).ready(function () {
    $("#jumbo").delay(3000).hide(3000);
});

 */


document.querySelector(".submit").addEventListener("click", (e) => {
    e.preventDefault();
    const commentaire = document.querySelector("input[name='commentaire']").value;
    //console.count(commentaire);
    const url = document.querySelector("#comment").getAttribute("action");
    fetch(window.location.origin + url, {
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        method: "POST",
        body: JSON.stringify({commentaire: commentaire})
    }).then(res => res.json()).then(data => {
        iziToast.success({
            title: 'OK',
            message: 'Votre commentaire à bien été ajouté !',
        });
        document.querySelector(".test-comment").innerHTML += createFormComment(data.contenu, data.date, data.id_user, data.heure, data.fullname, data.is_association);
        //$( "#load-comment" ).load(" #load-comment" );
    });

    //$('#comment')[0].reset();

});

const createFormComment = (content, date, user_id, heure, fullname, is_association) => {
    return `
    <hr>
    <div class="media m-2">
        <i class="fas fa-share p-2"></i>
        <div class="media-body">
            <p>
                <em>
                    Par
                    <a href="/${is_association ? "association":"adherent"}/${user_id}"  style="color: #000000 !important;">${fullname}</a>
                </em>
                <span class="badge badge-${is_association ? "light":"primary"}">${is_association ? "Association":"Moi"}</span>
                <span class="badge badge-dark">Le ${date + ' à ' + heure}</span>
            </p>
            <p>
                <small>${content}</small>
            </p>
        </div>
    </div>
    `
}