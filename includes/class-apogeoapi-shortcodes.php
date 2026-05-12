<?php
/**
 * Shortcodes registered by the plugin.
 *
 * @package ApogeoAPI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ApogeoAPI_Shortcodes {

	public function __construct() {
		add_shortcode( 'apogeo_country_selector', array( $this, 'country_selector' ) );
		add_shortcode( 'apogeo_country', array( $this, 'country_block' ) );
		add_shortcode( 'apogeo_exchange_rate', array( $this, 'exchange_rate' ) );
		add_shortcode( 'apogeo_visitor_country', array( $this, 'visitor_country' ) );
	}

	/**
	 * [apogeo_country_selector name="country" id="country-select"]
	 *
	 * Renders a <select> with all 250+ countries.
	 */
	public function country_selector( $atts ) {
		$atts = shortcode_atts(
			array(
				'name'           => 'country',
				'id'             => 'apogeo-country-selector',
				'default'        => '',
				'class'          => '',
				'include_flag'   => 'true',
			),
			$atts,
			'apogeo_country_selector'
		);

		$countries = ApogeoAPI_Client::list_countries();
		if ( is_wp_error( $countries ) ) {
			return $this->error_html( $countries );
		}

		$rows = '';
		foreach ( $countries as $c ) {
			$iso  = isset( $c['iso2'] ) ? esc_attr( $c['iso2'] ) : '';
			$name = isset( $c['name'] ) ? esc_html( $c['name'] ) : '';
			$flag = ( 'true' === $atts['include_flag'] && isset( $c['flagEmoji'] ) ) ? $c['flagEmoji'] . ' ' : '';
			$sel  = ( $atts['default'] === $iso ) ? ' selected' : '';
			$rows .= '<option value="' . $iso . '"' . $sel . '>' . $flag . $name . '</option>';
		}

		return sprintf(
			'<select name="%1$s" id="%2$s" class="%3$s">%4$s</select>',
			esc_attr( $atts['name'] ),
			esc_attr( $atts['id'] ),
			esc_attr( $atts['class'] ),
			$rows
		);
	}

	/**
	 * [apogeo_country iso="AR"]
	 *
	 * Inline data block for a country.
	 */
	public function country_block( $atts ) {
		$atts = shortcode_atts(
			array(
				'iso' => '',
			),
			$atts,
			'apogeo_country'
		);

		if ( empty( $atts['iso'] ) ) {
			return '<em>' . esc_html__( 'apogeo_country: missing iso="XX" attribute', 'apogeoapi' ) . '</em>';
		}

		$country = ApogeoAPI_Client::get_country( $atts['iso'] );
		if ( is_wp_error( $country ) ) {
			return $this->error_html( $country );
		}

		ob_start();
		?>
		<div class="apogeo-country-block">
			<?php if ( ! empty( $country['flagUrl'] ) ) : ?>
				<img src="<?php echo esc_url( $country['flagUrl'] ); ?>" alt="<?php echo esc_attr( $country['name'] ); ?>" style="width:48px;vertical-align:middle;" />
			<?php endif; ?>
			<strong><?php echo esc_html( $country['name'] ); ?></strong>
			<ul style="margin:0.5em 0;">
				<?php if ( ! empty( $country['capital'] ) ) : ?>
					<li><?php esc_html_e( 'Capital:', 'apogeoapi' ); ?> <?php echo esc_html( $country['capital'] ); ?></li>
				<?php endif; ?>
				<?php if ( ! empty( $country['region'] ) ) : ?>
					<li><?php esc_html_e( 'Region:', 'apogeoapi' ); ?> <?php echo esc_html( $country['region'] ); ?></li>
				<?php endif; ?>
				<?php if ( ! empty( $country['currency'] ) ) : ?>
					<li><?php esc_html_e( 'Currency:', 'apogeoapi' ); ?> <?php echo esc_html( $country['currency'] ); ?>
						<?php if ( ! empty( $country['currencyRate'] ) ) : ?>
							(<?php echo esc_html( number_format( (float) $country['currencyRate'], 4 ) ); ?> / USD)
						<?php endif; ?>
					</li>
				<?php endif; ?>
				<?php if ( ! empty( $country['population'] ) ) : ?>
					<li><?php esc_html_e( 'Population:', 'apogeoapi' ); ?> <?php echo esc_html( number_format( (int) $country['population'] ) ); ?></li>
				<?php endif; ?>
			</ul>
		</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * [apogeo_exchange_rate currency="EUR"]
	 *
	 * Renders a "1 USD = X EUR" line, plus last-updated timestamp.
	 */
	public function exchange_rate( $atts ) {
		$atts = shortcode_atts(
			array(
				'currency' => '',
				'format'   => '1 USD = {rate} {currency}',
			),
			$atts,
			'apogeo_exchange_rate'
		);

		if ( empty( $atts['currency'] ) ) {
			return '<em>' . esc_html__( 'apogeo_exchange_rate: missing currency="XXX" attribute', 'apogeoapi' ) . '</em>';
		}

		$rate = ApogeoAPI_Client::get_exchange_rate( $atts['currency'] );
		if ( is_wp_error( $rate ) ) {
			return $this->error_html( $rate );
		}

		$value = isset( $rate['usdRate'] ) ? number_format( (float) $rate['usdRate'], 4 ) : '?';
		$line  = str_replace(
			array( '{rate}', '{currency}' ),
			array( $value, strtoupper( $atts['currency'] ) ),
			$atts['format']
		);

		$ts = isset( $rate['lastUpdated'] ) ? sprintf( __( ' (updated %s)', 'apogeoapi' ), esc_html( $rate['lastUpdated'] ) ) : '';

		return '<span class="apogeo-fx-rate">' . esc_html( $line ) . '<small>' . $ts . '</small></span>';
	}

	/**
	 * [apogeo_visitor_country]
	 *
	 * Detects visitor country via IP, shows flag + name.
	 * Uses REMOTE_ADDR — behind a reverse proxy you may need X-Forwarded-For.
	 */
	public function visitor_country( $atts ) {
		$atts = shortcode_atts(
			array(
				'fallback' => 'your country',
				'show_flag' => 'true',
			),
			$atts,
			'apogeo_visitor_country'
		);

		$ip = $this->get_visitor_ip();
		if ( empty( $ip ) ) {
			return esc_html( $atts['fallback'] );
		}

		$geo = ApogeoAPI_Client::geolocate_ip( $ip );
		if ( is_wp_error( $geo ) || empty( $geo['country'] ) ) {
			return esc_html( $atts['fallback'] );
		}

		$flag = ( 'true' === $atts['show_flag'] && ! empty( $geo['country']['flagEmoji'] ) ) ? $geo['country']['flagEmoji'] . ' ' : '';
		$name = isset( $geo['country']['name'] ) ? $geo['country']['name'] : $atts['fallback'];

		return '<span class="apogeo-visitor-country">' . esc_html( $flag . $name ) . '</span>';
	}

	private function get_visitor_ip() {
		$keys = array( 'HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR' );
		foreach ( $keys as $k ) {
			if ( ! empty( $_SERVER[ $k ] ) ) {
				$ip = trim( explode( ',', wp_unslash( $_SERVER[ $k ] ) )[0] );
				if ( filter_var( $ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE ) ) {
					return $ip;
				}
			}
		}
		return '';
	}

	private function error_html( WP_Error $err ) {
		if ( current_user_can( 'manage_options' ) ) {
			return '<em>ApogeoAPI error: ' . esc_html( $err->get_error_message() ) . '</em>';
		}
		// Don't leak error details to public visitors.
		return '';
	}
}
