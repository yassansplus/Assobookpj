const $ = require('jquery');
window.iziToast = require('izitoast');

const resetpwd = $('.upd-pwd');
const cancelpwd = $('.btn-secondary');

const updatePwd = function () {
    $(this).addClass('d-none');
    $('.formPassword').removeClass('d-none');
}
const cancelForm = function () {
    $('form[name=update_pwd]')[0].reset();
    $('.sidebar .formPwd').addClass('d-none')
    resetpwd.removeClass('d-none');
}

const getIziToast = function (title, message, icon, color) {
    iziToast.show({
        icon: `${icon}`,
        color: `${color}`,
        title: `${title}`,
        message: `${message}`,
    });
}


$('form[name=update_pwd]').submit(function (event) {
    // cancels the form submission
    event.preventDefault();
    submitForm();
});

const condition = function (data, compare, title, message, icon, color) {
    if (data === compare) {
        return getIziToast(title, message, icon, color);
    }
}

const submitForm = function () {
    const numberOfInput = $('form[name=update_pwd] input[type=password]').length;
    const oldpwd = $('#update_pwd_old_password');
    const newpwd = $('#update_pwd_new_password_first');
    const confirmpwd = $('#update_pwd_new_password_second');
    const numberOfRequired = $('form[name=update_pwd] input[required=required]').length;

    $.ajax({
        type: "POST",
        url: "/reset-password",
        data: {
            input: numberOfInput,
            oldpwd: oldpwd.val(),
            newpwd: newpwd.val(),
            confirmpwd: confirmpwd.val(),
            required: numberOfRequired
        },
        success: function (data) {
            condition(data, 'OK', 'Super', 'Mot de passe modifié !', 'fas fa-check', 'green');
            condition(data, 'Pwd', 'Erreur', 'Mot de passe incorrect !', 'fas fa-ban', 'red');
            condition(data, 'Input', 'Erreur', 'Mot de passe modifié !', 'fas fa-ban', 'red');
            condition(data, 'Equals', 'Erreur', 'Les mots de passe ne correspondent pas !', 'fas fa-ban', 'red');
            condition(data, 'Size', 'Attention', 'Votre mot de passe doit faire entre 8 et 16 caractères', 'fas fa-exclamation-triangle', 'orange');
            condition(data, 'Error', 'Erreur', 'Une erreur est survenue', 'fas fa-ban', 'red');
            if (data === 'OK') {
                cancelForm();
            }
        }
    });
}


resetpwd.click(updatePwd);
cancelpwd.click(cancelForm);
