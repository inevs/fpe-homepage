console.log(drupalSettings.livescore);

(function ($) {
  livescoreEndpoint = "https://footballscores.herokuapp.com/games/" + drupalSettings.livescore.gameId + ".json"
  $.ajax({
    url: livescoreEndpoint,
    dataType: "json",
    success: function (data) {
      console.log(data);
      $(".scores").append(data["score"]["away"]["total"] + " : " + data["score"]["home"]["total"]);
    },
  });
})(jQuery);