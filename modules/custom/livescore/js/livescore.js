(function ($) {
  
  function updateLiveScore() {
    console.log("updateScore");
    livescoreEndpoint = "https://footballscores.herokuapp.com/games/" + drupalSettings.livescore.gameId + ".json"
    $.ajax({
      url: livescoreEndpoint,
      dataType: "json",
      success: function (data) {
        console.log(data);
        updateScores(data["score"]);
        updateNotes(data["notes"]);
      },
    });  
  };

  updateLiveScore();
  var autoTimer = setInterval(updateLiveScore, 5000);
  $autoreloadButton = $("#autoreload");
  $autoreloadButton.click( function() {
    $autoreloadButton.toggleClass("highlighted");
    if ($autoreloadButton.hasClass("highlighted")) {
      console.log("start auto");
      autoTimer = setInterval(updateLiveScore, 5000);
    } else {
      console.log("stop auto");
      clearInterval(autoTimer);
    }
  });

  function updateScores(data) {
    $scores = $(".scores");
    $scores.empty();
    $scores.append(data["away"]["total"] + " : " + data["home"]["total"]);
  }

  function updateNotes(data) {
    $notes = $(".notes");
    $notes.empty();
    data.forEach(note => {
      note_data = $('<div>', {class: 'note'})
        .append($('<div>', {class: 'timestamp'})).append(note["created_at"])
        .append($('<div>', {class: 'text'})).append(note["text"]);
      $notes.append(note_data);
    });
  }

})(jQuery);