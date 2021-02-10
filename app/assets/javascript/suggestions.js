$(document).ready(function () {
    $.get('/getTags', function (data) {
        $('#suggestions').amsifySuggestags({
            suggestions: data,
            afterAdd: function (value) {
                $.post('/manageTag', {tag: value});
            },
            afterRemove: function (value) {
                $.post('/manageTag', {tag: value, manage: true});
            },
        });
    })
    $('#validateTags').click(function () {
        $.get('/bestMatch', function (data) {
            let boxMatched = $('#resultMatch');
            boxMatched.empty();
            data.forEach(e => boxMatched.append('' +
                '  <li class="list-group-item">'
                + JSON.parse(e)?.name + '' +
                ' <button data-id="' + JSON.parse(e)?.id + '" class="suivre">' +
                'Suivre</button>' +
                '</li>'))

        })
    })
    $(document).on('click', '.suivre', function () {
        let association = $(this);
        $.post('/follow', {association: association.attr('data-id')}, function (data) {
            iziToast.success({
                title: data.title,
                message: data.message,
            })
            association.parent().remove();
        });
    })


});
