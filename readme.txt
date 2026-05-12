=== ApogeoAPI — Country Selector & Geo Data ===
Contributors: apogeoapi
Tags: api, countries, currency, exchange-rate, geolocation, ip, country-selector, world cities
Requires at least: 6.0
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 0.1.0
License: MIT
License URI: https://opensource.org/licenses/MIT

Add country selectors, IP-based geolocation, and live exchange rate widgets to any page or post via shortcodes.

== Description ==

ApogeoAPI brings 250+ countries, 5,000 states, 150,000 cities, 161 live currency exchange rates, and IP geolocation to your WordPress site through simple shortcodes.

Use cases:

* Country dropdown in your contact / signup / checkout form
* Show prices in the visitor's local currency at the live USD rate
* Detect visitor country and personalize content
* Inline country fact blocks in posts

= Shortcodes =

* `[apogeo_country_selector]` — `<select>` with all 250+ countries (flag + name).
* `[apogeo_country iso="AR"]` — inline country block (capital, currency, flag, population).
* `[apogeo_exchange_rate currency="EUR"]` — live USD/EUR rate.
* `[apogeo_visitor_country]` — detects visitor country from IP, shows flag + name.

= Setup =

1. Install and activate the plugin.
2. Get a free API key at [apogeoapi.com](https://apogeoapi.com) (1,000 requests / month, no credit card).
3. Go to **Settings → ApogeoAPI** and paste your key.
4. Use any shortcode in a post or page.

= Caching =

Responses are cached server-side via WP transients. Default TTL: 4 hours (matches the ApogeoAPI exchange-rate refresh cadence). Configurable in Settings.

= Free tier =

1,000 requests per month forever. Plus a 14-day full trial of paid features (cities, IP geo, exchange rates) on signup.

== Installation ==

1. Download the plugin zip from [github.com/APOGEOAPI/wp-apogeo](https://github.com/APOGEOAPI/wp-apogeo) (or install from the WordPress plugin directory once approved).
2. Upload to `/wp-content/plugins/` or install via Plugins → Add New → Upload.
3. Activate.
4. Settings → ApogeoAPI → paste your API key.

== Frequently Asked Questions ==

= Is this free? =

The plugin is free and open source (MIT). It calls the ApogeoAPI service, which has a free tier of 1,000 requests / month forever.

= How do I get an API key? =

Sign up at [apogeoapi.com](https://apogeoapi.com). No credit card required.

= Does this work behind Cloudflare / a reverse proxy? =

Yes. The visitor-IP shortcode reads from `HTTP_CF_CONNECTING_IP` and `HTTP_X_FORWARDED_FOR` before falling back to `REMOTE_ADDR`.

= Can I use this on a multisite install? =

Yes. Each site stores its own API key.

== Changelog ==

= 0.1.0 =
* Initial release.
* 4 shortcodes: country_selector, country, exchange_rate, visitor_country.
* Settings page under Settings → ApogeoAPI.
* Server-side response caching via WP transients.

== Upgrade Notice ==

= 0.1.0 =
First release.
