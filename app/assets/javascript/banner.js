$(document).ready(function () {
  $("div#panel").hide();

  var autoTimer = null;

  autoTimer = setTimeout(function () {
    $("div#panel").slideDown("slow");
    /* autoTimer = setTimeout(function(){
            $("div#panel").slideUp("slow");
        }, 5000); */
  }, 2000);

  $("#open").click(function () {
    $("div#panel").slideDown("slow");
    if (autoTimer) clearTimeout(autoTimer);
    autoTimer = null;
  });
});
