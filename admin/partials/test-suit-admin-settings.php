<?php

/**
 * Register all of the plugin's settings in one place.
 *
 * @link       xavortm.com
 * @since      1.0.0
 *
 * @package    Test_Suit
 * @subpackage Test_Suit/admin/partials
 */

/**
 * Defines the plugin's settings
 *
 * Keeping this in separate class for better separation. Only 
 * register and modify settings here.
 *
 * @package    Test_Suit
 * @subpackage Test_Suit/admin/partials
 * @author     Alex DImitrov <xavortm@gmail.com>
 */
class Test_Suit_Settings {


	/**
	 * Tracks new sections for whitelist_custom_options_page()
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var array
	 */
	private $page_sections;

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
		$this->page_sections = array();

	}

	/**
	 * Must run after wp's `option_update_filter()`, so priority > 10
	 * 
	 * @since    1.0.0
	 */
	public function whitelist_custom_options_page( $whitelist_options ) {
		
		// Custom options are mapped by section id; Re-map by page slug.
		foreach( $this->page_sections as $page => $sections ) {
			$whitelist_options[$page] = array();
			
			foreach( $sections as $section ) {
				if ( ! empty( $whitelist_options[$section] ) ) {
					foreach( $whitelist_options[$section] as $option ) {
						$whitelist_options[$page][] = $option;
					}
				}
			} // end foreach

		} // end foreach

		return $whitelist_options;

	}

	/**
	 * Wrapper for wp's `add_settings_section()` that tracks custom sections
	 * 
	 * @since    1.0.0
	 */
	private function add_settings_section( $id, $title, $cb, $page ){
		
		add_settings_section( $id, $title, $cb, $page );

		if ( $id != $page ){
			if( ! isset( $this->page_sections[$page]) ) {
				$this->page_sections[$page] = array();
			}
		
			$this->page_sections[$page][$id] = $id;
		}

	}

	/**
	 * Register all of the plugin's settings
	 *
	 * Note: You MUST register any options you use with add_settings_field() 
	 * or they won't be saved and updated automatically. See below for details and an example.
	 *
	 * @see  https://codex.wordpress.org/Settings_API
	 * @since    1.0.0
	 */
	public function register_settings() {
		
		register_setting( 'test_suit_checkboxes', 'test_suit_checkboxes', array( $this, 'test_suit_checkboxes_validate' ) ); 
		
		// Checklist before submitting the theme
		$this->add_settings_section(
			'test_checklist', // Section ID
			__( 'Checklist before submitting your theme', 'test-suit' ),
			array( $this, 'test_checklist_section_cb' ),
			'test-suit' // The menu page on which to display this section. Should match $menu_slug
		);

		add_settings_field( 
			'test_checklist_screenshot',
			__( 'Has screenshot of the homepage', 'test-suit' ),
			array( $this, 'test_checklist_screenshot_cb' ),
			'test-suit',
			'test_checklist', // Section ID
			array( 
				'label_for' => 'test_checklist_screenshot' 
			)
		);


	}

	/**
	 * Callback functions for the plugin's sections and settings fields.
	 * =================================================================
	 */

	public function test_checklist_section_cb( $arg ) {
		echo '<p>' . __( 'Use this checklist to make sure you have everything needed before submitting your theme. The reason this is not automated is because even though the theme checker will give you very large portion of the feedback, there are still leftovers that need to be taken cared of from yourself. This is also themes are not automatically accepted in the WordPress repository.', 'test-suit') . '</p>';
	}

	public function test_checklist_screenshot_cb( $arg ) {

		$options = get_option( 'test_suit_checkboxes' );

		$html = '<input type="checkbox" id="test_checklist_screenshot" name="test_suit_checkboxes[test_checklist_screenshot]" value="1" ' . checked( 1, $options['test_checklist_screenshot'], false ) . ' />';

		$html .= __( 'The screenshot must represent realistic view of the theme with no external stylings.', 'test-suit');

		echo $html;
	}

	public function test_suit_checkboxes_validate( $input ) {
		
		// Check our textbox option field contains no HTML tags - if so strip them out
		$input['text_string'] =  wp_filter_nohtml_kses( $input['text_string'] );	
		
		return $input; // return validated input

	}

}