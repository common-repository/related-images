<?php

/**
 * Class Name: Config
 */

namespace RI;


class Config {


	private static $instance;


	public static function get( $key ) {

		if( ! isset( self::$instance ) ) {
			self::$instance = new Config;
		}

		$arr = self::$instance->config_array();

		return $arr[$key];


	}


	// here we arr configs we need
	private static function config_array(){

		return array(

			'related_images' => '_related_images',
			'pre-defined-positions' => array(
				'startpage' => 'Startpage',
				'post' => 'Post'
			)

		);

	}


}