<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       xavortm.com
 * @since      1.0.0
 *
 * @package    Test_Suit
 * @subpackage Test_Suit/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Test_Suit
 * @subpackage Test_Suit/admin
 * @author     Alex DImitrov <xavortm@gmail.com>
 */
class Test_Suit_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		$this->load_dependencies();

	}

	/**
	 * Load the required dependencies for the settings page
	 *
	 * Include the following files that make up the settings page:
	 *
	 * - Test_Suit_Settings. Setup and handle all settings.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/test-suit-admin-settings.php';
	
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Test_Suit_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Test_Suit_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/test-suit-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Test_Suit_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Test_Suit_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/test-suit-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Simply register the new settings page. Rendering is triggered from the
	 * render_settings_page() function below.
	 * 
	 * @since    1.0.0
	 */
	public function register_settings_page() {
		
		add_menu_page(
			'Theme Test Suit',
			'Theme Test Suit',
			'administrator',
			 __FILE__,
			array( $this, 'render_settings_page' )
		);

	}

	/**
	 * Handles the rendering of the custom settings page rhg
	 * 
	 * @since    1.0.0
	 */
	public function render_settings_page() {
		
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		/**
		 * Check if the user have submitted the settings
		 * WordPress will add the "settings-updated" $_GET parameter to the url
		 */ 
		if ( isset( $_GET['settings-updated'] ) ) {
			 add_settings_error( 'testsuit_messages', 'testsuit_message', __( 'Settings Saved', 'test-suit' ), 'updated' );
		}

		// Show any error/update messages
		settings_errors( 'testsuit_message' );

		// Handle all of the html markup in this separate file
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/test-suit-admin-display.php';

	}

}
