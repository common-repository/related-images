<?php
/**
 * Tpl class
 *
 * usage:
 *
 * $vars = array();
 * $vars['title'] = 'Some string';
 * echo \RI\Tpl::get()->process_template( 'test', $vars );
 *
 */

namespace RI;

class Tpl  {

	private static $instance;


	/**
	 * Initialize
	 */
	public static function get() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new Tpl;
		}

		return self::$instance;
	}


	/**
	 * Get a template  name
	 */
	protected function get_template( $name ) {

		return RI_TPL_DIR . $name . '.php';

	}


	/**
	 * Get and return html from a template
	 */
	public function process_template( $template_name, $vars = array() ) {

		extract( $vars );
		ob_start();

		$full_template_path = $this->get_template( $template_name );

		if ( file_exists( $full_template_path ) ) {
			include $full_template_path;
		}

		return ob_get_clean();
	}

}