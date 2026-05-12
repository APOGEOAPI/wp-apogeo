<?php
/**
 * Fired when the plugin is uninstalled (deleted) from the WP admin.
 * Wipes all plugin options and transients. Activation/deactivation
 * does not trigger this — only "Delete" from Plugins screen.
 *
 * @package ApogeoAPI
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'apogeoapi_api_key' );
delete_option( 'apogeoapi_cache_ttl' );

global $wpdb;
$wpdb->query(
	"DELETE FROM {$wpdb->options} WHERE option_name LIKE '_transient_apogeoapi_%' OR option_name LIKE '_transient_timeout_apogeoapi_%'"
);
