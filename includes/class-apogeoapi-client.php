<?php
/**
 * ApogeoAPI HTTP client wrapper.
 *
 * @package ApogeoAPI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ApogeoAPI_Client {

	/**
	 * GET wrapper that uses WP transients to cache responses.
	 *
	 * @param string $path     Relative API path, e.g. '/countries' or '/countries/AR'.
	 * @param array  $args     Optional query string args.
	 * @param int    $cache_ttl Seconds to cache the response. Defaults to user setting.
	 * @return array|WP_Error Decoded JSON response or WP_Error on failure.
	 */
	public static function get( $path, $args = array(), $cache_ttl = null ) {
		$api_key = get_option( 'apogeoapi_api_key', '' );
		if ( empty( $api_key ) ) {
			return new WP_Error( 'apogeoapi_no_key', __( 'No ApogeoAPI key configured. Set one under Settings → ApogeoAPI.', 'apogeoapi' ) );
		}

		if ( null === $cache_ttl ) {
			$cache_ttl = (int) get_option( 'apogeoapi_cache_ttl', 14400 );
		}

		$url           = APOGEOAPI_API_BASE . $path;
		$query         = ! empty( $args ) ? '?' . http_build_query( $args ) : '';
		$transient_key = 'apogeoapi_' . md5( $url . $query );
		$cached        = get_transient( $transient_key );

		if ( false !== $cached ) {
			return $cached;
		}

		$response = wp_remote_get(
			$url . $query,
			array(
				'timeout' => 10,
				'headers' => array(
					'X-API-Key'    => $api_key,
					'User-Agent'   => 'apogeoapi-wp/' . APOGEOAPI_VERSION,
					'Accept'       => 'application/json',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );

		if ( $code < 200 || $code >= 300 ) {
			return new WP_Error(
				'apogeoapi_http_' . $code,
				sprintf( __( 'ApogeoAPI request failed with HTTP %d', 'apogeoapi' ), $code ),
				$body
			);
		}

		$data = json_decode( $body, true );
		if ( null === $data ) {
			return new WP_Error( 'apogeoapi_invalid_json', __( 'ApogeoAPI returned invalid JSON', 'apogeoapi' ) );
		}

		set_transient( $transient_key, $data, $cache_ttl );
		return $data;
	}

	/**
	 * Convenience: GET /countries (full list).
	 */
	public static function list_countries() {
		return self::get( '/countries' );
	}

	/**
	 * Convenience: GET /countries/{iso2}.
	 */
	public static function get_country( $iso2 ) {
		$iso2 = strtoupper( sanitize_text_field( $iso2 ) );
		return self::get( '/countries/' . $iso2 );
	}

	/**
	 * Convenience: GET /exchange-rates/{currency}.
	 */
	public static function get_exchange_rate( $currency ) {
		$currency = strtoupper( sanitize_text_field( $currency ) );
		return self::get( '/exchange-rates/' . $currency );
	}

	/**
	 * Convenience: GET /ip/{ip}.
	 */
	public static function geolocate_ip( $ip ) {
		$ip = filter_var( $ip, FILTER_VALIDATE_IP );
		if ( false === $ip ) {
			return new WP_Error( 'apogeoapi_invalid_ip', __( 'Invalid IP address', 'apogeoapi' ) );
		}
		return self::get( '/ip/' . $ip );
	}
}
