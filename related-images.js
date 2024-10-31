jQuery(document).ready(function() {
	
	// run the search
	jQuery("#do_search").click(function(){
		jQuery('#related_search_results').html('');
		var image_search_string = jQuery("#image_search_string").val();
		var image_search_nonce = jQuery("#image_search_nonce").val();
		jQuery.post(ajaxurl, { action : 'search_related_images', image_search_string : image_search_string, image_search_nonce : image_search_nonce }, function(data) {
			var postBack = jQuery.parseJSON(data);
			if(postBack){
				var the_list = '';
				the_list = "<ul class=\"related_search_results_list\">";
				jQuery.each(postBack, function(key,item) {				
					the_list = the_list + "<li>" + item.img_url + ' ' + item.img_position + "</li>";
				});
				the_list = the_list + "</ul>";
				jQuery('#related_search_results').append(the_list);
			}
		});
		return false;
	});
	
	// remove element on "Remove"
	jQuery('.img_position').live('change', function() {
		var value = jQuery(this).val();
		var img_id = jQuery(this).attr('data-img-id');
		var image_search_nonce = jQuery("#image_search_nonce").val();
		var post_ID = jQuery("#post_ID").attr('value');		
		
		if(value === 'Remove'){
			jQuery('#img_' + img_id).css({ 'backgroundColor':'#fbe986' }).fadeOut(300, function() { jQuery(this).remove(); });

			// run the image remove
			jQuery.post(ajaxurl, { action : 'remove_related_images', img_id : img_id, post_ID : post_ID, image_search_nonce : image_search_nonce } );
			
			
		}
		return false;
	});
	
});
