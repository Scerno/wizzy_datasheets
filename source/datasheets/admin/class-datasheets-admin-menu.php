<?php

// self instantiating settings page in the admin menu
require DATASHEETS_PLUGIN_DIR. 'admin/class-datasheets-admin-menu-page-settings.php';

//build rest of admin menu
class Datasheets_Admin_Menu {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
    }

    public function add_plugin_admin_menu() {
		// Create base64 encoded svg icon
		$b64svg = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzMjAgMjE1Ij4NCjxwb2x5Z29uIHBvaW50cz0iMCAyMTQuODY2IDAgLjEzNCAxNDQuNDk0IDE0NC42MjcgMTMyLjI2MyAxNTYuODU5IDE3LjI5NyA0MS44OTMgMTcuMjk3IDE3My4xMDYgNjcuMzk4IDEyMy4wMDUgNzkuNjI5IDEzNS4yMzYgMCAyMTQuODY2IiBzdHlsZT0iZmlsbDogIzdmN2Y3Zjsgc3Ryb2tlLXdpZHRoOiAwcHg7Ii8+DQo8cG9seWdvbiBwb2ludHM9IjMyMCAyMTQuODY2IDE3NS41MDYgNzAuMzcyIDE4Ny43MzcgNTguMTQxIDMwMi43MDMgMTczLjEwNiAzMDIuNzAzIDQxLjg5MyAyNTIuNjAyIDkxLjk5MyAyNDAuMzcxIDc5Ljc2MiAzMjAgLjEzNCAzMjAgMjE0Ljg2NiIgc3R5bGU9ImZpbGw6ICM3ZjdmN2Y7IHN0cm9rZS13aWR0aDogMHB4OyIvPg0KPHJlY3QgeD0iMTI5LjcyOSIgeT0iNDYuMzQ1IiB3aWR0aD0iMTcuMjk4IiBoZWlnaHQ9IjEyMi4zMSIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTM1LjQ4NCAxMjkuMzM0KSByb3RhdGUoLTQ1KSIgc3R5bGU9ImZpbGw6ICNlNDJmN2E7IHN0cm9rZS13aWR0aDogMHB4OyIvPg0KPHJlY3QgeD0iMTcyLjk3MyIgeT0iNDYuMzQ1IiB3aWR0aD0iMTcuMjk4IiBoZWlnaHQ9IjEyMi4zMSIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTIyLjgxOCAxNTkuOTEyKSByb3RhdGUoLTQ1KSIgc3R5bGU9ImZpbGw6ICNlNDJmN2E7IHN0cm9rZS13aWR0aDogMHB4OyIvPg0KPC9zdmc+';
	
		
        // Add your admin page with add_menu_page or add_submenu_page, etc.
        add_menu_page( __( 'Datasheets', 'datasheets' ), __( 'Datasheets', 'datasheets' ), 'manage_options', 'datasheets', array( $this, 'render_admin_menu_page' ), $b64svg, 55.5 );
		//add_submenu_page( 'datasheets', 'Add Datasheet', 'All Ds', 'manage_options', 'post-new.php?post_type=datasheet', '', 1);
		//add_submenu_page( 'datasheets', 'Add Templates', 'All Ts', 'manage_options', 'edit.php?post_type=datasheet_template', '', 3);
		//add_submenu_page( 'edit.php?post_type=datasheet_template', 'Testing', 'Test', 'manage_options', 'test_slug', '', 7);
		//add_submenu_page( 'datasheets', __( 'Settings', 'datasheets' ), __( 'Settings', 'datasheets' ), 'manage_options', 'datasheet_settings', [ $this, 'datasheet_admin_menu_settings' ], 5);
		
		
		// Add custom "Add New" submenus - on admin_menu hook is safer then direct manual call
		add_action( 'admin_menu', function () {
			add_submenu_page(
				'datasheets', // parent slug
				__( 'Add Layout Title', 'datasheets' ), // page title
				__( 'Add Layout', 'datasheets' ), // menu title
				'manage_options',
				'post-new.php?post_type=datasheet_layout',
				false, // no callback needed
				1 // position – just after “All Items”
			);
			
			add_submenu_page(
				'datasheets', // parent slug
				__( 'Add Datasheet', 'datasheets' ), // page title
				__( 'Add Datasheet', 'datasheets' ), // menu title
				'manage_options',
				'post-new.php?post_type=datasheet',
				false, // no callback needed
				3 // position – just after “All Items”
			);
		}, 20 );
		
    }

    public function render_admin_menu_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }
		echo '<div class="wrap"><h1>' .
			esc_html__( 'Datasheets Settings', 'datasheets' ) .
			'</h1></div>';
		// settings form coming soon…
    }
	
	
	
	/*
	public function datasheet_admin_menu_settings() {
		// ensure the user has permissions before going further
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		};

		require DATASHEETS_PLUGIN_DIR. 'admin/class-datasheets-admin-menu-page-settings.php';
		
	}*/
}






