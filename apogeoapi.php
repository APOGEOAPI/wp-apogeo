<?php
/**
 * Plugin Name:       ApogeoAPI — Country Selector & Geo Data
 * Plugin URI:        https://apogeoapi.com
 * Description:       Add a country selector, IP-based geolocation, and live exchange rate widgets to any WordPress page or post via shortcodes. Powered by ApogeoAPI.
 * Version:           0.1.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            ApogeoAPI
 * Author URI:        https://apogeoapi.com
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       apogeoapi
 *
 * @package ApogeoAPI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Direct access blocked.
}

define( 'APOGEOAPI_VERSION', '0.1.0' );
define( 'APOGEOAPI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'APOGEOAPI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'APOGEOAPI_API_BASE', 'https://api.apogeoapi.com/v1' );

require_once APOGEOAPI_PLUGIN_DIR . 'includes/class-apogeoapi-client.php';
require_once APOGEOAPI_PLUGIN_DIR . 'includes/class-apogeoapi-admin.php';
require_once APOGEOAPI_PLUGIN_DIR . 'includes/class-apogeoapi-shortcodes.php';

/**
 * Plugin bootstrap.
 */
function apogeoapi_init() {
	// Load text domain for translations.
	load_plugin_textdomain( 'apogeoapi', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	// Wire up admin settings page.
	if ( is_admin() ) {
		new ApogeoAPI_Admin();
	}

	// Register shortcodes (all environments).
	new ApogeoAPI_Shortcodes();
}
add_action( 'plugins_loaded', 'apogeoapi_init' );

/**
 * Activation hook — set sane defaults.
 */
register_activation_hook(
	__FILE__,
	function () {
		add_option( 'apogeoapi_api_key', '' );
		add_option( 'apogeoapi_cache_ttl', 14400 ); // 4h matches our backend FX cache cadence.
	}
);

/**
 * Deactivation hook — clear transients but keep settings.
 */
register_deactivation_hook(
	__FILE__,
	function () {
		// Walk through wp_options for our transient keys and delete them.
		global $wpdb;
		$wpdb->query(
			"DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_apogeoapi_%' OR option_name LIKE '_transient_timeout_apogeoapi_%'"
		);
	}
);

/**
 * Uninstall hook — wipe settings entirely.
 * (Lives in uninstall.php in the same dir per WP convention; here as fallback.)
 */
register_uninstall_hook(
	__FILE__,
	function () {
		delete_option( 'apogeoapi_api_key' );
		delete_option( 'apogeoapi_cache_ttl' );
	}
);
