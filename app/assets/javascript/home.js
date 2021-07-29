$(document).ready(function () {
    $(".submit").click(function () {
        let form = $(this).parent();
        let valueComment = form.serialize();
        let url = form.attr('action');
        let form_id = form.attr('id');

        if (valueComment !== "commentaire=") {
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
        } else {
            iziToast.error({
                message: 'Vous ne pouvez pas laisser un commentaire vide :(',
            })
        }

        if(data === "error_date") {
            iziToast.error({
                message: "Veuillez vérifier que vos dates correspondent et sont cohérentes !",
            })
        }

        return false;
    });

    $(".participation-event").click(function (e) {
        e.preventDefault();
        let form = $(this).parent();
        let event = form.serialize();
        let url = form.attr('action');
        let btn = $(this)[0];

        $.ajax({
            type: "POST",
            url: window.location.origin + url,
            data: event,
            dataType: "text",
            success: function (data) {
                if(data === "200"){
                    iziToast.success({
                        message: 'Vous participez bien à l\'événement !',
                    })
                    btn.classList.remove('btn-form-blue');
                    btn.classList.add('btn-form-outline-blue');
                    btn.textContent = "Me désinscrire";
                    form.attr('action','/desinscrire');
                }else if(data === "403") {
                    iziToast.error({
                        message: 'Vous n\'êtes pas un adhérent',
                    })
                }else if(data === "204"){
                    iziToast.success({
                        message: 'Vous ne participez plus à l\'événément',
                    })
                    btn.classList.remove('btn-form-outline-blue');
                    btn.classList.add('btn-form-blue');
                    btn.textContent = "Participer";
                    form.attr('action','/participate');
                }else{
                    iziToast.error({
                        message: data,
                    })
                }
            }
        });

        return false;
    });

});