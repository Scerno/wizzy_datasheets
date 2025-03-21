<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://scerno.com/author
 * @since             1.0.0
 * @package           Wizzy_Datasheets
 *
 * @wordpress-plugin
 * Plugin Name:       Wizzy Datasheets
 * Plugin URI:        https://scerno.com/plugin_url
 * Description:       Create beautiful PDF datasheets for WooCommerce Products using advanced templates and functional dependency to show and hide relevant items.
 * Version:           1.0.0
 * Author:            Scerno Ltd.
 * Author URI:        https://scerno.com/author/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wizzy-datasheets
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'WIZZY_DATASHEETS_VERSION', '1.0.0' );


// Define plugin paths for easy usage.
if ( ! defined( 'WIZZY_DATASHEETS_PLUGIN_DIR' ) ) {
    define( 'WIZZY_DATASHEETS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

// Define plugin paths for easy usage.
if ( ! defined( 'WIZZY_DATASHEETS_PLUGIN_URL' ) ) {
    define( 'WIZZY_DATASHEETS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wizzy-datasheets-activator.php
 */
function activate_wizzy_datasheets() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wizzy-datasheets-activator.php';
	Wizzy_Datasheets_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wizzy-datasheets-deactivator.php
 */
function deactivate_wizzy_datasheets() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wizzy-datasheets-deactivator.php';
	Wizzy_Datasheets_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_wizzy_datasheets' );
register_deactivation_hook( __FILE__, 'deactivate_wizzy_datasheets' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wizzy-datasheets.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_wizzy_datasheets() {

	$plugin = new Wizzy_Datasheets();
	$plugin->run();

}
run_wizzy_datasheets();



