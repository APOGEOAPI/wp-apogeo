<?php
/**
 * Admin settings page for ApogeoAPI.
 *
 * @package ApogeoAPI
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ApogeoAPI_Admin {

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	public function register_menu() {
		add_options_page(
			__( 'ApogeoAPI Settings', 'apogeoapi' ),
			'ApogeoAPI',
			'manage_options',
			'apogeoapi-settings',
			array( $this, 'render_page' )
		);
	}

	public function register_settings() {
		register_setting(
			'apogeoapi_settings',
			'apogeoapi_api_key',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);
		register_setting(
			'apogeoapi_settings',
			'apogeoapi_cache_ttl',
			array(
				'type'              => 'integer',
				'sanitize_callback' => 'absint',
				'default'           => 14400,
			)
		);

		add_settings_section(
			'apogeoapi_main',
			__( 'API Configuration', 'apogeoapi' ),
			function () {
				echo '<p>' . esc_html__( 'Get a free API key at apogeoapi.com (1,000 requests / month, no credit card).', 'apogeoapi' ) . '</p>';
			},
			'apogeoapi-settings'
		);

		add_settings_field(
			'apogeoapi_api_key',
			__( 'API Key', 'apogeoapi' ),
			function () {
				$value = esc_attr( get_option( 'apogeoapi_api_key', '' ) );
				echo '<input type="password" name="apogeoapi_api_key" value="' . $value . '" class="regular-text" autocomplete="off" />';
				echo '<p class="description">' . esc_html__( 'Your ApogeoAPI key. Stored encrypted via wp_options.', 'apogeoapi' ) . '</p>';
			},
			'apogeoapi-settings',
			'apogeoapi_main'
		);

		add_settings_field(
			'apogeoapi_cache_ttl',
			__( 'Cache TTL (seconds)', 'apogeoapi' ),
			function () {
				$value = (int) get_option( 'apogeoapi_cache_ttl', 14400 );
				echo '<input type="number" name="apogeoapi_cache_ttl" value="' . $value . '" min="60" max="86400" class="small-text" />';
				echo '<p class="description">' . esc_html__( 'How long to cache API responses on this server. Default 14400 (4 hours) — matches the ApogeoAPI exchange-rate refresh cadence.', 'apogeoapi' ) . '</p>';
			},
			'apogeoapi-settings',
			'apogeoapi_main'
		);
	}

	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'ApogeoAPI Settings', 'apogeoapi' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'apogeoapi_settings' );
				do_settings_sections( 'apogeoapi-settings' );
				submit_button();
				?>
			</form>

			<hr />
			<h2><?php echo esc_html__( 'Available shortcodes', 'apogeoapi' ); ?></h2>
			<table class="widefat striped">
				<thead><tr><th>Shortcode</th><th>What it does</th></tr></thead>
				<tbody>
					<tr>
						<td><code>[apogeo_country_selector]</code></td>
						<td><?php echo esc_html__( 'Renders a <select> with all 250+ countries (name + flag emoji).', 'apogeoapi' ); ?></td>
					</tr>
					<tr>
						<td><code>[apogeo_country iso="AR"]</code></td>
						<td><?php echo esc_html__( 'Inline country data block (capital, currency, flag, population).', 'apogeoapi' ); ?></td>
					</tr>
					<tr>
						<td><code>[apogeo_exchange_rate currency="EUR"]</code></td>
						<td><?php echo esc_html__( 'Live USD exchange rate, updated every 4 hours.', 'apogeoapi' ); ?></td>
					</tr>
					<tr>
						<td><code>[apogeo_visitor_country]</code></td>
						<td><?php echo esc_html__( 'Detects the visitor country from IP and shows the country name + flag.', 'apogeoapi' ); ?></td>
					</tr>
				</tbody>
			</table>

			<p>
				<a href="https://apogeoapi.com" target="_blank" rel="noopener"><?php echo esc_html__( 'apogeoapi.com', 'apogeoapi' ); ?></a> &middot;
				<a href="https://api.apogeoapi.com/api/docs" target="_blank" rel="noopener"><?php echo esc_html__( 'API docs', 'apogeoapi' ); ?></a> &middot;
				<a href="mailto:support@apogeoapi.com"><?php echo esc_html__( 'Support', 'apogeoapi' ); ?></a>
			</p>
		</div>
		<?php
	}
}
