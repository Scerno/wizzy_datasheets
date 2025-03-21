<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://scerno.com/author
 * @since      1.0.0
 *
 * @package    Wizzy_datasheets
 * @subpackage Wizzy_datasheets/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wizzy_datasheets
 * @subpackage Wizzy_datasheets/includes
 * @author     Scerno Ltd. <info@scerno.com>
 */
class Wizzy_datasheets_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wizzy_datasheets',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
