<?php
/**
 * Admin settings page for Datasheets plugin.
 *
 * Registers the settings option, adds the "Settings" submenu under the
 * Datasheets top‑level menu and renders the page (title, form and a reference
 * list of fields for the selected post type).
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'Datasheets_Admin_Settings_Page' ) ) :

class Datasheets_Admin_Settings_Page {

	/**
	 * Constructor – set up hooks.
	 */
	public function __construct() {
		// 1) Register option and form fields.
		add_action( 'admin_init', [ $this, 'register_settings' ] );

		// 2) Add the Settings submenu *after* WP adds CPT links (priority > 99).
		add_action( 'admin_menu', [ $this, 'add_submenu' ], 120 );
	}

	/**
	 * Add "Settings" under the Datasheets parent menu.
	 */
	public function add_submenu() {
		add_submenu_page(
			'datasheets',
			__( 'Settings', 'datasheets' ),
			__( 'Settings', 'datasheets' ),
			'manage_options',
			'datasheet_settings',
			[ $this, 'render_page' ],
			20
		);
	}

	/**
	 * Register the option, section and field using the Settings API.
	 */
	public function register_settings() {
		register_setting(
			'datasheets_settings_group',
			'datasheets_post_type',
			[
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_key',
				'default'           => '',
			]
		);

		add_settings_section(
			'datasheets_main_section',
			__( 'General', 'datasheets' ),
			[ $this, 'section_description' ],
			'datasheets_settings_page'
		);

		add_settings_field(
			'datasheets_post_type_field',
			__( 'Select a Post Type', 'datasheets' ),
			[ $this, 'field_post_type_dropdown' ],
			'datasheets_settings_page',
			'datasheets_main_section'
		);
	}

	/**
	 * Section description.
	 */
	public function section_description() {
		echo '<p>' . esc_html__( 'Choose which post type your datasheets reference. This controls which fields are available in the template builder.', 'datasheets' ) . '</p>';
	}

	/**
	 * Dropdown of all UI‑visible post types.
	 */
	public function field_post_type_dropdown() {
		$selected   = get_option( 'datasheets_post_type', '' );
		$post_types = get_post_types( [ 'show_ui' => true ], 'objects' );

		echo '<select name="datasheets_post_type" id="datasheets_post_type" class="regular-text">';
		echo '<option value="">' . esc_html__( '— Select —', 'datasheets' ) . '</option>';
		foreach ( $post_types as $pt ) {
			printf(
				'<option value="%1$s" %3$s>%2$s</option>',
				esc_attr( $pt->name ),
				esc_html( $pt->labels->singular_name ),
				selected( $selected, $pt->name, false )
			);
		}
		echo '</select>';
	}

	/**
	 * Render the settings page.
	 */
	public function render_page() {
		echo '<div class="wrap">';
		echo '<h1>' . esc_html__( 'Settings', 'datasheets' ) . '</h1>';
		echo '<p>' . esc_html__( 'Configure how Datasheets interacts with your chosen post type. After you save a post type, a reference list of its available fields will appear below the form.', 'datasheets' ) . '</p>';

		echo '<form method="post" action="options.php">';
		settings_fields( 'datasheets_settings_group' );
		do_settings_sections( 'datasheets_settings_page' );
		submit_button();
		echo '</form>';

		$this->output_fields_reference();
		echo '</div>';
	}

	/**
	 * Print a reference list of fields for the selected post type.
	 */
	private function output_fields_reference() {
		$pt = get_option( 'datasheets_post_type', '' );
		if ( ! $pt || ! post_type_exists( $pt ) ) {
			return;
		}

		echo '<hr>';
		printf( '<h2>%s</h2>', esc_html( sprintf( __( 'Fields available for “%s”', 'datasheets' ), $pt ) ) );

		// Core WP_Post properties
		$core_fields = [
			'ID', 'post_title', 'post_content', 'post_excerpt', 'post_status', 'post_name',
			'post_author', 'post_date', 'post_modified', 'post_parent', 'menu_order',
		];
		echo '<p><strong>' . esc_html__( 'Core fields', 'datasheets' ) . '</strong></p><pre>';
		echo esc_html( implode( "\n", $core_fields ) );
		echo '</pre>';

		// Taxonomies
		$taxonomies = get_object_taxonomies( $pt, 'objects' );
		if ( $taxonomies ) {
			echo '<p><strong>' . esc_html__( 'Taxonomies', 'datasheets' ) . '</strong></p><pre>';
			foreach ( $taxonomies as $tax ) {
				echo esc_html( $tax->name ) . "\n";
			}
			echo '</pre>';
		}

		// Custom meta keys (limit 100)
		global $wpdb;
		$meta_keys = $wpdb->get_col( $wpdb->prepare(
			"SELECT DISTINCT meta_key FROM {$wpdb->postmeta} WHERE post_id IN ( SELECT ID FROM {$wpdb->posts} WHERE post_type = %s ) LIMIT 100",
			$pt
		) );

		if ( $meta_keys ) {
			echo '<p><strong>' . esc_html__( 'Custom fields (meta keys)', 'datasheets' ) . '</strong></p><pre>';
			foreach ( $meta_keys as $key ) {
				echo esc_html( $key ) . "\n";
			}
			echo '</pre>';
		}
	}
}

endif; // class guard

// Instantiate the page when in admin context.
if ( is_admin() ) {
	new Datasheets_Admin_Settings_Page();
}
