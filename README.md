# Runa Mod Workshop Suite

Custom administration helpers that streamline managing Workshop Suite content in WordPress. The plugin bundles quality-of-life improvements for Pods-powered post types such as custom menu routing and tailored list-table columns.

## Features

- Dedicated “Workshop Suite” top-level menu that routes directly to Pods-managed Codes, Kurse, Lektionen, Module und Zielgruppen.
- Cleanup hook that removes the redundant Pods defaults to keep the admin menu tidy.
- Custom list-table column for the `rna_wss_code` Pod that surfaces each Code’s `rna_wss_codes_quota` value at a glance.

## Requirements

- WordPress 6.4+ (tested locally with LocalWP).
- PHP 8.1+.
- The Pods Framework plugin with a Pod named `rna_wss_code` and a field `rna_wss_codes_quota`.

## Installation

1. Copy the plugin directory into `wp-content/plugins/runa-mod-workshop-suite`.
2. Ensure the namespace-based autoloader remains intact (`src/` mirrors the class namespace).
3. Activate the plugin in `WP Admin → Plugins`.
4. Confirm that the Pods-managed Codes page (`admin.php?page=pods-manage-rna_wss_code`) shows the new “Kontingent” column.

## Development

```bash
# Run inside the plugin directory
wp plugin deactivate runa-mod-workshop-suite
wp plugin activate runa-mod-workshop-suite
```

- The plugin has no build step; edit PHP files directly.
- For new admin behaviors, add classes under `src/Admin` and register them from `src/Plugin.php`.
- Keep translations under the `runa-mod-workshop-suite` text domain.

## Changelog

See [`CHANGELOG.md`](CHANGELOG.md) for release notes.
