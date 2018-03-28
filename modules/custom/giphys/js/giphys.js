console.log(drupalSettings.giphys);

(function ($) {
  var $giphysList,
      giphysEndpoint,
      giphysSearchTerm;

  giphysEndpoint = drupalSettings.giphys.url + '?api_key=' + drupalSettings.giphys.secret;

  $giphysList = $('ul.giphys-list');

  $('#giphys-search').submit( function(e) {
    e.preventDefault();

    $giphysList.empty();

    giphysSearchTerm = $('#giphys-search-text').val();

    $.getJSON(giphysEndpoint + '&q=' + giphysSearchTerm).done(function(data) {
      if (data) {

        var $giphysListItem,
            giphysData = data.data,
            len = giphysData.length;

        for(var i = 0; i < len; i++) {
          $giphysListItem = '<li><img src="'+ giphysData[i].images.fixed_height_small.url +'" /></li>';
          $giphysList.append($giphysListItem);
        }
      }
    });

  });

})(jQuery);
