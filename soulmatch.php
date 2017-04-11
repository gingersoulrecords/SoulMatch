<?php
/*
Plugin Name: SoulMatch
Plugin URI: http://gingersoulrecords.com/soulmatch
Description: Equalize the heights of grouped elements.
Version: 0.1.1
Author: Dave Bloom
Author URI: http://gingersoulrecords.com
Text Domain: soulmatch
*/

add_action( 'plugins_loaded', array( 'SoulMatch', 'init' ) );

class SoulMatch {
	public static $options = array(
		'a' => 'test',
		'b'	=> 'test2',
		'lists_check'	=> true,
	);
	public static $settings = false;
	public static $plugin_path = '';
	public static function init() {
		self::$plugin_path = plugin_dir_path( __FILE__ );

		add_action( 'wp_enqueue_scripts', 		array( 'SoulMatch', 'scripts' ) );
		add_action( 'admin_enqueue_scripts', 	array( 'SoulMatch', 'admin_scripts' ) );

		add_action( 'wp_enqueue_scripts', 		array( 'SoulMatch', 'styles' ) );
		add_action( 'admin_enqueue_scripts', 	array( 'SoulMatch', 'admin_styles' ) );

		// tinyOptions v 0.3.0
		// self::$options = wp_parse_args( get_option( 'soulmatch_options' ), self::$options );
		// add_action( 'plugins_loaded', array( 'SoulMatch', 'init_options' ), 9999 - 0030 );

		// SoulRepeater v0.1.0
		add_action( 'plugins_loaded', array( 'SoulMatch', 'init_repeater' ), 9999 - 0010 );

	}

	public static function scripts() {
		wp_register_script( 'matchheight', plugins_url( 'js/jquery.matchHeight-min.js', __FILE__ ), array( 'jquery' ), false, true );
		wp_register_script( 'soulmatch', plugins_url( 'soulmatch.js', __FILE__ ), array( 'jquery','matchheight' ), false, true );
		$data = array(
			'options'	=> get_option('soulmatch_repeater'),
		);
		wp_localize_script( 'soulmatch', 'soulmatch_data', $data );
		wp_add_inline_script( 'soulmatch', 'var soulmatch_after = "";', 'after' );
		wp_enqueue_script( 'soulmatch' );
	}
	public static function admin_scripts() {
		$screen = get_current_screen();
		if ( 'settings_page_soulmatch-settings' != $screen->id) {
			return false;
		}

		wp_register_script( 'soulmatch-admin', plugins_url( 'soulmatch-admin.js', __FILE__ ), array( 'jquery' ), false, true );
		$data = array(
			'options'	=> self::$options,
		);
		wp_localize_script( 'soulmatch-admin', 'soulmatch_admin_data', $data );
		wp_add_inline_script( 'soulmatch-admin', 'var soulmatch_admin_after = "";', 'after' );
		wp_enqueue_script( 'soulmatch-admin' );
	}

	public static function styles() {
		// wp_register_style( 'somestyle', plugins_url( 'css/somestyle.css', __FILE__ ), array( 'dashicons' ) );
		//wp_register_style( 'soulmatch', plugins_url( 'soulmatch.css', __FILE__ ), array( 'dashicons' ) );
		//wp_add_inline_style( 'soulmatch', '.soulmatch { color:blue; }' );
		//wp_enqueue_style( 'soulmatch' );
	}
	public static function admin_styles() {
		$screen = get_current_screen();
		if ( 'settings_page_soulmatch-settings' != $screen->id) {
			return false;
		}
		// wp_register_style( 'somestyle', plugins_url( 'css/somestyle.css', __FILE__ ), array( 'dashicons' ) );
		wp_register_style( 'soulmatch-admin', plugins_url( 'soulmatch-admin.css', __FILE__ ), array( 'dashicons' ) );
		wp_add_inline_style( 'soulmatch-admin', '.soulmatch { color:blue; }' );
		wp_enqueue_style( 'soulmatch-admin' );
	}

	public static function init_repeater() {
		$settings = array(
			'links' => array(
				'file'	=> plugin_basename( __FILE__ ),
				'links' => array(
					array(
						'title'	=> __( 'Settings', 'soulmatch' ),
					),
				),
			),
			'page' => array(
				'title' 			=> __( 'SoulMatch Settings', 'soulmatch' ),
				'menu_title'	=> __( 'SoulMatch', 'soulmatch' ),
				'slug' 				=> 'soulmatch-settings',
				'option'			=> 'soulmatch_repeater',
				// optional
				'description'	=> __( 'Some general information about the plugin', 'soulmatch' ),
			),
			'fields' => array(
				'selector' => array(
					'title'	=> __( 'Selector', 'soulmatch' ),
					'attributes'	=> array(
						'placeholder'	=> __( 'Selector', 'soulmatch' ),
					),
				),
				'byrow' => array(
					'title'	=> __( 'By Row', 'soulmatch' ),
					'label'	=> __( 'Check this to only equalize heights if the selected elements are in the same row.', 'soulmatch' ),
					'callback'	=> 'checkbox',
				),
			),
			'l10n' => array(
				'no_access'			=> __( 'You do not have sufficient permissions to access this page.', 'soulmatch' ),
				'save_changes'	=> esc_attr__( 'Save Changes', 'soulmatch' ),
				'add_repeater'	=> __( 'Add Repeater', 'soulmatch' ),
				'delete_repeater'	=> __( 'Delete Repeater', 'soulmatch' ),
				'repeater'			=> __( 'Repeater', 'soulmatch' ),
				'save_success'	=> __( 'Saved successfully.', 'soulmatch' ),
				'nonce_error'		=> __( 'Nonce verification failed.', 'soulmatch' ),
			),
		);
		require_once( self::$plugin_path . 'tiny/soul.repeater.php' );
		$settings = new SoulRepeater( $settings, __CLASS__ );
	}

	public static function init_options() {
		self::$settings = array(
			'page' => array(
				'title' 			=> __( 'SoulMatch Settings', 'soulmatch' ),
				'menu_title'	=> __( 'SoulMatch', 'soulmatch' ),
				'slug' 				=> 'soulmatch-settings',
				'option'			=> 'soulmatch_options',
				// optional
				'description'	=> __( 'Some general information about the plugin', 'soulmatch' ),
			),
			'sections' => array(
				'inputs' => array(
					'title'				=> __( 'Section #1 - Inputs', 'soulmatch' ),
					'description'	=> __( 'Showcases various <code>&lt;input&gt;</code> based fields', 'soulmatch' ),
					'fields'	=> array(
						'input_group'		=> array(
							'title'	=> __( 'Group Input', 'soulmatch' ),
							'callback'	=> 'group',
							'attributes'	=> array(
							),
							'fields' => array(
								'input_simple' => array(
									'title'	=> __( 'Simple Input', 'soulmatch' ),
									'attributes'	=> array(
										'placeholder'	=> __( 'Selector', 'soulmatch' ),
									),
								),
								'lists_check' => array(
									'title'	=> __( 'Checkbox', 'soulmatch' ),
									'label'	=> __( 'Checkbox', 'soulmatch' ),
									'callback'	=> 'checkbox',
								),
								'lists_checkbox' => array(
									'title'	=> __( 'Select Checkbox', 'soulmatch' ),
									'list'	=> array( ''=>'-none-', 'a' => 'A', 'b' => 'B' ),
									'callback'	=> 'listfield',
									'attributes'	=> array(
										'type'	=> 'select',
									)
								),
							),
						),
						'input_simple' => array(
							'title'	=> __( 'Simple Input', 'soulmatch' ),
						),
						'input_description' => array(
							'title'	=> __( 'Simple Input', 'soulmatch' ),
							'description'	=> __( 'With a description', 'soulmatch' ),
						),
						'input_placeholder' => array(
							'title'	=> __( 'Paceholder Input', 'soulmatch' ),
							'attributes' => array(
								'placeholder'	=> __( 'Placeholder example', 'soulmatch' ),
							),
						),
						'input_number' => array(
							'title'	=> __( 'Number Input', 'soulmatch' ),
							'attributes' => array(
								'type'	=> 'number',
								'step'	=> 2,
								'min'		=> 10,
							),
						),
						'input_password' => array(
							'title'	=> __( 'Password Input', 'soulmatch' ),
							'attributes' => array(
								'type'	=> 'password',
							),
						),
					),
				),
				'lists' => array(
					'title'	=> __( 'Section #2 - lists and checkboxes', 'soulmatch'),
					'description'	=> __( 'Checkbox lists, radiobox list, select, checkbox, etc.', 'soulmatch' ),
					'fields'	=> array(
						'lists_check' => array(
							'title'	=> __( 'Checkbox', 'soulmatch' ),
							'label'	=> __( 'Checkbox', 'soulmatch' ),
							'callback'	=> 'checkbox',

						),
						'lists_select' => array(
							'title'	=> __( 'Select', 'soulmatch' ),
							'list'	=> array( ''=>'-none-', 'a' => 'A', 'b' => 'B' ),
							'callback'	=> 'listfield',
						),
						'lists_select_multi' => array(
							'title'	=> __( 'Select Multiple', 'soulmatch' ),
							'list'	=> array( ''=>'-none-', 'a' => 'A', 'b' => 'B', 'c'=> 'C', 'd'=>'D' ),
							'callback'	=> 'listfield',
							'attributes' => array(
								'size'	=> 3,
								'multiple'=>true,
							)
						),
						'lists_radio' => array(
							'title'	=> __( 'Select Radiobutton', 'soulmatch' ),
							'list'	=> array( ''=>'-none-', 'a' => 'A', 'b' => 'B' ),
							'callback'	=> 'listfield',
							'attributes'	=> array(
								'type'	=> 'radio',
							)
						),
						'lists_checkbox' => array(
							'title'	=> __( 'Select Checkbox', 'soulmatch' ),
							'list'	=> array( ''=>'-none-', 'a' => 'A', 'b' => 'B' ),
							'callback'	=> 'listfield',
							'attributes'	=> array(
								'type'	=> 'checkbox',
							)
						),
					),
				),
				'misc' => array(
					'title'	=> __( 'Section #3 - other fields', 'soulmatch'),
					'description'	=> __( 'Other types of fields', 'soulmatch' ),
					'fields'	=> array(
						'textarea' => array(
							'title'	=> __( 'Textarea', 'soulmatch' ),
							'callback'	=> 'textarea',
							'attributes'	=> array(
								'rows' => 10,
								'cols' => 30,
							)
						),
						'link' => array(
							'title'	=> __( 'Link', 'soulmatch' ),
							'label'	=> __( 'Click', 'soulmatch' ),
							'attributes'	=> array(
								'href' => '?gogogo',
								// 'class' => 'button',
							),
							'callback'	=> 'url',
						),
						'link_button' => array(
							'title'	=> __( 'Link Button', 'soulmatch' ),
							'label'	=> __( 'Click', 'soulmatch' ),
							'attributes'	=> array(
								'href' => '?gogogo',
								'class' => 'button',
							),
							'callback'	=> 'url',
						),
						'link_primary' => array(
							'title'	=> __( 'Link Primary', 'soulmatch' ),
							'label'	=> __( 'Click', 'soulmatch' ),
							'attributes'	=> array(
								'href' => '?gogogo',
								'class' => 'button button-primary',
								'target'	=> '_blank',
							),
							'callback'	=> 'url',
						),
					),
				),
			),
			'l10n' => array(
				'no_access'			=> __( 'You do not have sufficient permissions to access this page.', 'soulmatch' ),
				'save_changes'	=> esc_attr( 'Save Changes', 'soulmatch' ),
			),
		);
		require_once( self::$plugin_path . 'tiny/tiny.options.php' );
		self::$settings = new tinyOptions( self::$settings, __CLASS__ );
	}
}
