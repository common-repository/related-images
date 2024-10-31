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
	 * return a gallery
	 */
	public function get_gallery(){

	}

	/**
	 * return single image
	 */
	public function get_image( $position, $size = 'thumbnail' ){
		global $post;

		// get the key
		$key = $post->ID.'_'.\RI\Config::get('related_images');

		// get the post meta
		$related_images = get_post_meta($post->ID, $key);

		if( $related_images ) {

			foreach ( $related_images as $key => $value ) {

				foreach ($value as $item) {

					if( $item['position'] == $position){

						// the old style
						//$img = wp_get_attachment_image( $item['img_id'], 'full' );

						// the responsive style
						$src = wp_get_attachment_image_src( $item['img_id'], $size );
						$img = '<img src="'.$src[0].'">';

						return $img;

					}


				}


			}
		}
	}





}

Frontend::get();