//document.getElementById("envoi").hidden = true;

$(document).ready(function () {
    $(".submit").click(function () {
        let form = $(this).parent();
        let valueComment = form.serialize();
        let url = form.attr('action');


        /*
        $.post(window.location.origin + url, valueComment, function (data) {
            iziToast.success({
                title: 'OK',
                message: data,
            })
            $('#comment')[0].reset();
        })
         */

        //$("#load-comment").load(window.location.href + " #load-comment");


        $.ajax({
            type: "POST",
            url: window.location.origin + url,
            data: valueComment,
            dataType: "text",
            success: function () {
                iziToast.success({
                    message: 'Votre commentaire à bien été ajouté !',
                })
                $("#load-comment").load(" #load-comment");
            }
        });

        return false;
    });
});