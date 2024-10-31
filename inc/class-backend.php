<?php
/**
 * Backend class
 *
 * nonce, validera postningen
 * frontend, get_image('position');
 */

namespace RI;

class Backend  {


	private static $instance;

	protected $option_position_key = 'related_images_pos_key';

	/**
	 * Construct
	 */
	public function __construct(){

		add_action('save_post', array( $this, 'save_related_images' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_ri_meta_box' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'scripts' ) );
		add_action("wp_ajax_add_position",  array( $this, 'handle_add_position') );

	}


	/**
	 * return the instance
	 */
	public static function get() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new Backend;
		}

		return self::$instance;
	}


	/**
	 * Load scripts we need
	 * */
	public function scripts() {

		// Enqueues all scripts, styles, settings, and templates necessary to use all media JavaScript APIs.
		wp_enqueue_media();

		// Add scripts
		wp_register_script( 'ri-media', RI_PLUGIN_URL.'js/media.js', array( 'jquery' ), '1.0.0', true );

		// get positions
		$pre_defined_positions = \RI\Config::get('pre-defined-positions');

		$positions_as_json = json_encode($pre_defined_positions);

		wp_localize_script( 'ri-media', 'ri_media',
			array(
				'title' => __( 'Upload or Choose Your Image Files' ),
				'button' => __( 'Add related images' ),
				'positions' => $positions_as_json,
				'ajax_nonce' => wp_create_nonce('related_images'),
			)
		);

		wp_enqueue_script( 'ri-media' );

	}


	/**
	 * add a new position
	 */
	public function handle_add_position() {

		// verify nonce
		if ( !wp_verify_nonce( $_REQUEST['nonce'], "related_images")) {
			die();
		}

		if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
			$return = $this->update_positions( $_REQUEST['position'] );
			if ( $return ) {
				$result['type'] = "success";
			} else {
				$result['type'] = "error";
			}
		} else {
			$result['type'] = "error";
		}

		$result = json_encode( $result );
		echo $result;

		die();

	}


	/**
	 * Update positions
	 */
	public function update_positions( $position ) {

		if ( !$position ) {
			return false;
		}

		$position = trim( $position );
		$current_positions = $this->get_positions();

		if ( $current_positions ) {
			array_push( $current_positions, $position );
			update_option( $this->option_position_key , $current_positions );
			return true;
		} else {
			$pos = array();
			array_push( $pos, $position );
			update_option( $this->option_position_key , $pos );
			return true;
		}

	}


	/**
	 * get config and custom positions
	 * merge and return
	 */
	public function get_all_positions(){

		// get pre-defined positions
		$pre_defined_positions = \RI\Config::get('pre-defined-positions');

		// get custum positions
		$custum_positions = \RI\Backend::get()->get_positions();

		$return = array();

		// add configured positions
		foreach ($pre_defined_positions as $pos_key => $pos) {
			$return[$pos_key] = $pos;
		}

		// if we have any custom positions, add them
		if ( $custum_positions ) {
			foreach ($custum_positions as $key => $value) {
				$return[$value] = ucfirst($value);
			}
		}

		return $return;

	}

	/**
	 * get existing positions
	 */
	public function get_positions(){

		return get_option( $this->option_position_key );

	}


	/**
	 * Add metabox
	 */
	public function add_ri_meta_box() {

		add_meta_box( 'ri_meta_box_id', 'Related images', array( $this, 'ri_box' ), 'post' );

	}

	/**
	 * print the meta box
	 */
	public function ri_box( $post ) {

		$vars = array();

		$key = $post->ID.'_'.\RI\Config::get('related_images');

		$vars['related_images'] = get_post_meta( $post->ID, $key, true );

		echo \RI\Tpl::get()->process_template( 'meta-box', $vars );

	}


	/**
	 * save images and positions
	 */
	public function save_related_images( $post_id ){

		/**
		 * Don't save on autosave
		 */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;


		/**
		 * Check the permissions
		 */
		if ( ! current_user_can( 'edit_post', $post_id ) )
			return $post_id;


		/**
		 * get the data
		 */
		$data = $this->get_images_and_positions( $_POST['related-images-select'] );


		/**
		 * save
		 */
		$this->save_metadata_fields( $post_id, $data );


	}

	/**
	 * split up the post and figure out images and positions
	 */
	public function get_images_and_positions( $related_images ){

		$return = array();

		foreach ($related_images as $key => $value) {

			$exploded = explode('_', $value);

			$item = array(
				'img_id' => $exploded[0],
				'position' => $exploded[1],
			);

			array_push($return, $item);

		}

		return $return;

	}



	/**
	 * Save or delete the metadata
	 */
	protected function save_metadata_fields( $post_id, $data ) {

		$nbr = count( $data );

		$key = $post_id.'_'.\RI\Config::get('related_images');

		if ( $nbr > 0 ) {
			update_post_meta( $post_id, $key, $data );
		} else {
			delete_post_meta( $post_id, $key );
		}

	}




}

Backend::get();