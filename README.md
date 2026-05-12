# wp-apogeo — WordPress plugin for ApogeoAPI

WordPress plugin exposing ApogeoAPI's geographic data, IP geolocation, and live exchange rates as shortcodes.

> **Status: 0.1.0 — initial scaffold.** Functional with happy-path testing. Not yet submitted to the WordPress.org plugin directory.

## What's included in this scaffold

```
packages/wp-apogeo/
├── apogeoapi.php                            ← main plugin bootstrap
├── readme.txt                                ← WordPress.org plugin readme format
├── README.md                                 ← this file
└── includes/
    ├── class-apogeoapi-client.php           ← HTTP client wrapper with WP transient caching
    ├── class-apogeoapi-admin.php            ← Settings → ApogeoAPI page
    └── class-apogeoapi-shortcodes.php       ← 4 shortcodes
```

## Shortcodes implemented

| Shortcode | What it does |
|-----------|--------------|
| `[apogeo_country_selector name="country" default="AR"]` | `<select>` with 250+ countries, flag emoji + name, optional default |
| `[apogeo_country iso="AR"]` | Inline country fact block (flag, capital, region, currency + live FX rate, population) |
| `[apogeo_exchange_rate currency="EUR"]` | "1 USD = X EUR" with last-updated timestamp |
| `[apogeo_visitor_country fallback="your country"]` | Detects visitor country from IP, shows flag + name |

## Local dev / testing

1. Symlink or copy `packages/wp-apogeo/` to a WP install at `wp-content/plugins/wp-apogeo/`.
2. WP admin → Plugins → Activate "ApogeoAPI — Country Selector & Geo Data".
3. Settings → ApogeoAPI → paste API key from apogeoapi.com.
4. Create a draft page with `[apogeo_country iso="AR"]` and preview.

## What's NOT done yet (next iteration)

- [ ] **PHPUnit tests** for `ApogeoAPI_Client::get()` happy + error paths
- [ ] **WP-CLI command** `wp apogeoapi flush-cache` to clear all transients
- [ ] **REST API endpoint** so JS components on the front-end can fetch country lists without re-hitting the upstream API on every pageview
- [ ] **Block editor (Gutenberg) blocks** as alternatives to shortcodes
- [ ] **Optional Cloudflare Workers KV cache** for high-traffic sites
- [ ] **Submission to WordPress.org plugin directory** (requires SVN check-in, screenshots, banner image)
- [ ] **Translation files** for ES, FR, DE, PT-BR

## Distribution roadmap (matches GROWTH_ROADMAP.md INT-1)

1. Test locally on a WP 6.5 install with a real ApogeoAPI key.
2. Push to a public GitHub repo: `github.com/APOGEOAPI/wp-apogeo`.
3. Submit to WordPress.org plugin directory (review takes 2-4 weeks).
4. Once approved, list on alternativeto.net and on the ApogeoAPI website's "Integrations" page.
5. Crosspost a "Add live exchange rates to WooCommerce in 2 minutes" tutorial to the WP-focused subreddits and WP Tavern.

## License

MIT
