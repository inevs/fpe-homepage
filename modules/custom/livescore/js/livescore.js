console.log(drupalSettings.livescore);

function jsonCallback(json){
  console.log(json);
}

(function ($) {  
  $.ajax({
    url: "https://footballscores.herokuapp.com/games/576.json?callback=?",
    dataType: "jsonp"
  });  
})(jQuery);