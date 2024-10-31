<?php
class RelatedImages {

	function __construct() {
		add_action('add_meta_boxes', array($this, 'add_image_meta_box'));
		add_action('save_post', array($this, 'save_related_images'));
		add_action('admin_menu', array($this, 'plugin_admin_add_page'));
		add_action('admin_init', array($this, 'plugin_admin_init'));
	}


	// add the settings page
	function plugin_admin_init(){
		register_setting( 'plugin_options', 'related_images_options', array($this, 'plugin_options_validate') );
		add_settings_section('plugin_main', '', array($this, 'plugin_section_text'), 'related_images_plugin');
		add_settings_field('image_position_1', 'Image position 1', array($this, 'plugin_setting_string_1'), 'related_images_plugin', 'plugin_main');
		add_settings_field('image_position_2', 'Image position 2', array($this, 'plugin_setting_string_2'), 'related_images_plugin', 'plugin_main');
		add_settings_field('image_position_3', 'Image position 3', array($this, 'plugin_setting_string_3'), 'related_images_plugin', 'plugin_main');
		add_settings_field('image_position_4', 'Image position 4', array($this, 'plugin_setting_string_4'), 'related_images_plugin', 'plugin_main');
		add_settings_field('image_position_5', 'Image position 5', array($this, 'plugin_setting_string_5'), 'related_images_plugin', 'plugin_main');
	}

	function plugin_section_text() {
		echo '<p>Add your image positions here</p>';
	}

	function plugin_setting_string_1() {
		$options = get_option('related_images_options');
		echo "<input id='image_position_1' name='related_images_options[image_position_1]' size='40' type='text' value='{$options['image_position_1']}' />";
	}

	function plugin_setting_string_2() {
		$options = get_option('related_images_options');
		echo "<input id='image_position_2' name='related_images_options[image_position_2]' size='40' type='text' value='{$options['image_position_2']}' />";
	}

	function plugin_setting_string_3() {
		$options = get_option('related_images_options');
		echo "<input id='image_position_3' name='related_images_options[image_position_3]' size='40' type='text' value='{$options['image_position_3']}' />";
	}

	function plugin_setting_string_4() {
		$options = get_option('related_images_options');
		echo "<input id='image_position_4' name='related_images_options[image_position_4]' size='40' type='text' value='{$options['image_position_4']}' />";
	}

	function plugin_setting_string_5() {
		$options = get_option('related_images_options');
		echo "<input id='image_position_5' name='related_images_options[image_position_5]' size='40' type='text' value='{$options['image_position_5']}' />";
	}


	// do some validtion, better please :-)
	function plugin_options_validate($input) {
		return $input;
	}


	// add the Settings menu
	function plugin_admin_add_page() {
		add_options_page('Related Images Page', 'Related Images', 'manage_options', 'related_images_plugin', array($this, 'related_images_plugin_options_page'));
	}

	// the settings page
	function related_images_plugin_options_page() {

		echo "<div class=\"wrap\">";
		echo "<div class=\"icon32\" id=\"icon-options-general\"><br></div>";

			echo "<h2>Related Images Settings</h2>";
			echo "<form action=\"options.php\" method=\"post\">";

			settings_fields('plugin_options');
			do_settings_sections('related_images_plugin');

			echo "<p class=\"submit\">";
			echo "<input type=\"submit\" value=\"".esc_attr('Save Changes')."\" class=\"button-primary\" id=\"submit\" name=\"submit\"/>";
			echo "</p>";
			echo "</form>";

		echo "</div>";

	}

	// returne the "default" post_id, not the revision
	function get_correct_post_id($post_id){

		$post_id_parent = get_post($post_id)->post_parent;

    	// Determines if its a post revision
    	if(wp_is_post_revision($post_id)){
        	$post_id = wp_is_post_revision($post_id);
    	}

		return $post_id;
	}

	// save on 'save_post'
	function save_related_images($post_id){
		global $wpdb;

		// allways use the "default" post_id, not the revision one
		$post_id = $this->get_correct_post_id($post_id);

		// get saved positions
		if($_POST['saved_img_position']){
			foreach ($_POST['saved_img_position'] as $key => $value){
				if($value){
					$cleaned[$key] = $value;
				}
			}
		}

		// get new postitions
		if($_POST['img_position']){
			foreach ($_POST['img_position'] as $key => $value){
				if($value){
					$cleaned[$key] = $value;
				}
			}
		}

		// do not save if arr not exits, for example in the Quick "Edit"
		if($cleaned){

			// check if we have any positions at all
			$len = sizeof($cleaned);

			// if we do, save or not
			if($len > 0){
				update_option($post_id."_related_images", $cleaned);
			} else {
				delete_option($post_id."_related_images");
			}

		}

	}

	// the template tag
	function print_image($position = '', $width = 100 ,$height = 100, $class = '', $crop = ''){
		global $post;

		// get my options for this post
		$options = get_option($post->ID."_related_images");

		// get and crop the image
		if($options){
			foreach ($options as $key => $value){

				if($value == $position){

					$image_data = $this->get_image_data($key, $width, $height, $crop);

					if($class){ // add css-class
						$class = " class=\"".$class."\"";
					}

					echo "<img src=\"".$image_data['img_url']."\" ".$image_data['img_h_w']."".$class.">"; // add h & w

					if($image_data['description']){
						echo "<p class=\"description\">".$image_data['description']."</p>";
					}
					if($image_data['caption']){
						echo "<p class=\"caption\">".$image_data['caption']."</p>";
					}

				}
			}

		}

	}


	// crop, save and do the work to the image
	function get_image_data($id, $width = false, $height = false, $crop = ''){

		$img = array(); // tre return array

		$attachment = wp_get_attachment_metadata($id);
		$attachment_url = wp_get_attachment_url($id);

		if (isset($attachment_url)) {
			if ($width && $height) {
				$uploads = wp_upload_dir();
				$imgpath = $uploads['basedir'].'/'.$attachment['file'];

				if($attachment['file']){
					$image = image_resize( $imgpath, $width, $height, $crop );
					if ( $image && !is_wp_error( $image ) ) {
						$image = path_join( dirname($attachment_url), basename($image) );
					} else {
						$image = $attachment_url;
					}
				}

			} else {
				$image = $attachment_url;
			}
			if (isset($image)) {

				// add the url
				$img['img_url'] = $image;

				// get w/h
				if ($data = @getimagesize($image)) {
					$size = getimagesize($image);
					$img['img_h_w'] = $size[3];
				}

				// and add the image metdata
				$attachment_meta = get_post($id);
				$img['description'] = $attachment_meta->post_content;
				$img['caption'] = $attachment_meta->post_excerpt;
				return $img;

			}
		}
	}


	// add backend metabox
	function add_image_meta_box() {
		add_meta_box('related_images_meta_box_id', 'Related images', array($this, 'related_images_box'), 'post');
		add_meta_box('related_images_meta_box_id', 'Related images', array($this, 'related_images_box'), 'page');
	}

	// metabox fields
	function related_images_box($post){

		$options = get_option($post->ID."_related_images");

		echo "<p>";
		echo "<input type=\"text\" value=\"\" name=\"image_search_string\" id=\"image_search_string\">";
		echo "<input type=\"hidden\" id=\"image_search_nonce\" name=\"image_search_nonce\" value=\"".wp_create_nonce(__FILE__)."\" />";
		echo "&nbsp; <input name=\"do_search\" id=\"do_search\" type=\"submit\" value=\"Search\" />";
		echo "</p>";

		if($options){
			echo "<ul class=\"related_search_results_list saved\">";
			foreach ($options as $key => $value){
				$image_attributes = wp_get_attachment_image_src($key);
				if($image_attributes[0]){
					echo "<li id=\"img_".$key."\"><img src=\"".$image_attributes[0]."\"> ".$this->get_images_options($key,$value,'saved')."</li>";
				}
			}
			echo "</ul>";
		}

		echo "<div id=\"related_search_results\"></div>";

	}

	// checked or not?
	function check_position($position, $needle){
		if($position === $needle){
			return " selected=\"selected\"";
		}
	}

	// create the option list
	function get_images_options($image_id, $position = '', $state = ''){

		if($state == 'saved'){
			$drop = "<select data-img-id=\"".$image_id."\" class=\"img_position\" name=\"saved_img_position[".$image_id."]\">";
			$drop .= "<option >Remove</option>";
		} else {
			$drop = "<select class=\"img_position\" name=\"img_position[".$image_id."]\">";
			$drop .= "<option></option>";
		}

		// get positions from options
		$options = get_option('related_images_options');
		foreach ($options as $value) {
			if($value){
				$drop .= "<option value=\"".$value."\"".$this->check_position($position, $value).">".$value." &nbsp;</option>";
			}
		}


		$drop .= "</select>";

		return $drop;

	}

	// remova image
	function remove_image(){
		global $wpdb, $post;

		if (!wp_verify_nonce($_POST['image_search_nonce'],__FILE__)){
			die();
		}

		// get the search string
		$img_id = $_POST['img_id'];
		$post_ID = $_POST['post_ID'];

		// get saved options
		$related_images = get_option($post_ID."_related_images");

		// if there is only one saved, remove it all, or unset a specific image and then update the options
		$len = sizeof($related_images);

		if($related_images == 1){
			delete_option($post_ID."_related_images");
		} else {
			unset($related_images[$img_id]);
			update_option($post_ID."_related_images", $related_images);
		}

		die();

	}


	// run the search
	function do_image_search(){
		global $wpdb;

		$return = array();

		if (!wp_verify_nonce($_POST['image_search_nonce'],__FILE__)){
			die();
		}

		// get the search string
		$image_search_string = "%".$_POST['image_search_string']."%";

		// had to create the search by myself, did not find any native??
		$images = $wpdb->get_results(
			$wpdb->prepare("
			SELECT DISTINCT SQL_CALC_FOUND_ROWS {$wpdb->prefix}posts . *
			FROM {$wpdb->prefix}posts
			WHERE {$wpdb->prefix}posts.post_title
			LIKE  %s
			AND post_type = 'attachment'
			AND post_type != 'revision'
			AND post_status != 'future'
			ORDER BY post_date DESC LIMIT 20", $image_search_string ));

		if ( $images ) {
			foreach ( $images as $id => $image ) {

				$img = wp_get_attachment_thumb_url( $image->ID );
				$link = get_permalink( $post->ID );
				$drop = $this->get_images_options($image->ID);

				$item = array(
					'img_title' => $image->post_title,
					'img_url' => '<img src="'.$img.'">',
					'img_position' => $drop
				);

				array_push($return, $item);
			}
			echo json_encode($return);
		}
		die();
	}

}