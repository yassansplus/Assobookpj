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
                ' <button data-id="' + JSON.parse(e)?.id + '" class="btn suivre btn-form-blue text-white">' +
                'Suivre</button>' +
                '</li>'))

        })
    })
    $(document).on('click', '.suivre', function () {
        let association = $(this);
        $.post('/follow-suggestion', {association: association.attr('data-id')}, function (data) {
            iziToast.success({
                title: data.title,
                message: data.message,
            })
            association.parent().remove();
        });
    })
});

const addAttributeToNewElem = (array, newElem) => {
    for (const [key, value] of Object.entries(array)) {
        newElem.setAttribute(key, value);
    }
}

const follow = document.querySelector('.follow');

const clickBtn = (btn) => {
    document.querySelector('.follow').addEventListener('click', () => {
        const id = btn.dataset.id;
        gestionFollower(id, btn);
    })
}

if (follow) {
    clickBtn(follow);
}

const gestionFollower = (value, btn) => {
    fetch("/follow", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "Accept": 'application/json'
        },
        body: JSON.stringify(value),
    }).then((response) => {
        return response.json();
    }).then((data) => {
        iziToast.success({
            title: data.title,
            message: data.message,
        })
        const getElemFollow = document.querySelector('.info-details-account').children[4];
        const getNumberFollow = Number(getElemFollow.textContent.replace(/[a-zA-Z]+/g, ''));
        const newBtn = document.createElement('button');
        const newIcon = document.createElement('i');
        if (data.value === 'add') {
            addAttributeToNewElem({
                class: "btn btn-form btn-form-outline-blue w-100 follow",
                style: "white !important",
                "data-id": value
            }, newBtn);
            addAttributeToNewElem({class: "fas fa-minus-circle", style: "font-size:13px !important;"}, newIcon);
            getElemFollow.textContent = getNumberFollow + 1 + ` follower${getNumberFollow > 1 ? 's' : ''}`;
        } else if (data.value === 'del') {
            addAttributeToNewElem({class: "btn btn-form btn-form-blue w-100 follow", "data-id": value}, newBtn);
            addAttributeToNewElem({class: "fas fa-plus-circle"}, newIcon);
            getElemFollow.textContent = getNumberFollow - 1 + ` follower${getNumberFollow > 1 ? 's' : ''}`;
        }
        newBtn.appendChild(newIcon);
        newBtn.innerHTML += data.value === 'add' ? ' Ne plus suivre' : ' Follow';
        btn.parentNode.appendChild(newBtn);
        btn.remove();
        clickBtn(newBtn);
    })
}
