<?php

/** 
 * required in the main includes file.
 * Defines custom post types and how they work
 */
 
 
class Wizzy_Datasheets_Custom_Post_Types {

    public function __construct() {
        add_action( 'init', array( $this, 'register_my_custom_post_types' ) );
    }

    public function register_my_custom_post_types() {
        $labels = array(
            'name'               => __( 'Page Designs (labs arr)', 'wizzy-datasheets' ),
            'singular_name'      => __( 'Page Design (sing labs arr)', 'wizzy-datasheets' ),
			'menu_name'           => __( 'Page Design', 'wizzy-datasheets' ),
			'parent_item_colon'   => __( 'Parent Page Design', 'wizzy-datasheets' ),
			'all_items'           => __( 'All Page Designs', 'wizzy-datasheets' ),
			'view_item'           => __( 'View Page Design', 'wizzy-datasheets' ),
			'add_new_item'        => __( 'Add New Page Design', 'wizzy-datasheets' ),
			'add_new'             => __( 'Add New', 'wizzy-datasheets' ),
			'edit_item'           => __( 'Edit Page Design', 'wizzy-datasheets' ),
			'update_item'         => __( 'Update Page Design', 'wizzy-datasheets' ),
			'search_items'        => __( 'Search Page Design', 'wizzy-datasheets' ),
			'not_found'           => __( 'Not Found', 'wizzy-datasheets' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'wizzy-datasheets' ),
        );

        $args = array(
            'label'              => __( 'Page Designs (args arr)', 'wizzy-datasheets' ),
			'description'        => __( 'Page Designs (desc args arr)', 'wizzy-datasheets' ),
			'labels'             => $labels,
            'public'             => true,
            'has_archive'        => true,
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
			'show_in_menu'        => 'wizzy-datasheets',
			'show_in_nav_menus'   => false,
			'show_in_admin_bar'   => true,
			'menu_position'       => 5,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => true,
			'publicly_queryable'  => true,
			'capability_type'     => 'post',
			// Enable block editor (Gutenberg):
			'show_in_rest' => true,
        );

        register_post_type( 'wizzy_pages', $args );
    }
}



