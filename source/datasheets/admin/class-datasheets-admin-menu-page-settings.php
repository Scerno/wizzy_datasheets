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
	private function output_fields_reference_old() {
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
		};
	}
	
	/**
	 * Print a reference list of fields for the selected post type.
	 */
	private function output_fields_reference() {
		$pt = get_option( 'datasheets_post_type', '' );
		if ( ! $pt || ! post_type_exists( $pt ) ) {
			return;
		}

		printf(
			'<hr><h2>%s</h2>',
			esc_html( sprintf( __( 'Fields available for “%s”', 'datasheets' ), $pt ) )
		);

		/*--------------------------------------------------------------*/
		/* Core WP_Post properties
		/*--------------------------------------------------------------*/
		$core_fields = [
			'ID', 'post_title', 'post_content', 'post_excerpt', 'post_status', 'post_name',
			'post_author', 'post_date', 'post_modified', 'post_parent', 'menu_order',
		];
		echo '<p><strong>' . esc_html__( 'Core fields', 'datasheets' ) . '</strong></p><pre>';
		echo esc_html( implode( "\n", $core_fields ) );
		echo '</pre>';

		/*--------------------------------------------------------------*/
		/* Taxonomies
		/*--------------------------------------------------------------*/
		$taxonomies = get_object_taxonomies( $pt, 'objects' );
		if ( $taxonomies ) {
			echo '<p><strong>' . esc_html__( 'Taxonomies', 'datasheets' ) . '</strong></p><pre>';
			foreach ( $taxonomies as $tax ) {
				echo esc_html( $tax->name ) . "\n";
			}
			echo '</pre>';
		}

		/*--------------------------------------------------------------*/
		/* Custom meta keys – keep unique, human-usable names (≤ 100)   */
		/*--------------------------------------------------------------*/
		global $wpdb;
		$all_keys = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT meta_key
				   FROM {$wpdb->postmeta}
				  WHERE post_id IN ( SELECT ID FROM {$wpdb->posts} WHERE post_type = %s )
				  LIMIT 200",        /* grab a bit more so we can discard dups later */
				$pt
			)
		);

		if ( $all_keys ) {
			// Build a set we can test membership against.
			$plain_set = array_flip(
				array_filter( $all_keys, static fn ( $k ) => $k[0] !== '_' )
			);

			$user_keys = [];
			foreach ( $all_keys as $key ) {
				if ( $key[0] === '_' ) {
					/* If "_price" exists *and* "price" already exists, skip the underscore twin. */
					$bare = ltrim( $key, '_' );
					if ( isset( $plain_set[ $bare ] ) ) {
						continue;
					}
				}
				$user_keys[] = $key;
			}

			/* Trim to 100 so the page stays lightweight. */
			$user_keys = array_slice( $user_keys, 0, 100 );

			echo '<p><strong>' . esc_html__( 'Custom fields (meta keys)', 'datasheets' ) . '</strong></p><pre>';
			echo esc_html( implode( "\n", $user_keys ) );
			echo '</pre>';
		}


		/*--------------------------------------------------------------*/
		/* ACF field definitions
		/*--------------------------------------------------------------*/
		if ( function_exists( 'acf_get_field_groups' ) ) {
			$groups = acf_get_field_groups( [ 'post_type' => $pt ] );

			if ( $groups ) {
				echo '<p><strong>' . esc_html__( 'ACF field groups', 'datasheets' ) . '</strong></p>';

				foreach ( $groups as $group ) {
					echo '<details style="margin-bottom:0.5rem;"><summary>';
					echo esc_html( $group['title'] );
					echo '</summary><pre style="margin-top:0.5rem;">';

					$fields = acf_get_fields( $group );
					if ( $fields ) {
						self::print_acf_fields_recursive( $fields );
					} else {
						esc_html_e( '*(No fields found – maybe the group is empty?)*', 'datasheets' );
					}

					echo '</pre></details>';
				}
			}
		}
	}

	/**
	 * Recursively prints ACF fields in “name (type)” format, handling sub-fields.
	 *
	 * @param array $fields The field array returned by acf_get_fields().
	 */
	private static function print_acf_fields_recursive( array $fields ) {
		foreach ( $fields as $field ) {
			// Skip internal ACF “clone” placeholder rows.
			if ( isset( $field['type'] ) && $field['type'] === 'clone' ) {
				continue;
			}

			printf(
				"%s (%s)\n",
				esc_html( $field['name'] ?? $field['key'] ),
				esc_html( $field['type'] ?? 'unknown' )
			);

			// Handle repeaters, groups, flex-content sub fields.
			if ( ! empty( $field['sub_fields'] ) && is_array( $field['sub_fields'] ) ) {
				self::print_acf_fields_recursive( $field['sub_fields'] );
			}
		}
	}

}

endif; // class guard

// Instantiate the page when in admin context.
if ( is_admin() ) {
	new Datasheets_Admin_Settings_Page();
};