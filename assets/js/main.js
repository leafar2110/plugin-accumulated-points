jQuery(function($){



	$('#rudr_select2_tags').select2();
	// multiple select with AJAX search
	$('#rudr_select2_posts').select2({
  		ajax: {
    			url: global.ajax, // AJAX URL is predefined in WordPress admin
    			dataType: 'json',
    			delay: 250, // delay in ms while typing when to perform a AJAX search
    			data: function (data) {
					console.log(data.term);
					
      				return {
					
        				searchTerm: data.term, // search query
        				action: 'search_site' // AJAX action for admin-ajax.php
      				};
					  
    			},
			
    			processResults: function( data ) {
					
				var options = [];
				
				if ( data ) {
			
					// data is the array of arrays, and each of them contains ID and the Label of the option
					$.each( data.data, function( index, text ) { // do not forget that "index" is just auto incremented value
						options.push( { id: text['ID'], text: text['post_title']  } );
						console.log(options);
					});
				
				}
				return {
					results: options
				};
				
			},
			
			cache: true,
		},
		minimumInputLength: 3 // the minimum of symbols to input before perform a search
	});
});