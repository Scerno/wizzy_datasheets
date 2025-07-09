<?php

/** 
 * required in the main includes file.
 * Defines custom post types and how they work
 */
 
 
class Datasheets_Custom_Post_Types {

    public function __construct() {
        add_action( 'init', array( $this, 'register_my_custom_post_types' ) );
    }

    public function register_my_custom_post_types() {
		
		// Create CPT for page layouts - like A4, Letter etc
        $layout_labels = array(
            'name'                => __( 'Datasheet layouts', 'datasheets' ),
            'singular_name'       => __( 'Datasheet layout', 'datasheets' ),
			'menu_name'           => __( 'Datasheet layouts', 'datasheets' ),
			'parent_item_colon'   => __( 'Parent Datasheet layout', 'datasheets' ),
			'all_items'           => __( 'All layouts', 'datasheets' ),
			'view_item'           => __( 'View Datasheet layout', 'datasheets' ),
			'add_new_item'        => __( 'Add New Datasheet layout', 'datasheets' ),
			'add_new'             => __( 'Add New', 'datasheets' ),
			'edit_item'           => __( 'Edit Datasheet layout', 'datasheets' ),
			'update_item'         => __( 'Update Datasheet layout', 'datasheets' ),
			'search_items'        => __( 'Search Datasheet layout', 'datasheets' ),
			'not_found'           => __( 'Not Found', 'datasheets' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'datasheets' ),
        );

        $layout_args = array(
            'label'              => __( 'Datasheet Layouts', 'datasheets' ),
			'description'        => __( 'Reusable layouts for Datasheet PDFs.', 'datasheets' ),
			'labels'             => $layout_labels,

            // Features this CPT supports in Post Editor
			'supports'            => array( 'title', 'revisions'),
			// You can associate this CPT with a taxonomy or custom taxonomy. 
			'taxonomies'          => array( 'genres' ),
			/* A hierarchical CPT is like Pages and can have
			* Parent and child items. A non-hierarchical CPT
			* is like Posts.
			*/
			'hierarchical'        => false,
			'public'              => false,
			'show_ui'             => true,
			'show_in_menu'        => 'datasheets',
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'can_export'          => true,
			'has_archive'         => false,
			'exclude_from_search' => true,
			'publicly_queryable'  => false,
			'capability_type'     => 'post',
			'show_in_rest'        => true, // theoretically show gutenberg but we turn this off with a filter later.  Needed to query by JS
        );

        register_post_type( 'datasheet_layout', $layout_args );
		
		
		
		
		
		// Create CPT for datasheets
		$datasheet_labels = array(
            'name'               => __( 'Datasheets', 'datasheets' ),
            'singular_name'      => __( 'Datasheet', 'datasheets' ),
			'menu_name'           => __( 'Datasheet', 'datasheets' ),
			'parent_item_colon'   => __( 'Parent Datasheet', 'datasheets' ),
			'all_items'           => __( 'All Datasheets', 'datasheets' ),
			'view_item'           => __( 'View Datasheet', 'datasheets' ),
			'add_new_item'        => __( 'Add New Datasheet', 'datasheets' ),
			'add_new'             => __( 'Add New', 'datasheets' ),
			'edit_item'           => __( 'Edit Datasheet', 'datasheets' ),
			'update_item'         => __( 'Update Datasheet', 'datasheets' ),
			'search_items'        => __( 'Search Datasheet', 'datasheets' ),
			'not_found'           => __( 'Not Found', 'datasheets' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'datasheets' ),
        );

        $datasheet_args = array(
            'label'              => __( 'Datasheet', 'datasheets' ),
			'description'        => __( 'Datasheets', 'datasheets' ),
			'labels'             => $datasheet_labels,
            // Features this CPT supports in Post Editor
			'supports'            => array( 'title', 'revisions', 'editor' ),
			// You can associate this CPT with a taxonomy or custom taxonomy. 
			'taxonomies'          => array( 'genres' ),
			/* A hierarchical CPT is like Pages and can have
			* Parent and child items. A non-hierarchical CPT
			* is like Posts.
			*/
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => 'datasheets',
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			// Enable block editor (Gutenberg):
			'show_in_rest' => true,
        );

        register_post_type( 'datasheet', $datasheet_args );
		
		
		
		
		
    }
}




// Ensure Datasheet Layouts cpt doesn't use block editor
add_filter( 'use_block_editor_for_post_type', function ( $use_block, $post_type ) {
	if ( 'datasheet_layout' === $post_type ) {
		return false;         // fall back to Classic
	}
	return $use_block;
}, 10, 2 );


// Register the single-row meta and make it return as JSON for the Datasheets block

add_action( 'init', function () {
    register_meta(
        'post',
        '_ds_layout_settings',
        [
            'single'       => true,
            'type'         => 'object',
            'show_in_rest' => true,              // makes it appear under meta.*
            'auth_callback'=> function() {
                return current_user_can( 'edit_posts' );
            },
        ]
    );
} );


// set up the datasheet_layout post fields
class DS_Layout_Meta_Box {

    const META_KEY = '_ds_layout_settings';

    public function __construct() {
        add_action( 'add_meta_boxes', [ $this, 'register_box' ] );
        add_action( 'save_post_datasheet_layout', [ $this, 'save' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_js' ] );
    }

    /** Register the box on the edit screen */
    public function register_box() {
        add_meta_box(
            'ds_layout_meta',
            __( 'PDF Layout', 'datasheets' ),
            [ $this, 'render' ],
            'datasheet_layout',
            'normal',
            'high'
        );
    }

    /** Draw the form */
    public function render( WP_Post $post ) {
        wp_nonce_field( 'ds_layout_meta_nonce', 'ds_layout_meta_nonce' );

        $defaults = [
            'width'        => '',
            'width_unit'   => 'mm',
            'height'       => '',
            'height_unit'  => 'mm',
            'margin_equal' => 1,
            'margin'       => '',       // used when equal
            'margin_top'   => '',
            'margin_right' => '',
            'margin_bottom'=> '',
            'margin_left'  => '',
            'description'  => '',
        ];
        $settings = wp_parse_args( get_post_meta( $post->ID, self::META_KEY, true ), $defaults );

        $units = [ 'mm', 'cm', 'in', 'px', 'pt', '%' ];
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label for="ds_width"><?php _e( 'Page width', 'datasheets' ); ?></label></th>
                <td>
                    <input type="number" id="ds_width" name="ds_settings[width]" value="<?php echo esc_attr( $settings['width'] ); ?>" step="0.01" min="0">
                    <?php $this->unit_select( 'width_unit', $settings['width_unit'], $units ); ?>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="ds_height"><?php _e( 'Page height', 'datasheets' ); ?></label></th>
                <td>
                    <input type="number" id="ds_height" name="ds_settings[height]" value="<?php echo esc_attr( $settings['height'] ); ?>" step="0.01" min="0">
                    <?php $this->unit_select( 'height_unit', $settings['height_unit'], $units ); ?>
                </td>
            </tr>
            <tr>
				<th scope="row"><?php _e( 'Page margins', 'datasheets' ); ?></th>
				<td>
					<label>
						<input type="checkbox"
							   id="ds_equal"
							   name="ds_settings[margin_equal]"
							   value="1"
							   <?php checked( $settings['margin_equal'] ); ?>>
						<?php _e( 'Equal margins', 'datasheets' ); ?>
					</label>

					<!--  ONE unit dropdown, always visible  -->
					<?php $this->unit_select(
							'margin_unit',
							isset( $settings['margin_unit'] )
								? $settings['margin_unit']
								: $settings['width_unit'],
							$units
					); ?>

					<br>

					<!-- Single-value input (shown when “equal” is ticked) -->
					<div id="ds_margin_equal_wrap">
						<input type="number"
							   name="ds_settings[margin]"
							   value="<?php echo esc_attr( $settings['margin'] ); ?>"
							   step="0.01" min="0">
					</div>

					<!-- Four-side inputs (shown when “equal” is unticked) -->
					<div id="ds_margin_quad_wrap" style="display:none">
						<?php foreach ( [ 'top', 'right', 'bottom', 'left' ] as $side ) : ?>
							<label style="display:block;margin-top:5px">
								<?php echo ucfirst( $side ); ?>
								<input type="number"
									   name="ds_settings[margin_<?php echo $side; ?>]"
									   value="<?php echo esc_attr( $settings[ 'margin_' . $side ] ); ?>"
									   step="0.01" min="0" style="width:90px">
							</label>
						<?php endforeach; ?>
					</div>
				</td>
			</tr>

            <tr>
                <th scope="row"><label for="ds_desc"><?php _e( 'Description', 'datasheets' ); ?></label></th>
                <td>
                    <textarea id="ds_desc" name="ds_settings[description]" rows="4" class="large-text"><?php echo esc_textarea( $settings['description'] ); ?></textarea>
                </td>
            </tr>
        </table>
        <?php
    }

    /** Helper: <select> for units */
    private function unit_select( $key, $current, $units ) {
        echo '<select name="ds_settings[' . esc_attr( $key ) . ']">';
        foreach ( $units as $u ) {
            printf( '<option value="%1$s" %2$s>%1$s</option>',
                esc_attr( $u ),
                selected( $current, $u, false )
            );
        }
        echo '</select>';
    }

    /** Save handler */
    public function save( $post_id ) {
        // Basic checks
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
        if ( ! isset( $_POST['ds_layout_meta_nonce'] ) ||
             ! wp_verify_nonce( $_POST['ds_layout_meta_nonce'], 'ds_layout_meta_nonce' ) ) return;
        if ( ! current_user_can( 'edit_post', $post_id ) ) return;
		
		/*
        if ( isset( $_POST['ds_settings'] ) && is_array( $_POST['ds_settings'] ) ) {
            // Clean up integers / floats; leave strings untouched for now
            $clean = array_map( 'sanitize_text_field', $_POST['ds_settings'] );
            update_post_meta( $post_id, self::META_KEY, $clean );
        }
		*/
		
		if ( ! empty( $_POST['ds_settings'] ) && is_array( $_POST['ds_settings'] ) ) {
			$in         = wp_unslash( $_POST['ds_settings'] );
			$units      = [ 'mm', 'cm', 'in', 'px', 'pt', '%' ];   // whitelist
			$clean      = [];

			// cast numeric values
			foreach ( [ 'width', 'height', 'margin', 'margin_top', 'margin_right', 'margin_bottom', 'margin_left' ] as $num ) {
				if ( isset( $in[ $num ] ) && $in[ $num ] !== '' ) {
					$clean[ $num ] = floatval( $in[ $num ] );
				}
			}

			// booleans & strings
			$clean['margin_equal'] = empty( $in['margin_equal'] ) ? 0 : 1;
			$clean['description']  = isset( $in['description'] ) ? sanitize_textarea_field( $in['description'] ) : '';

			// units – keep only allowed values
			foreach ( [ 'width_unit', 'height_unit', 'margin_unit' ] as $u ) {
				if ( isset( $in[ $u ] ) && in_array( $in[ $u ], $units, true ) ) {
					$clean[ $u ] = $in[ $u ];
				}
			}

			update_post_meta( $post_id, self::META_KEY, $clean );
		}
	}
	
    /** Enqueue simple toggle script */
    public function enqueue_js() {
        global $typenow;
        if ( 'datasheet_layout' !== $typenow ) return;

        wp_add_inline_script( 'jquery-core', "
            jQuery(document).ready(function($){
                function toggleMargins() {
                    if ( $('#ds_equal').is(':checked') ) {
                        $('#ds_margin_quad_wrap').hide();
                        $('#ds_margin_equal_wrap').show();
                    } else {
                        $('#ds_margin_quad_wrap').show();
                        $('#ds_margin_equal_wrap').hide();
                    }
                }
                toggleMargins();
                $('#ds_equal').on('change', toggleMargins);
            });
        " );
    }
}

new DS_Layout_Meta_Box();
