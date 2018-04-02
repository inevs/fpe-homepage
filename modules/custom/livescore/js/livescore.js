(function ($) {
  
  function updateScore() {
    console.log("updateScore");
    livescoreEndpoint = "https://footballscores.herokuapp.com/games/" + drupalSettings.livescore.gameId + ".json"
    $.ajax({
      url: livescoreEndpoint,
      dataType: "json",
      success: function (data) {
        console.log(data);
        $(".scores").empty();
        $(".scores").append(data["score"]["away"]["total"] + " : " + data["score"]["home"]["total"]);
      },
    });  
  };

  updateScore();
  var autoTimer = setInterval(updateScore, 5000);
  $autoreloadButton = $("#autoreload");
  $autoreloadButton.click( function() {
    $autoreloadButton.toggleClass("highlighted");
    if ($autoreloadButton.hasClass("highlighted")) {
      console.log("start auto");
      autoTimer = setInterval(updateScore, 5000);
    } else {
      console.log("stop auto");
      clearInterval(autoTimer);
    }
  });

})(jQuery);