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
class Test_Suit_Checklist {


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
	 * @access   private
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
	 * Wrapper for wp's `add_settings_field()` to cleanup a bit the code
	 *
	 * There are tons of checkboxes to be used, wrapping them all in something
	 * that reuses the repeatable code will make everything easier to read.
	 *
	 * @access   private
	 * @since    1.0.0
	 */
	private function add_settings_field( $id, $description = '', $page = 'test-suit', $section_id = 'test_checklist', $args = array() ) {
		
		$cb = array( $this, $id . '_cb' );

		if ( empty ( $args ) ) {
			$args = array( 
				'label_for' => $id 
			);
		}

		add_settings_field( $id, __( $description, 'test-suit' ), $cb, $page, $section_id, $args );

	}

	/**
	 * Register all of the plugin's settings
	 *
	 * Note: You MUST register any options you use with add_settings_field() 
	 * or they won't be saved and updated automatically. See below for details and an example.
	 * The order of the functions here will decide the order of the fields in the page.
	 *
	 * @see  https://codex.wordpress.org/Settings_API
	 * @since    1.0.0
	 */
	public function register_settings() {
		
		register_setting( 'test_suit_checkboxes', 'test_suit_checkboxes', array( $this, 'test_suit_checkboxes_validate' ) ); 
		
		// Checklist before submitting the theme
		$this->add_settings_section(
			'test_checklist', // Section ID
			__( 'Things you need to check before submitting your theme to WordPress.org', 'test-suit' ),
			array( $this, 'test_checklist_section_cb' ),
			'test-suit' // The menu page on which to display this section. Should match $menu_slug
		);

		/**
		 * Use helper function to register the fields. It takes the ID and the label
		 * The callback is the same as the ID but with _cb suffix.
		 */
		$this->add_settings_field( 'test_checklist_functions','Uses prefixes' );
		$this->add_settings_field( 'test_checklist_use_available_functionality','Uses WordPress functionality' );
		$this->add_settings_field( 'test_checklist_only_theme','I have no other themes awaiting review' );
		$this->add_settings_field( 'test_checklist_theme_check_requirements','Theme check requirements' );
		$this->add_settings_field( 'test_checklist_enqueue_scripts','Scripts and styles are enqueued' );
		$this->add_settings_field( 'test_checklist_php_js_errors','No PHP or JS notices' );
		$this->add_settings_field( 'test_checklist_style_css','Has Style.css tags' );
		$this->add_settings_field( 'test_checklist_license','Copyright information' );
		$this->add_settings_field( 'test_checklist_customizer_options','Customizer options work' );
		$this->add_settings_field( 'test_checklist_translation','Is translationable' );
		$this->add_settings_field( 'test_checklist_demo_content','No demo content' );
		$this->add_settings_field( 'test_checklist_validate_data','Validate and sanitize all data' );
		$this->add_settings_field( 'test_checklist_screenshot','Has proper screenshot' );

	}

	/**
	 * Build checkbox helper function. 
	 *
	 * Since it looks messy and does some checks, it is best to have it as a helper 
	 * private function and simply pass the arguments to create the checkbox. 
	 * Plus separating into smaller blocks is always a plus
	 * 
	 * @access   private
	 * @since    1.0.0
	 */
	private function create_checkbox( $id, $description ) {
		
		$options = get_option( 'test_suit_checkboxes' );
		$checkbox_value = 0; // Default value
		
		if ( isset( $options[$id] ) ) {
			$checkbox_value = checked( 1, $options[$id], false );
		}

		$html = '<input type="checkbox" id="'. $id .'" name="'. 'test_suit_checkboxes' .'['. $id .']" value="1" ' . $checkbox_value . ' />';

		if ( ! empty( $description ) ) {
			$html .= '<em>' . __( $description, 'test-suit' ) . '</em>' ;
		}

		echo $html;
	}

	/**
	 * Callback functions for the plugin's sections and settings fields.
	 * All callback functions must be named in the same way as the ID and
	 * suffixed with _cb.
	 *
	 * The order of the functions is irrelevant
	 * =================================================================
	 */

	public function test_checklist_section_cb( $arg ) {
		echo '<p>' . __( 'Use this checklist to make sure you have everything needed before submitting your theme. The reason this is not automated is because even though the theme checker will give you very large portion of the feedback, there are still leftovers that need to be taken cared of from yourself. This is also themes are not automatically accepted in the WordPress repository.', 'test-suit') . '</p>';

		echo '<p>' . __( 'The list of things to check is not complete. Instead it shows the most common issues that appear in themes while reviewing them. For complete list of the requirements please visit the ', 'test-suit' ) . '<a href="https://make.wordpress.org/themes/handbook/review/required/" target="_blank">' . __( '"required" page in the WordPress handbook', 'test-suit' ) . '</a></p>';
	}

	public function test_checklist_only_theme_cb( $arg ) {
		$this->create_checkbox(
			'test_checklist_only_theme',
			'You are allowed to have only one theme ot be rewviewed at the same time.'
		);
	}

	public function test_checklist_screenshot_cb( $arg ) {
		$this->create_checkbox(
			'test_checklist_screenshot',
			'The screenshot must represent realistic view of the theme with no external stylings.'
		);
	}

	public function test_checklist_functions_cb( $arg ) {
		$this->create_checkbox(
			'test_checklist_functions',
			'Make sure to prefix your functions or wrap them in classes depending on your code structure.'
		);
	}

	public function test_checklist_enqueue_scripts_cb() {
		$this->create_checkbox(
			'test_checklist_enqueue_scripts',
			'All scripts and styles must be enqueued. Do not add them directly into the templating files.'
		);
	}

	public function test_checklist_style_css_cb() {
		$this->create_checkbox(
			'test_checklist_style_css',
			'Style.css tags and descriptions must corespondend properly to the theme\'s features.'
		);
	}
	
	public function test_checklist_customizer_options_cb() {
		$this->create_checkbox(
			'test_checklist_customizer_options',
			'All customizer options must work properly. Make sure to test all of them as a cleanly installed theme.'
		);
	}

	public function test_checklist_demo_content_cb() {
		$this->create_checkbox(
			'test_checklist_demo_content',
			'Do not add demo content inside the theme. Let the user add all of it. Messages are not considered content.'
		);
	}

	public function test_checklist_translation_cb() {
		$this->create_checkbox(
			'test_checklist_translation',
			'The theme must be translationable. Use the existing functions like _e() or __() among others to achieve that.'
		);
	}

	public function test_checklist_license_cb() {
		$this->create_checkbox(
			'test_checklist_license',
			'You must point out the license of ALL assets that are being used in the theme. That includes images, icons, fonts and everything else.'
		);
	}

	public function test_checklist_php_js_errors_cb() {
		$this->create_checkbox(
			'test_checklist_php_js_errors',
			'The theme should not produce and PHP or JS errors, warnings or notices'
		);
	}

	public function test_checklist_validate_data_cb() {
		$this->create_checkbox(
			'test_checklist_validate_data',
			'Validate and/or sanitize untrusted data before entering into the database. All untrusted data should be escaped before output'
		);
	}

	public function test_checklist_theme_check_requirements_cb() {
		$this->create_checkbox(
			'test_checklist_theme_check_requirements',
			'The theme must meet all of the theme check requirements.'
		);

		// Some extra information
		echo '<a target="_blank" href="https://make.wordpress.org/themes/handbook/review/required/theme-check-plugin/">See more</a>';
	}

	public function test_checklist_use_available_functionality_cb() {
		$this->create_checkbox(
			'test_checklist_use_available_functionality',
			'If incorporated, features must support the WordPress functionality. Make sure to check the codex if there\'s already a function you can use'
		);

		// Some extra information
		echo '<a target="_blank" href="https://developer.wordpress.org/themes/functionality/">See more</a>';
	}


	public function test_suit_checkboxes_validate( $input ) {
		
		// Check our textbox option field contains no HTML tags - if so strip them out
		$input['text_string'] =  wp_filter_nohtml_kses( $input['text_string'] );	
		
		return $input; // return validated input

	}

}