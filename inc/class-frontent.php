<?php
/**
 * Backend class
 *
 * usage
 * echo \RI\Frontend::get()->get_image( 'startpage' );
 */

namespace RI;

class Frontend  {

	private static $instance;


	/**
	 * Initialize
	 */
	public static function get() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new Frontend;
		}

		return self::$instance;
	}


	/**
	 * return image on current post ID and position
	 */
	public function get_image( $position, $height = '', $width = '' ){
		global $post;

		return "xx";

		$key = $post->ID.'_'.\RI\Config::get('related_images');

		$related_images = get_post_meta($post->ID, $key);

		print_r($related_images);

	}

}

Frontend::get();