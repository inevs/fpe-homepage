(function ($) {
  
  function updateLiveScore() {
    console.log("updateScore");
    livescoreEndpoint = "https://footballscores.herokuapp.com/games/" + drupalSettings.livescore.gameId + ".json"
    $.ajax({
      url: livescoreEndpoint,
      dataType: "json",
      success: function (data) {
        console.log(data);
        updateScores(data);
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
    $scores.append($('<div>', {class: 'period'}).append(data["period"]));
    $scores.append($('<div>', {class: 'total'}).append(data["score"]["away"]["total"] + " - " + data["score"]["home"]["total"]));
    $table = $('<table>', {class: 'score_table'});
    $homeRow = $('<tr>');
    $homeRow.append($('<td>').append(data["home_team"]));
    data["score"]["home"]["periods"].forEach(period => {
      $homeRow.append($('<td>').append(period));
    })
    $awayRow = $('<tr>');
    $awayRow.append($('<td>').append(data["away_team"]));
    data["score"]["home"]["periods"].forEach(period => {
      $awayRow.append($('<td>').append(period));
    })
    $table.append($homeRow);
    $table.append($awayRow);
    $scores.append($table);
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