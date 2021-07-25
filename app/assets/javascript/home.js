$(document).ready(function () {
    $(".submit").click(function () {
        let form = $(this).parent();
        let valueComment = form.serialize();
        let url = form.attr('action');
        let form_id = form.attr('id');

        $.ajax({
            type: "POST",
            url: window.location.origin + url,
            data: valueComment,
            dataType: "text",
            success: function () {
                iziToast.success({
                    message: 'Votre commentaire à bien été ajouté !',
                })
                $("#load-" + form_id).load(" #load-" + form_id);
                $("#" + form_id)[0].reset();
            }
        });

        return false;
    });
});