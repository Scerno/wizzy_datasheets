<?php
class Wizzy_Datasheets_Admin_Menu {
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );
    }

    public function add_plugin_admin_menu() {
		// Create base64 encoded svg icon
		$b64svg = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAzMjAgMjE1Ij4NCjxwb2x5Z29uIHBvaW50cz0iMCAyMTQuODY2IDAgLjEzNCAxNDQuNDk0IDE0NC42MjcgMTMyLjI2MyAxNTYuODU5IDE3LjI5NyA0MS44OTMgMTcuMjk3IDE3My4xMDYgNjcuMzk4IDEyMy4wMDUgNzkuNjI5IDEzNS4yMzYgMCAyMTQuODY2IiBzdHlsZT0iZmlsbDogIzdmN2Y3Zjsgc3Ryb2tlLXdpZHRoOiAwcHg7Ii8+DQo8cG9seWdvbiBwb2ludHM9IjMyMCAyMTQuODY2IDE3NS41MDYgNzAuMzcyIDE4Ny43MzcgNTguMTQxIDMwMi43MDMgMTczLjEwNiAzMDIuNzAzIDQxLjg5MyAyNTIuNjAyIDkxLjk5MyAyNDAuMzcxIDc5Ljc2MiAzMjAgLjEzNCAzMjAgMjE0Ljg2NiIgc3R5bGU9ImZpbGw6ICM3ZjdmN2Y7IHN0cm9rZS13aWR0aDogMHB4OyIvPg0KPHJlY3QgeD0iMTI5LjcyOSIgeT0iNDYuMzQ1IiB3aWR0aD0iMTcuMjk4IiBoZWlnaHQ9IjEyMi4zMSIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTM1LjQ4NCAxMjkuMzM0KSByb3RhdGUoLTQ1KSIgc3R5bGU9ImZpbGw6ICNlNDJmN2E7IHN0cm9rZS13aWR0aDogMHB4OyIvPg0KPHJlY3QgeD0iMTcyLjk3MyIgeT0iNDYuMzQ1IiB3aWR0aD0iMTcuMjk4IiBoZWlnaHQ9IjEyMi4zMSIgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoLTIyLjgxOCAxNTkuOTEyKSByb3RhdGUoLTQ1KSIgc3R5bGU9ImZpbGw6ICNlNDJmN2E7IHN0cm9rZS13aWR0aDogMHB4OyIvPg0KPC9zdmc+';
	
		
        // Add your admin page with add_menu_page or add_submenu_page, etc.
        add_menu_page( __( 'Wizzy Settings', 'wizzy-datasheets' ), __( 'Wizzy', 'wizzy-datasheets' ), 'manage_options', 'wizzy-datasheets', array( $this, 'render_admin_menu_page' ), $b64svg, 55.5 );
    }

    public function render_admin_menu_page() {
        // Display your admin page content here.
    }
}





