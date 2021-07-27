$("textarea").keydown(function (e) {
    // Enter was pressed without shift key
    if (e.key == 'Enter' && !e.shiftKey) {
        sendT();
        e.preventDefault();
    }
});

$("#send").click(sendT);
window.setInterval(function () {
    userTalkToId = $('#talkingTo').val();
    let url = window.location.origin + '/chat/updateMessage/' + userTalkToId;
    console.log(url);
    $.get(url, userTalkToId, (data) => {
        $("#blockToUpdate").html(data);
    })
}, 2000);

function sendT() {

    let myForm = $("#myForm").serialize();
    let url = window.location.origin + $('#myForm').attr('action');


    $.post(url, myForm, (data) => {
        $("#blockToUpdate").html(data);
    })

    //showErrors('tips', 'Tips', 'Press Ctrl + Shift to make a new line!');
    $('.message .messArea').last().append('<div class="textM  newMmess">' + $('#message').val() + '</div>')
    var goup = setTimeout(function () {
        $('.message .messArea .textM').last().removeClass('newMmess');
    }, 10);
    $('#message').val('')
    goToBottom();
    findText()
    return false;


}

document.onload = goToBottom();

var chatStatus = 1;
$('.chatArea').on('scroll', function () {
    if ($(this).scrollTop() + $(this).innerHeight() < $(this)[0].scrollHeight - $('.message').last().innerHeight()) {
        newGoD()
    }
})

findText()

function findText() {
    var message = document.querySelectorAll('.textM'),
        i;
    for (i = 0; i < message.length; i++) {
        message[i].innerHTML = linkify(message[i].textContent);
    }
}


function linkify(text) {
    var urlRegex = /(\b(https?|ftp|file):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|])/ig;
    return text.replace(urlRegex, function (url) {
        return '<a class="linkInMessage" href="' + url + '" target="blank">' + url + "</a>";
    });
}

$("#message").on("input", function () {
    var textarea = document.querySelector("#message")
    enteredText = textarea.value;
    numberOfLineBreaks = (enteredText.match(/\n/g) || []).length;
    characterCount = enteredText.length + numberOfLineBreaks;
    rowcount = numberOfLineBreaks + 1;
    if (rowcount < 4) {
        $("#message").attr('rows', rowcount)
    }
});

function newGoD() {
    $("#goToDown").removeClass('downDowny')
}


function showErrors(type, title, details) {
    if (type == 'error') {
        $('.errorsSide').append('<div class="bubble" id="errorBubble"><h1 class="erStatus"> <span class="material-icons">error</span>' + title + '</h1><p class="erDetails">' + details + '</p></div>');
    }
    if (type == 'tips') {
        $('.errorsSide').append('<div class="bubble" id="tipsBubble"><h1 class="erStatus"> <span class="material-icons">tips_and_updates</span>' + title + '</h1><p class="erDetails">' + details + '</p></div>');
    }
    $('.bubble').attr('style', 'display:block;');

    var start = setTimeout(function () {
        $('.bubble').addClass('bubbleAfter');
    }, 100);
    var end = setTimeout(function () {
        $('.bubble').first().removeClass('bubbleAfter');
        $('.bubble').first().addClass('bubbleGone');
    }, 5000);
    var deleteEl = setTimeout(function () {
        $('.bubble').first().remove();
    }, 5700)
}

$("#goToDown").click(function () {
    goToBottom();
})

function goToBottom() {
    $("#goToDown").addClass('downDowny');
    $('.chatArea').scrollTop($('.chatArea')[0].scrollHeight);
}

$("#linkCopy").click(function () {
    var timer = setTimeout(function () {
        $(".shareLink").addClass('showItem');
        $(".blackout").addClass('blackShow');
    }, 120);
    var timer2 = setTimeout(function () {
        $(".shareLink").attr('style', 'display: block');
        $(".blackout").attr('style', 'display: block');

    }, 100);

    $(".blackout").click(function () {
            $(".shareLink").removeClass('showItem');
            $(".blackout").removeClass('blackShow');
            var timer2 = setTimeout(function () {
                $(".shareLink").attr('style', 'display: none');
                $(".blackout").attr('style', 'display: none');

            }, 400);
        }
    )
    $("#copyLinker").click(function () {
        var dummy = document.querySelector("#copyvalue");
        dummy.select();
        document.execCommand("copy");
    })
})
