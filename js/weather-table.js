
jQuery(document).ready(function($) {
    $('#city-search').on('input', function() {
      var searchQuery = $(this).val();
      console.log(searchQuery);
      $.ajax({
        type: 'POST',
        url: ajax_object.ajax_url,
        data: {
          'action': 'city_search',
          'search_query': searchQuery
        },
        success: function(response) {
          console.log(response);
          // Update the weather table with the response data
        }
      });
    });
  });
